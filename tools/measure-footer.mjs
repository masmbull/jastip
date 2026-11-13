/**
 * Measure the footer + last accent bar positions at the real PDF content width
 * to find why an element spills to page 2.
 *
 * Usage: node tools/measure-footer.mjs
 */
import puppeteer from 'puppeteer';
import path from 'node:path';
import { pathToFileURL } from 'node:url';

const dir = path.resolve('storage/app/invoice-qa');
const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox'] });
try {
    for (const file of ['heavy.html']) {
        const page = await browser.newPage();
        await page.setViewport({ width: 718, height: 1123, deviceScaleFactor: 1 });
        await page.goto(pathToFileURL(path.join(dir, file)).href, { waitUntil: 'networkidle0', timeout: 30000 }).catch(() => {});
        await new Promise((r) => setTimeout(r, 300));

        const report = await page.evaluate(() => {
            const r = (el) => { const b = el.getBoundingClientRect(); return { top: +b.top.toFixed(1), bottom: +b.bottom.toFixed(1), height: +b.height.toFixed(1), display: getComputedStyle(el).display }; };
            const doc = document.querySelector('.invoice-doc');
            const body = document.querySelector('.invoice-body');
            const footer = document.querySelector('.invoice-footer');
            const footerDivider = footer ? footer.querySelector('div') : null;
            const bar = footer ? footer.lastElementChild : null;
            const bodyEnd = body ? r(body).bottom : 0;
            return {
                docHeight: +doc.getBoundingClientRect().height.toFixed(1),
                bodyRect: r(body),
                footerRect: footer ? r(footer) : null,
                footerDivider: footerDivider ? r(footerDivider) : null,
                bar: bar ? r(bar) : null,
                docScrollHeight: doc.scrollHeight,
                docClientHeight: doc.clientHeight,
                bodyFlex: getComputedStyle(body).flex,
                bodyFlexShrink: getComputedStyle(body).flexShrink,
                bodyFlexBasis: getComputedStyle(body).flexBasis,
                footerFlexShrink: getComputedStyle(footer).flexShrink,
            };
        });
        console.log(file, JSON.stringify(report, null, 2));
        await page.close();
    }
} finally {
    await browser.close();
}
