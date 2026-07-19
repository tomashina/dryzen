const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

const projectRoot = path.resolve(__dirname, '..');
const input = path.join(projectRoot, 'upload/image/catalog/about-2026/dryzen-team.jpg');
const outputDirectory = path.join(projectRoot, 'upload/image/catalog/seo');
const output = path.join(outputDirectory, 'dryzen-social-1200x630.webp');
const aboutDirectory = path.join(projectRoot, 'upload/image/catalog/about-2026');
const aboutImages = [
  ['dryzen-team.jpg', 'dryzen-team.webp'],
  ['dryzen-story.jpg', 'dryzen-story.webp'],
];
const landingDirectory = path.join(projectRoot, 'upload/image/catalog/landing-2026');
const landingImages = [
  'hero',
  'need-armpits',
  'need-feet',
  'need-hands',
  'need-shoes',
  'product-finder',
  'routine',
];
const paymentDirectory = path.join(projectRoot, 'upload/image/catalog/credit-cards');
const paymentImages = [
  ['visa.jpg', 'visa.webp'],
  ['mastercard-identity-check.png', 'mastercard-identity-check.webp'],
  ['visa-secure.jpg', 'visa-secure.webp'],
];

(async () => {
  fs.mkdirSync(outputDirectory, { recursive: true });

  await sharp(input)
    .rotate()
    .resize({
      width: 1200,
      height: 630,
      fit: 'cover',
      position: 'attention',
    })
    .webp({
      quality: 92,
      alphaQuality: 100,
      smartSubsample: true,
      effort: 6,
    })
    .toFile(output);

  for (const [sourceName, outputName] of aboutImages) {
    const sourcePath = path.join(aboutDirectory, sourceName);
    const outputPath = path.join(aboutDirectory, outputName);

    await sharp(sourcePath)
      .rotate()
      .webp({
        quality: 92,
        alphaQuality: 100,
        smartSubsample: true,
        effort: 6,
      })
      .toFile(outputPath);

    process.stdout.write(`Created ${path.relative(projectRoot, outputPath)}\n`);
  }

  for (const imageName of landingImages) {
    const sourcePath = path.join(landingDirectory, `${imageName}.jpg`);
    const targets = [
      [`${imageName}-480.webp`, 480],
      [`${imageName}-800.webp`, 800],
      [`${imageName}-1400.webp`, 1400],
      [`${imageName}.webp`, null],
    ];

    for (const [outputName, width] of targets) {
      let pipeline = sharp(sourcePath).rotate();

      if (width) {
        pipeline = pipeline.resize({
          width,
          withoutEnlargement: true,
        });
      }

      const outputPath = path.join(landingDirectory, outputName);
      await pipeline
        .webp({
          quality: 90,
          alphaQuality: 100,
          smartSubsample: true,
          effort: 6,
        })
        .toFile(outputPath);

      process.stdout.write(`Created ${path.relative(projectRoot, outputPath)}\n`);
    }
  }

  for (const [sourceName, outputName] of paymentImages) {
    const sourcePath = path.join(paymentDirectory, sourceName);
    const outputPath = path.join(paymentDirectory, outputName);

    await sharp(sourcePath)
      .rotate()
      .webp({
        quality: 92,
        alphaQuality: 100,
        smartSubsample: true,
        effort: 6,
      })
      .toFile(outputPath);

    process.stdout.write(`Created ${path.relative(projectRoot, outputPath)}\n`);
  }

  process.stdout.write(`Created ${path.relative(projectRoot, output)}\n`);
})().catch((error) => {
  process.stderr.write(`${error.stack}\n`);
  process.exit(1);
});
