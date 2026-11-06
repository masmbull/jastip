/**
 * Screenshot rendered invoice QA HTML files to PNG + PDF.
 * Usage: node tools/screenshot-invoice.mjs
 * Input:  storage/app/invoice-qa/{variant}.html
 * Output: storage/app/invoice-qa/{variant}.png + {variant}.pdf
 */
import puppeteer from 'puppeteer';
import fs from 'node:fs';
import path from 'node:path';
import { pathToFileURL } from 'node:url';

const dir = path.resolve('storage/app/invoice-qa');
const files = fs.readdirSync(dir).filter((f) => f.endsWith('.html')).sort();

const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox'] });
try {
    for (const file of files) {
        const name = file.replace(/\.html$/, '');
        const page = await browser.newPage();
        await page.setViewport({ width: 794, height: 1123, deviceScaleFactor: 2 });
        await page.goto(pathToFileURL(path.join(dir, file)).href, { waitUntil: 'networkidle0', timeout: 30000 }).catch(() => {});
        await new Promise((r) => setTimeout(r, 500));
        await page.screenshot({ path: path.join(dir, name + '.png'), fullPage: false });
        await page.pdf({
            path: path.join(dir, name + '.pdf'),
            format: 'A4',
            printBackground: true,
            margin: { top: '8mm', right: '10mm', bottom: '8mm', left: '10mm' },
            preferCSSPageSize: false,
        });
        await page.close();
        console.log('shot', name);
    }
} finally {
    await browser.close();
}
