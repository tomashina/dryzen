const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

const projectRoot = path.resolve(__dirname, '..');
const catalogPath = path.join(projectRoot, 'database/content/hr/product-catalog.json');
const sourceRoot = path.join(projectRoot, 'tmp/product-catalog-sources');
const outputRoot = path.join(projectRoot, 'upload/image/catalog/product-catalog-2026');
const catalog = JSON.parse(fs.readFileSync(catalogPath, 'utf8'));
const kidsHandsRoot = path.join(projectRoot, 'upload/image/catalog/product-kids-hands-2026');
const kidsHandsImages = [
  'dryzen-kids-hand-wipes-pack',
  'dryzen-kids-school',
  'dryzen-kids-pack',
  'dryzen-kids-family',
  'dryzen-kids-routine',
];

(async () => {
  let count = 0;

  for (const product of catalog) {
    const productOutput = path.join(outputRoot, product.slug);
    fs.mkdirSync(productOutput, { recursive: true });

    for (const source of product.images.sources) {
      const input = path.join(sourceRoot, product.slug, source.target);
      const output = path.join(productOutput, source.target);

      await sharp(input)
        .rotate()
        .resize({
          width: 4800,
          height: 4800,
          fit: 'inside',
          withoutEnlargement: true,
        })
        .flatten({ background: '#f2efed' })
        .webp({
          quality: 98,
          alphaQuality: 100,
          smartSubsample: true,
          effort: 6,
        })
        .toFile(output);

      count += 1;
    }
  }

  for (const basename of kidsHandsImages) {
    await sharp(path.join(kidsHandsRoot, `${basename}.jpg`))
      .rotate()
      .flatten({ background: '#f2efed' })
      .webp({
        quality: 98,
        alphaQuality: 100,
        smartSubsample: true,
        effort: 6,
      })
      .toFile(path.join(kidsHandsRoot, `${basename}.webp`));
    count += 1;
  }

  process.stdout.write(`Optimized product images: ${count}\n`);
})().catch((error) => {
  process.stderr.write(`${error.stack}\n`);
  process.exit(1);
});
