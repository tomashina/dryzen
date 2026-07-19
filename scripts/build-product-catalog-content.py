from pathlib import Path
from zipfile import ZipFile
import json
import shutil

from docx import Document


PROJECT_ROOT = Path(__file__).resolve().parents[1]
DOCX_PATH = PROJECT_ROOT / "Web 17.07.26..docx"
ZIP_PATH = PROJECT_ROOT / "Za Web-20260718T182134Z-1-001.zip"
MATCHES_PATH = PROJECT_ROOT / "tmp/docx-review.DbnQdt/image-matches-pixel.json"
OUTPUT_PATH = PROJECT_ROOT / "database/content/hr/product-catalog.json"
SOURCE_DIR = PROJECT_ROOT / "tmp/product-catalog-sources"

PRODUCTS = [
    {
        "title": "DryZen maramice za ruke za muškarce",
        "product_id": 9,
        "model": "002",
        "slug": "hands-men",
        "media": ["image66.jpg", "image73.jpg", "image14.png", "image39.jpg"],
    },
    {
        "title": "DryZen maramice za ruke Sport",
        "product_id": 10,
        "model": "003",
        "slug": "hands-sport",
        "media": ["image17.jpg", "image23.jpg", "image87.jpg", "image84.jpg", "image27.jpg"],
    },
    {
        "title": "DryZen maramice za ruke za žene",
        "product_id": 6,
        "model": "001",
        "slug": "hands-women",
        "media": ["image64.jpg", "image88.jpg", "image56.jpg", "image46.jpg", "image62.jpg"],
    },
    {
        "title": "DryZen maramice za stopala za djecu",
        "product_id": 15,
        "model": "008",
        "slug": "feet-kids",
        "media": ["image47.jpg", "image32.jpg", "image15.jpg", "image52.jpg", "image79.jpg"],
    },
    {
        "title": "DryZen maramice za stopala za muškarce",
        "product_id": 13,
        "model": "006",
        "slug": "feet-men",
        "media": ["image80.jpg", "image48.jpg", "image14.png", "image39.jpg"],
    },
    {
        "title": "DryZen maramice za stopala za sport",
        "product_id": 14,
        "model": "007",
        "slug": "feet-sport",
        "media": ["image85.jpg", "image5.jpg", "image41.jpg", "image50.jpg", "image27.jpg"],
    },
    {
        "title": "DryZen maramice za stopala za žene",
        "product_id": 12,
        "model": "005",
        "slug": "feet-women",
        "media": ["image55.jpg", "image53.jpg", "image2.jpg", "image46.jpg", "image62.jpg"],
    },
    {
        "title": "DryZen Kids roll-on",
        "product_id": 19,
        "model": "012",
        "slug": "rollon-kids",
        "media": ["image21.jpg", "image68.jpg", "image13.jpg", "image74.jpg", "image51.jpg", "image79.jpg"],
    },
    {
        "title": "DryZen roll-on za muškarce",
        "product_id": 17,
        "model": "010",
        "slug": "rollon-men",
        "media": ["image6.jpg", "image22.jpg", "image67.jpg", "image14.png", "image39.jpg"],
    },
    {
        "title": "DryZen roll-on Sport",
        "product_id": 18,
        "model": "011",
        "slug": "rollon-sport",
        "media": ["image8.jpg", "image65.jpg", "image71.jpg", "image75.jpg", "image86.jpg", "image27.jpg"],
    },
    {
        "title": "DryZen roll-on za žene",
        "product_id": 16,
        "model": "009",
        "slug": "rollon-women",
        "media": ["image37.jpg", "image12.jpg", "image19.jpg", "image59.jpg", "image46.jpg", "image62.jpg"],
    },
    {
        "title": "DryZen sprej za obuću",
        "product_id": 20,
        "model": "013",
        "slug": "shoe-spray",
        "media": ["image58.jpg", "image20.jpg", "image69.jpg", "image35.jpg", "image63.jpg"],
    },
]

SUBSECTION_LABELS = {
    "Početna rutina",
    "Odgovorna osoba",
    "Odgovorna osoba:",
}


def paragraph_numbering(paragraph):
    properties = paragraph._p.pPr
    if properties is None or properties.numPr is None or properties.numPr.numId is None:
        return None
    return int(properties.numPr.numId.val)


def add_text(container, key, text):
    container.setdefault(key, []).append(text)


def meta_summary(text, limit=255):
    if len(text) <= limit:
        return text

    summary = text[: limit - 1].rsplit(" ", 1)[0].rstrip(" ,;:-")
    return summary + "…"


