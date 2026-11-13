<?php

/**
 * Browsershot configuration.
 *
 * Browsershot uses Puppeteer (Node.js) to render PDFs via headless Chrome.
 * On Windows, node/npm paths must be explicit because PHP's exec() does not
 * inherit the same PATH as the interactive shell.
 *
 * How to find your paths:
 *   node --version          → to confirm node is in PATH
 *   where.exe node          → full path on Windows
 *   where.exe npm           → full path on Windows
 *
 * Chrome executable path is bundled by Puppeteer at:
 *   node_modules\puppeteer\.local-chromium\...\chrome.exe  (old puppeteer)
 *   node_modules\puppeteer\.local-chrome\...\chrome.exe    (puppeteer v20+)
 *
 * Run this once to find it:
 *   node -e "const p = require('puppeteer'); console.log(p.executablePath())"
 * (run from the project directory after npm install)
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Node binary path
    |--------------------------------------------------------------------------
    | Set to null to auto-detect from PATH (works on Linux/Mac).
    | On Windows you usually need the full path.
    |
    | Example: 'C:\Program Files\nodejs\node.exe'
    | Or set in .env: BROWSERSHOT_NODE_PATH=C:\Program Files\nodejs\node.exe
    */
    'node_binary' => env('BROWSERSHOT_NODE_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | npm binary path
    |--------------------------------------------------------------------------
    | Example: 'C:\Program Files\nodejs\npm.cmd'
    | Or set in .env: BROWSERSHOT_NPM_PATH=C:\Program Files\nodejs\npm.cmd
    */
    'npm_binary' => env('BROWSERSHOT_NPM_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | Chrome / Chromium executable path
    |--------------------------------------------------------------------------
    | Leave null to let Puppeteer find its bundled Chrome automatically.
    | Set this if you get "Could not find browser" errors.
    |
    | Example: 'C:\Users\Mbul\AppData\Local\ms-playwright\chromium-...\chrome.exe'
    | Or set in .env: BROWSERSHOT_CHROME_PATH=...
    */
    'chrome_path' => env('BROWSERSHOT_CHROME_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | Global node_modules path
    |--------------------------------------------------------------------------
    | Browsershot menjalankan `node vendor/.../browser.cjs` dan file itu
    | melakukan `require('puppeteer')`. Node mencari modul relatif ke lokasi
    | file, jadi `node_modules` project harus ada. Tapi node_modules TIDAK
    | ikut ter-deploy ke server, jadi Puppeteer dipasang global dan path-nya
    | ditunjuk lewat variabel ini.
    |
    | Kosongkan kalau Puppeteer ada di node_modules project (lokal).
    |
    | Contoh: /usr/local/lib/node_modules (NodeSource) atau
    |          /usr/lib/node_modules (paket Debian).
    */
    'node_module_path' => env('BROWSERSHOT_NODE_MODULE_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | Additional Chromium arguments
    |--------------------------------------------------------------------------
    | 'no-sandbox' is required in Docker/CI environments running as root.
    | Do NOT add it in local dev unless you need to.
    */
    'chromium_args' => array_filter(explode(',', env('BROWSERSHOT_CHROME_ARGS', ''))),

];
