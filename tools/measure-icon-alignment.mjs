/**
 * Report vertical center offset between every invoice SVG icon and its
 * adjacent text sibling, in the real Chromium render.
 *
 * Usage: node tools/measure-icon-alignment.mjs
 */
import puppeteer from 'puppeteer';
import path from 'node:path';
import { pathToFileURL } from 'node:url';

const file = path.resolve('storage/app/invoice-qa/base.html');
const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox'] });
try {
    const page = await browser.newPage();
    await page.setViewport({ width: 718, height: 1123, deviceScaleFactor: 1 });
    await page.goto(pathToFileURL(file).href, { waitUntil: 'networkidle0', timeout: 30000 });
    await new Promise((r) => setTimeout(r, 400));

    const rows = await page.evaluate(() => {
        const doc = document.querySelector('.invoice-doc');
        if (!doc) return [];

        const out = [];
        const icons = doc.querySelectorAll('svg');

        icons.forEach((icon) => {
            // Find the nearest text sibling inside the same flex row.
            let row = icon.parentElement;
            while (row && getComputedStyle(row).display !== 'flex') {
                row = row.parentElement;
                if (!row || row === doc) { row = null; break; }
            }
            if (!row) return;

            const iconRect = icon.getBoundingClientRect();
            const iconCenter = (iconRect.top + iconRect.bottom) / 2;

            const texts = Array.from(row.querySelectorAll('span, div'))
                .filter((el) => el !== icon && el.children.length === 0 && el.textContent.trim());

            const text = texts.reduce((best, el) => {
                const w = el.getBoundingClientRect().width;
                return !best || w > best.getBoundingClientRect().width ? el : best;
            }, null);

            if (!text) return;

            const textRect = text.getBoundingClientRect();
            const textCenter = (textRect.top + textRect.bottom) / 2;

            out.push({
                icon: icon.outerHTML.slice(0, 60),
                text: text.textContent.trim().slice(0, 40),
                iconCenter: +iconCenter.toFixed(1),
                textCenter: +textCenter.toFixed(1),
                diff: +(iconCenter - textCenter).toFixed(1),
                iconHeight: +(iconRect.height).toFixed(1),
                textHeight: +(textRect.height).toFixed(1),
            });
        });

        return out;
    });

    rows.forEach((r) => {
        const flag = Math.abs(r.diff) > 1.5 ? '  <-- OFF' : '';
        process.stdout.write(`diff=${r.diff.toFixed(1).padStart(5)}  iconH=${String(r.iconHeight).padStart(3)}  textH=${String(r.textHeight).padStart(4)}  ${JSON.stringify(r.text)}${flag}\n`);
    });
} finally {
    await browser.close();
}
