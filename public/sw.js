/* Nitip Di End — PWA service worker (Vanilla, no deps) */
(function () {
    var CACHE = 'jastip-pwa-v4';
    var OFFLINE = '/offline.html';
    var PRECACHE = [
        '/',
        '/manifest.webmanifest',
        '/favicon.ico',
        '/images/icons/icon-192.png',
        '/images/icons/icon-512.png',
        '/images/icons/maskable-192.png',
        '/images/icons/maskable-512.png',
        '/images/icons/apple-touch-icon.png',
        '/images/icons/favicon-32.png',
        '/images/icons/favicon-16.png',
        OFFLINE,
    ];

    self.addEventListener('install', function (e) {
        e.waitUntil(caches.open(CACHE).then(function (c) {
            return c.addAll(PRECACHE);
        }).then(function () { return self.skipWaiting(); }));
    });

    self.addEventListener('activate', function (e) {
        e.waitUntil(caches.keys().then(function (keys) {
            return Promise.all(keys.map(function (k) { return k !== CACHE && caches.delete(k); }));
        }).then(function () { return self.clients.claim(); }));
    });

    function putCache(req, res) {
        if (req.method !== 'GET' || res.status !== 200) return;
        var url = new URL(req.url);
        if (url.origin !== location.origin) return;
        // Cache CSS/JS/images (incl. Vite hashed build assets) so the PWA shell never serves stale/missing styles.
        if (url.pathname.startsWith('/build/') ||
            (res.headers.get('content-type') &&
             res.headers.get('content-type').match(/^(text\/css|application\/javascript|image\/)/))) {
            caches.open(CACHE).then(function (c) { c.put(req, res.clone()); });
        }
    }

    self.addEventListener('fetch', function (e) {
        var req = e.request;
        if (req.method !== 'GET') return;

        var url = new URL(req.url);

        // Navigation requests (documents) -> network first, cache the real HTML,
        // and only fall back to offline.html once the actual page is unreachable/offline.
        if (req.destination === 'document') {
            e.respondWith(
                fetch(req).then(function (res) {
                    caches.open(CACHE).then(function (c) { c.put(req, res.clone()); });
                    return res;
                }).catch(function () {
                    return caches.match(req).then(function (cached) {
                        return cached || caches.match(OFFLINE);
                    });
                })
            );
            return;
        }

        // Vite build assets (/build/*) -> network first. They are content-hashed,
        // so cache-first would pin an old bundle and make UI fixes (theme toggle,
        // modals) appear broken until a manual cache clear. Hash change busts it.
        if (url.pathname.startsWith('/build/')) {
            e.respondWith(
                fetch(req).then(function (res) {
                    putCache(req, res);
                    return res;
                }).catch(function () { return caches.match(req); })
            );
            return;
        }

        // Everything else -> cache first, then network (and cache the response).
        e.respondWith(
            caches.match(req).then(function (cached) {
                if (cached) return cached;
                return fetch(req).then(function (res) {
                    putCache(req, res);
                    return res;
                }).catch(function () { return caches.match(OFFLINE); });
            })
        );
    });

    /* ---- Web Push ---- */
    self.addEventListener('push', function (e) {
        var data = { title: 'NITIP DI END', body: 'Ada notifikasi baru', url: '/profile/notifications' };
        if (e.data) {
            try { data = Object.assign(data, e.data.json()); } catch (err) { /* keep defaults */ }
        }
        e.waitUntil(self.registration.showNotification(data.title, {
            body: data.body,
            tag: data.tag,
            icon: '/images/icons/icon-192.png',
            badge: '/images/icons/icon-192.png',
            data: { url: data.url },
        }));
    });

    self.addEventListener('notificationclick', function (e) {
        e.notification.close();
        var url = (e.notification.data && e.notification.data.url) || '/';
        e.waitUntil(self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clients) {
            for (var i = 0; i < clients.length; i++) {
                if (clients[i].url.indexOf(self.location.origin) === 0 && 'focus' in clients[i]) {
                    if ('navigate' in clients[i]) clients[i].navigate(url);
                    return clients[i].focus();
                }
            }
            return self.clients.openWindow(url);
        }));
    });
})();