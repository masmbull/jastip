/**
 * Rasterize official bank SVG logos to transparent PNGs.
 *
 * Sources (see public/img/invoice/banks/):
 *  - seabank.svg  — official SeaBank logo (paths only, from seabank.co.id
 *                   via Wikimedia Commons). Rasterized here because SVG as
 *                   <img> keeps vector quality but the DomPDF fallback and
 *                   older renderers handle PNG more predictably; also the
 *                   official BCA/blu assets are distributed as PNG only.
 *  - bca.png      — official BCA logo (GRUP BCA), downloaded from Wikimedia
 *                   Commons (source: bca.co.id brand assets). Used as-is.
 *  - blubca.png   — official blu logo, downloaded from blubybcadigital.id
 *                   (_nuxt/logo-color). Used as-is.
 *
 * Usage: node tools/rasterize-bank-logos.mjs
 */
import puppeteer from 'puppeteer';
import fs from 'node:fs';
import path from 'node:path';

const dir = path.resolve('public/img/invoice/banks');
const jobs = [
    // SeaBank official SVG viewBox 252x81 — render at ~5x for print sharpness.
    { file: 'seabank.svg', out: 'seabank.png', w: 252, h: 81, scale: 5 },
];

const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox'] });
try {
    for (const job of jobs) {
        const svg = fs.readFileSync(path.join(dir, job.file), 'utf8');
        const page = await browser.newPage();
        await page.setViewport({ width: job.w * job.scale, height: job.h * job.scale, deviceScaleFactor: 1 });
        await page.setContent(
            `<!doctype html><html><head><style>html,body{margin:0;padding:0;background:transparent}svg{display:block;width:${job.w * job.scale}px;height:${job.h * job.scale}px}</style></head><body>${svg}</body></html>`,
            { waitUntil: 'load' },
        );
        await page.screenshot({ path: path.join(dir, job.out), omitBackground: true });
        await page.close();
        console.log('rasterized', job.out);
    }
} finally {
    await browser.close();
}
