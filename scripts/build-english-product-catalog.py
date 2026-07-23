from pathlib import Path
from copy import deepcopy
import json
import re

from docx import Document


PROJECT_ROOT = Path(__file__).resolve().parents[1]
DOCX_PATH = Path("/Users/tomek/Downloads/DryZen web - engleski.docx")
HR_CATALOG_PATH = PROJECT_ROOT / "database/content/hr/product-catalog.json"
OUTPUT_PATH = PROJECT_ROOT / "database/content/en/product-catalog.json"

PRODUCTS = [
    ("DryZen Kids Anti-Perspirant Hand Wipes", 11),
    ("DryZen Men Antiperspirant Hand Wipes", 9),
    ("DryZen Sport Antiperspirant Hand Wipes", 10),
    ("DryZen Women Antiperspirant Hand Wipes", 6),
    ("DryZen Kids Antiperspirant Foot Wipes", 15),
    ("DryZen Men Antiperspirant Foot Wipes", 13),
    ("DryZen Sport Antiperspirant Foot Wipes", 14),
    ("DryZen Women Antiperspirant Foot Wipes", 12),
    ("DryZen Kids Antiperspirant Roll-On", 19),
    ("DryZen Men Antiperspirant Roll-On", 17),
    ("DryZen Sport Antiperspirant Roll-On", 18),
    ("DryZen Women Antiperspirant Roll-On", 16),
    ("DryZen Shoe Spray", 20),
]

TOP_LEVEL_SECTIONS = (
    ("when should you choose", "When to Choose"),
    ("why choose", "Why Choose"),
    ("how to use", "How to Use"),
    ("what's included", "What's Included?"),
    ("what’s included", "What's Included?"),
    ("ingredients (inci)", "Ingredients (INCI)"),
    ("ingridients (inci)", "Ingredients (INCI)"),
    ("warnings", "Warnings"),
    ("additional information", "Additional Information"),
)

SUBSECTIONS = {
    "initial routine": "Initial Routine",
    "for best results": "For Best Results",
    "after the initial routine": "After the Initial Routine",
}

WIPES_VALUE_PROPOSITION = (
    "One pack = one complete initial treatment (3 consecutive evenings), "
    "not just three individual uses."
)
ROLL_ON_VALUE_PROPOSITION = (
    "Small pack. Long-lasting protection. One 10 ml pack is enough for "
    "up to 6 months of use."
)
SHOE_SPRAY_VALUE_PROPOSITION = (
    "One bottle. Months without compromise. How many times have you bought "
    "a cheaper spray that ended up in the bin after just a few weeks?"
)


def paragraph_numbering(paragraph):
    properties = paragraph._p.pPr
    if properties is None or properties.numPr is None or properties.numPr.numId is None:
        return None
    return int(properties.numPr.numId.val)


def normalize_heading(text):
    return re.sub(r"\s+", " ", text.strip()).rstrip("?").lower()


def meta_summary(text, limit=255):
    if len(text) <= limit:
        return text
    summary = text[: limit - 1].rsplit(" ", 1)[0].rstrip(" ,;:-")
    return summary + "…"


def canonical_top_level(text):
    normalized = normalize_heading(text)
    for prefix, canonical in TOP_LEVEL_SECTIONS:
        if normalized.startswith(prefix):
            if canonical == "When to Choose":
                return text.strip()
            if canonical == "Why Choose":
                return text.strip().replace("Why choose", "Why Choose")
            return canonical
    return None


def add_value(container, key, text):
    container.setdefault(key, []).append(text)