def parse_product(paragraphs, definition, next_title):
    start = next(
        index
        for index, paragraph in enumerate(paragraphs)
        if paragraph.text.strip() == definition["title"] and paragraph.style.name == "Heading 1"
    )
    end = len(paragraphs)
    if next_title:
        end = next(
            index
            for index, paragraph in enumerate(paragraphs[start + 1 :], start + 1)
            if paragraph.text.strip() == next_title and paragraph.style.name == "Heading 1"
        )

    section = [paragraph for paragraph in paragraphs[start:end] if paragraph.text.strip()]
    name = section[0].text.strip()
    subtitle = section[1].text.strip()
    body = section[2:]

    if body and "PDV uključen" in body[0].text:
        body = body[1:]

    intro = []
    tabs = []
    current_tab = None
    current_subsection = None

    for paragraph in body:
        text = paragraph.text.strip()
        style = paragraph.style.name if paragraph.style is not None else ""
        numbered = paragraph_numbering(paragraph) is not None

        if style == "Heading 2":
            current_tab = {"name": text}
            tabs.append(current_tab)
            current_subsection = None
            continue

        if current_tab is None:
            intro.append(text)
            continue

        if style == "Heading 3":
            current_subsection = {"title": text}
            current_tab.setdefault("subsections", []).append(current_subsection)
            continue

        if text in SUBSECTION_LABELS:
            current_subsection = {"title": text.rstrip(":")}
            current_tab.setdefault("subsections", []).append(current_subsection)
            continue

        if text.startswith("Odgovorna osoba:") and "\n" in text:
            _, details = text.split(":", 1)
            current_subsection = {"title": "Odgovorna osoba"}
            current_tab.setdefault("subsections", []).append(current_subsection)
            details = details.strip()
            if details:
                add_text(current_subsection, "paragraphs", details)
            continue

        target = current_subsection if current_subsection is not None else current_tab

        if numbered:
            if current_subsection is None and current_tab["name"] == "Kako koristiti?":
                add_text(target, "ordered_list", text)
            else:
                add_text(target, "list", text)
        else:
            add_text(target, "paragraphs", text)

    if len(intro) < 2:
        raise RuntimeError(f"Expected two intro paragraphs for {name}, found {len(intro)}")

    return {
        "product_id": definition["product_id"],
        "expected_model": definition["model"],
        "slug": definition["slug"],
        "name": name,
        "subtitle": subtitle,
        "meta_title": f"{name} | DryZen",
        "meta_description": meta_summary(intro[1]),
        "intro": intro,
        "tabs": tabs,
    }


def build_images(definition, matches):
    images = []
    sources = []

    for index, media_name in enumerate(definition["media"], 1):
        match = matches[media_name]["candidates"][0]
        if match["score"] > 30:
            raise RuntimeError(f"Low-confidence image match for {media_name}: {match}")
        filename = f"{definition['slug']}-{index:02d}.webp"
        images.append(f"catalog/product-catalog-2026/{definition['slug']}/{filename}")
        sources.append(
            {
                "media": media_name,
                "archive": match["source"],
                "score": match["score"],
                "target": filename,
            }
        )

    return {
        "main": images[0],
        "additional": images[1:],
        "sources": sources,
    }


if not MATCHES_PATH.is_file():
    raise SystemExit(f"Missing image match inventory: {MATCHES_PATH}")

document = Document(DOCX_PATH)
matches = json.loads(MATCHES_PATH.read_text(encoding="utf-8"))
catalog = []

for index, definition in enumerate(PRODUCTS):
    next_title = PRODUCTS[index + 1]["title"] if index + 1 < len(PRODUCTS) else None
    product = parse_product(document.paragraphs, definition, next_title)
    product["images"] = build_images(definition, matches)
    catalog.append(product)

OUTPUT_PATH.write_text(json.dumps(catalog, ensure_ascii=False, indent=2), encoding="utf-8")

if SOURCE_DIR.exists():
    shutil.rmtree(SOURCE_DIR)
SOURCE_DIR.mkdir(parents=True)

with ZipFile(ZIP_PATH) as archive:
    for product in catalog:
        product_source_dir = SOURCE_DIR / product["slug"]
        product_source_dir.mkdir()
        for source in product["images"]["sources"]:
            destination = product_source_dir / source["target"]
            destination.write_bytes(archive.read(source["archive"]))

print(f"Products: {len(catalog)}")
print(f"Content: {OUTPUT_PATH}")
print(f"Sources: {SOURCE_DIR}")
