/**
 * Screenshot rendered invoice QA HTML files to PNG + PDF.
 *
 * Usage: node tools/screenshot-invoice.mjs
 * Input:  storage/app/invoice-qa/{variant}.html
 * Output: storage/app/invoice-qa/{variant}.png + {variant}.pdf
 *
 * PENTING: PNG harus dibuat pada geometri yang SAMA dengan PDF asli.
 * PDF asli (Browsershot, A4 + margin 8/10mm) punya content width 718px
 * (190mm), bukan 794px. Kalau PNG di-screenshot pada 794px, header kanan
 * nempel ke tepi dan title terlihat terpotong — itu artefak tool, bukan
 * cacat di template. Jadi di sini viewport = 794px (A4 penuh) dan
 * @page margin diterapkan lewat CSS print, persis seperti Browsershot.
 */
import puppeteer from 'puppeteer';
import fs from 'node:fs';
import path from 'node:path';
import { pathToFileURL } from 'node:url';

const dir = path.resolve('storage/app/invoice-qa');
const files = fs.readdirSync(dir).filter((f) => f.endsWith('.html')).sort();

// A4 pada 96dpi = 794 x 1123 px. Margin PDF: 8mm atas/bawah, 10mm kiri/kanan.
const A4 = { width: 794, height: 1123 };
const MARGIN = { top: '8mm', right: '10mm', bottom: '8mm', left: '10mm' };

const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox'] });
try {
    for (const file of files) {
        const name = file.replace(/\.html$/, '');
        const page = await browser.newPage();
        await page.setViewport({ width: A4.width, height: A4.height, deviceScaleFactor: 2 });
        await page.goto(pathToFileURL(path.join(dir, file)).href, { waitUntil: 'networkidle0', timeout: 30000 }).catch(() => {});
        await new Promise((r) => setTimeout(r, 500));

        // PDF: Chrome+satu halaman A4 dengan margin yang sama seperti
        // InvoiceController::invoicePdf() ->margins(8, 10, 8, 10).
        await page.pdf({
            path: path.join(dir, name + '.pdf'),
            format: 'A4',
            printBackground: true,
            margin: MARGIN,
            preferCSSPageSize: false,
        });

        // PNG: emulate media print supaya @page margin ikut berlaku, lalu
        // screenshot area isi (A4 dikurangi margin) supaya pixel-nya
        // sebanding dengan PDF.
        await page.emulateMediaType('print');
        await page.addStyleTag({
            content: `
                @page { size: A4 portrait; margin: 8mm 10mm 8mm; }
                html, body { margin: 0 !important; padding: 0 !important; width: 190mm !important; }
            `,
        });
        await new Promise((r) => setTimeout(r, 300));

        const content = await page.evaluate(() => {
            const doc = document.querySelector('.invoice-doc');
            if (!doc) return null;
            const r = doc.getBoundingClientRect();
            return { x: r.x, y: r.y, width: r.width, height: r.height };
        });

        if (content) {
            await page.screenshot({
                path: path.join(dir, name + '.png'),
                clip: {
                    x: content.x,
                    y: content.y,
                    width: content.width,
                    height: Math.min(content.height, A4.height - content.y),
                },
            });
        }

        await page.close();
        console.log('shot', name);
    }
} finally {
    await browser.close();
}