def parse_product(paragraphs, title, product_id, next_title):
    start = next(
        index for index, paragraph in enumerate(paragraphs)
        if paragraph.text.strip() == title
    )
    end = len(paragraphs)
    if next_title:
        end = next(
            index for index, paragraph in enumerate(paragraphs[start + 1 :], start + 1)
            if paragraph.text.strip() == next_title
        )

    section = [paragraph for paragraph in paragraphs[start:end] if paragraph.text.strip()]
    name = section[0].text.strip()
    subtitle = section[1].text.strip()
    body = section[2:]

    if body and "VAT included" in body[0].text:
        body = body[1:]

    first_tab = next(
        index for index, paragraph in enumerate(body)
        if canonical_top_level(paragraph.text.strip()) is not None
    )
    lead = body[:first_tab]
    body = body[first_tab:]
    intro = [paragraph.text.strip() for paragraph in lead[:2]]
    description = None

    if product_id == 11:
        description = {
            "title": lead[2].text.strip(),
            "paragraphs": [paragraph.text.strip() for paragraph in lead[3:]],
        }
        description["paragraphs"][0] = description["paragraphs"][0].replace(
            "aged 1016", "aged 10–16"
        )

    tabs = []
    current_tab = None
    current_subsection = None

    for paragraph in body:
        text = paragraph.text.strip()
        normalized = normalize_heading(text)
        top_level = canonical_top_level(text)

        if top_level is not None:
            current_tab = {"name": top_level}
            tabs.append(current_tab)
            current_subsection = None
            continue

        if normalized in SUBSECTIONS:
            current_subsection = {"title": SUBSECTIONS[normalized]}
            current_tab.setdefault("subsections", []).append(current_subsection)
            continue

        if normalized in {"responsible person", "odgovorna osoba"}:
            current_subsection = {"title": "Responsible Person"}
            current_tab.setdefault("subsections", []).append(current_subsection)
            continue

        if normalized.startswith("responsible:"):
            current_subsection = {"title": "Responsible Person"}
            current_tab.setdefault("subsections", []).append(current_subsection)
            details = text.split(":", 1)[1].strip()
            if details:
                add_value(current_subsection, "paragraphs", details)
            continue

        target = current_subsection if current_subsection is not None else current_tab
        if target is None:
            raise RuntimeError(f"Unassigned content in {name}: {text}")

        if paragraph_numbering(paragraph) is not None:
            if current_subsection is None and current_tab["name"] == "How to Use":
                add_value(target, "ordered_list", text)
            else:
                add_value(target, "list", text)
        else:
            add_value(target, "paragraphs", text)

    if len(intro) != 2:
        raise RuntimeError(f"Expected two intro paragraphs for {name}, found {len(intro)}")
    if len(tabs) != 7:
        raise RuntimeError(f"Expected seven tabs for {name}, found {len(tabs)}")

    product = {
        "product_id": product_id,
        "name": name,
        "subtitle": subtitle,
        "value_proposition": (
            WIPES_VALUE_PROPOSITION
            if product_id in {6, 9, 10, 11, 12, 13, 14, 15}
            else ROLL_ON_VALUE_PROPOSITION
            if product_id in {16, 17, 18, 19}
            else SHOE_SPRAY_VALUE_PROPOSITION
        ),
        "meta_title": f"{name} | DryZen",
        "meta_description": meta_summary(intro[0] if product_id == 11 else intro[1]),
        "intro": intro,
        "tabs": tabs,
    }
    if description:
        product["description"] = description
    return product


hr_catalog = json.loads(HR_CATALOG_PATH.read_text(encoding="utf-8"))
images_by_product = {
    int(product["product_id"]): deepcopy(product["images"]) for product in hr_catalog
}

# The Kids hand-wipes images are defined in PHP. Keep the established paths
# explicit here so the generated English catalog remains deterministic.
images_by_product[11] = {
    "main": "catalog/product-kids-hands-2026/dryzen-kids-hand-wipes-pack.webp",
    "additional": [
        "catalog/product-kids-hands-2026/dryzen-kids-school.webp",
        "catalog/product-kids-hands-2026/dryzen-kids-pack.webp",
        "catalog/product-kids-hands-2026/dryzen-kids-family.webp",
        "catalog/product-kids-hands-2026/dryzen-kids-routine.webp",
    ],
}

document = Document(DOCX_PATH)
catalog = []

for index, (title, product_id) in enumerate(PRODUCTS):
    next_title = PRODUCTS[index + 1][0] if index + 1 < len(PRODUCTS) else None
    product = parse_product(document.paragraphs, title, product_id, next_title)
    product["images"] = images_by_product[product_id]
    catalog.append(product)

OUTPUT_PATH.parent.mkdir(parents=True, exist_ok=True)
OUTPUT_PATH.write_text(
    json.dumps(catalog, ensure_ascii=False, indent=2) + "\n",
    encoding="utf-8",
)

print(f"Products: {len(catalog)}")
print(f"Content: {OUTPUT_PATH}")
