# JASTIP — NITIP DI END

Minimal Jastip (indonesian "titip beli") storefront: a curated-product catalog with a
cookie-based cart, checkout → order flow, and a minimal admin panel for products,
categories, orders, and settings.

- **Frontend:** Laravel Blade + Tailwind CSS + Alpine.js (no React/Vue).
- **Data:** single `admin`/`customer` role column on `User` (no second auth table).
- **State:** session cart (guests allowed), no payments (status workflow in admin).
- **Stack:** Laravel 11 · PHP 8.2+ · MySQL/SQLite · Vite + TailwindCSS v4.

## Requirements

- PHP **8.2+** (`php -v`)
- Composer ≥ 2
- Node.js ≥ 20 / npm
- MySQL 8+ (or SQLite for local dev)

## Install (local)

```bash
git clone <repo> nitipdiend
cd nitipdiend
composer install
npm install
cp .env.example .env
php artisan key:generate
# edit .env: DB_*, APP_URL, WA_* (WhatsApp number), MAIL_*
php artisan migrate:fresh --seed
php artisan serve
# -> http://127.0.0.1:8000
```

Seeded default admin: `admin@nitipdiend.com` / `admin123`, product `fashion-item`.

## Commands (reference, from `composer.json` `scripts`)

| Command | Purpose |
|---|---|
| `composer install` | install PHP deps |
| `npm install` | install frontend deps |
| `npm run dev` | `vite` (HMR) |
| `npm run build` | `vite build` + `config/route/view` cache |
| `php artisan test` | `vendor/bin/phpunit` (in-memory SQLite) |

## Production deploy

```bash
git clone
composer install --optimize-autoloader --no-dev
npm ci && npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan storage:link
php artisan up
```

## Tests

```bash
php artisan test
# 2 tests, 32 assertions
```

`tests/Feature/CheckoutFlowTest.php` exercises the full RBAC chain:
unauthenticated `/admin/*` → redirect `admin.login` → login as seed admin →
`admin.orders.index` → `PUT admin.orders.status` updates order status.

## RBAC

- Guard: `web` (session) on the `users` provider (`App\Models\User`).
- One table, `role` column ∈ { `customer`, `admin` }.
- Middleware alias `admin` → `App\Http\Middleware\AdminMiddleware`:
  - `auth()->check()` is false → redirect to `admin.login`.
  - `auth()->user()->isAdmin()` is false → `abort(403)`.
- Admin login enforced in `LoginController::login` (`AdminLoginRequest`).
- Frontend (catalog/cart/checkout/confirmation) is public;
  `admin.*` (except `login`/`login.post`) is guarded by `middleware('admin')`.

## Routes

| Area | Routes |
|---|---|
| Home | `GET /` (`home`) |
| Products | `GET /produk` (`products.index`), `GET /produk/{product:slug}` (`products.show`) |
| Categories | `GET /kategori`, `GET /kategori/{category:slug}` |
| Cart | `GET /titipan`, `POST .../tambah`, `POST .../update`, `DELETE .../hapus`, `DELETE /titipan/clear` |
| Checkout | `GET /checkout`, `POST /checkout`, `GET /checkout/confirmation` |
| Static | `GET /tentang`, `GET /cara-nitip`, `GET /kontak` |
| Admin auth | `GET /admin/login` (`admin.login`), `POST /admin/login` (`admin.login.post`), `POST /admin/logout` (`admin.logout`) |
| Admin (guarded) | `admin.dashboard`, `admin.products.*`, `admin.categories.*`, `admin.orders.{index,show,status,destroy}`, `admin.settings.{index,update}` |

## Security

- Escaping: all data via `{{ }}`; the only unescaped path is
  `Product::$description_html`, which is `strip_tags`-whitelisted to
  `p br strong em ul ol li hr` — dropping `<a>`, `<img>`, `<script>` so no
  `onerror`/`javascript:` vectors remain.
- External links: every `target="_blank"` carries `rel="noopener noreferrer"`.
- WhatsApp: numbers reduced to digits via `preg_replace('/[^0-9]/', '', $whatsapp)`.
- Nav links guarded with `Route::has(...)`; CSRF + `@method` on all mutations.

## White-label / handoff

1. `git clone` — `vendor/`, `node_modules/`, `.env` are git-ignored.
2. `composer install && npm install && npm run build`.
3. Copy `.env.example → .env`, set `APP_KEY`, `APP_URL`, DB, WhatsApp number, mail.
4. `php artisan migrate:fresh --seed` (default admin + product) or import a client `.sql`.
5. Brand rename: change `setting('brand_name', 'NITIP DI END')` and seeded brand/hero rows.
6. Deliver: the repo + the client's `.env` + (optionally) a `.sql` dump.

## License

Custom / closed-source by default — update this section for the client.
