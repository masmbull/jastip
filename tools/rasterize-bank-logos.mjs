/**
 * Rasterize the official bank SVG logos to transparent PNGs.
 *
 * Why PNG: the SVGs use "Arial Black" text. In headless Chrome on a Linux
 * server that font is missing, so the fallback metrics make the wordmarks
 * overflow their viewBox and get clipped (e.g. "SeaBank" -> "SeaB").
 * Baking the vector to PNG on a machine with Arial Black (Windows)
 * freezes the correct shapes for every renderer.
 *
 * Usage: node tools/rasterize-bank-logos.mjs
 */
import puppeteer from 'puppeteer';
import fs from 'node:fs';
import path from 'node:path';

const dir = path.resolve('public/img/invoice/banks');
const jobs = [
    { file: 'bca.svg', out: 'bca.png', w: 520, h: 200 },
    { file: 'seabank.svg', out: 'seabank.png', w: 480, h: 160 },
    { file: 'blubca.svg', out: 'blubca.png', w: 400, h: 300 },
];
const SCALE = 3;

const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox'] });
try {
    for (const job of jobs) {
        const svg = fs.readFileSync(path.join(dir, job.file), 'utf8');
        const page = await browser.newPage();
        await page.setViewport({ width: job.w * SCALE, height: job.h * SCALE, deviceScaleFactor: 1 });
        await page.setContent(
            `<!doctype html><html><head><style>html,body{margin:0;padding:0;background:transparent}svg{display:block;width:${job.w * SCALE}px;height:${job.h * SCALE}px}</style></head><body>${svg}</body></html>`,
            { waitUntil: 'load' },
        );
        await page.screenshot({ path: path.join(dir, job.out), omitBackground: true });
        await page.close();
        console.log('rasterized', job.out);
    }
} finally {
    await browser.close();
}
