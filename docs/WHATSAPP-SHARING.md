# Berbagi invoice melalui WhatsApp

Tombol berbagi di daftar invoice membuat PNG di browser admin, lalu menawarkan
gambar dan caption lewat menu berbagi perangkat. Admin memilih aplikasi,
akun WhatsApp, dan customer, lalu mengonfirmasi pengiriman.

Nomor WhatsApp di Pengaturan adalah kontak toko yang tampil di situs dan
invoice. Nomor itu tidak mengatur akun pengirim. WhatsApp mengirim dari akun
yang sedang login di aplikasi atau WhatsApp Web pada perangkat admin.

Web Share API hanya dapat menyerahkan file dan teks ke menu berbagi sistem.
Website tidak dapat memilih WhatsApp, kontak, atau akun pengirim. Dukungan
berbagi file berbeda di tiap browser dan perangkat, dan membutuhkan HTTPS.
Jika berbagi file tidak tersedia, browser mengunduh PNG dan membuka chat
customer dengan caption yang sudah diisi. Admin perlu melampirkan PNG dan
menekan tombol kirim sendiri.

Pengiriman otomatis dari nomor bisnis tertentu ke customer membutuhkan
integrasi layanan pesan. WhatsApp Cloud API resmi dapat mengirim gambar dengan
caption, tetapi memerlukan onboarding bisnis, kredensial API, dan integrasi
server. Repo `whatsapp-web.js` dan Baileys mengotomasi WhatsApp Web, bukan
pengganti fitur berbagi browser yang didukung resmi.

## Referensi

- [WhatsApp Click to Chat](https://faq.whatsapp.com/5913398998672934/?locale=en_US)
- [MDN: Web Share API](https://developer.mozilla.org/en-US/docs/Web/API/Web_Share_API)
- [W3C: Web Share specification](https://w3c.github.io/web-share/)
- [Meta: WhatsApp Cloud API image messages](https://developers.facebook.com/documentation/business-messaging/whatsapp/messages/image-messages)
- [whatsapp-web.js](https://github.com/wwebjs/whatsapp-web.js)
- [Baileys](https://github.com/WhiskeySockets/Baileys)
