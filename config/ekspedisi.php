<?php

/**
 * Data referensi ekspedisi Indonesia untuk halaman "Cek Ongkir".
 *
 * Angka tarif di sini adalah REFERENSI (bukan tarif resmi kurir) dan dipakai
 * untuk estimasi cepat sebelum order. Estimasi = tarif per kg x pembulatan berat
 * x faktor zona, dengan batas minimum sesuai kebijakan tiap kurir.
 *
 * Untuk tarif live & akurat per kecamatan, sambungkan API pada bagian
 * "free_apis" di bawah (mis. api.co.id Cek Ongkir v2 atau RajaOngkir/Komerce).
 */
return [
    'origin' => [
        'city' => 'Jakarta',
        'province' => 'DKI Jakarta',
        'label' => 'Jakarta (gudang NITIP DI END)',
    ],

    // Zona jarak dari Jakarta -> faktor pengali tarif.
    'zone_factors' => [
        1 => 1.00, // Jabodetabek
        2 => 1.15, // Jawa non-Jabodetabek
        3 => 1.35, // Sumatera, Bali, Nusa Tenggara
        4 => 1.60, // Kalimantan, Sulawesi
        5 => 2.00, // Maluku, Papua
    ],

    'zones' => [
        1 => 'Jabodetabek',
        2 => 'Jawa',
        3 => 'Sumatera, Bali & Nusa Tenggara',
        4 => 'Kalimantan & Sulawesi',
        5 => 'Maluku & Papua',
    ],

    // Kota tujuan populer: nama => [zona, provinsi].
    // (Estimasi tarif kurir sebenarnya dihitung sampai level kecamatan —
    //  pakai API wilayah pada 'free_apis' bila butuh presisi penuh.)
    'cities' => [
        // Zona 1 — Jabodetabek
        'Jakarta' => ['zone' => 1, 'province' => 'DKI Jakarta'],
        'Bogor' => ['zone' => 1, 'province' => 'Jawa Barat'],
        'Depok' => ['zone' => 1, 'province' => 'Jawa Barat'],
        'Tangerang' => ['zone' => 1, 'province' => 'Banten'],
        'Tangerang Selatan' => ['zone' => 1, 'province' => 'Banten'],
        'Bekasi' => ['zone' => 1, 'province' => 'Jawa Barat'],

        // Zona 2 — Jawa
        'Bandung' => ['zone' => 2, 'province' => 'Jawa Barat'],
        'Cirebon' => ['zone' => 2, 'province' => 'Jawa Barat'],
        'Tasikmalaya' => ['zone' => 2, 'province' => 'Jawa Barat'],
        'Semarang' => ['zone' => 2, 'province' => 'Jawa Tengah'],
        'Solo' => ['zone' => 2, 'province' => 'Jawa Tengah'],
        'Magelang' => ['zone' => 2, 'province' => 'Jawa Tengah'],
        'Purwokerto' => ['zone' => 2, 'province' => 'Jawa Tengah'],
        'Yogyakarta' => ['zone' => 2, 'province' => 'DI Yogyakarta'],
        'Surabaya' => ['zone' => 2, 'province' => 'Jawa Timur'],
        'Sidoarjo' => ['zone' => 2, 'province' => 'Jawa Timur'],
        'Malang' => ['zone' => 2, 'province' => 'Jawa Timur'],
        'Kediri' => ['zone' => 2, 'province' => 'Jawa Timur'],
        'Jember' => ['zone' => 2, 'province' => 'Jawa Timur'],

        // Zona 3 — Sumatera, Bali, Nusa Tenggara
        'Denpasar' => ['zone' => 3, 'province' => 'Bali'],
        'Mataram' => ['zone' => 3, 'province' => 'NTB'],
        'Kupang' => ['zone' => 3, 'province' => 'NTT'],
        'Medan' => ['zone' => 3, 'province' => 'Sumatera Utara'],
        'Padang' => ['zone' => 3, 'province' => 'Sumatera Barat'],
        'Pekanbaru' => ['zone' => 3, 'province' => 'Riau'],
        'Batam' => ['zone' => 3, 'province' => 'Kepulauan Riau'],
        'Jambi' => ['zone' => 3, 'province' => 'Jambi'],
        'Palembang' => ['zone' => 3, 'province' => 'Sumatera Selatan'],
        'Bengkulu' => ['zone' => 3, 'province' => 'Bengkulu'],
        'Bandar Lampung' => ['zone' => 3, 'province' => 'Lampung'],
        'Banda Aceh' => ['zone' => 3, 'province' => 'Aceh'],

        // Zona 4 — Kalimantan & Sulawesi
        'Pontianak' => ['zone' => 4, 'province' => 'Kalimantan Barat'],
        'Palangkaraya' => ['zone' => 4, 'province' => 'Kalimantan Tengah'],
        'Banjarmasin' => ['zone' => 4, 'province' => 'Kalimantan Selatan'],
        'Balikpapan' => ['zone' => 4, 'province' => 'Kalimantan Timur'],
        'Samarinda' => ['zone' => 4, 'province' => 'Kalimantan Timur'],
        'Makassar' => ['zone' => 4, 'province' => 'Sulawesi Selatan'],
        'Palu' => ['zone' => 4, 'province' => 'Sulawesi Tengah'],
        'Kendari' => ['zone' => 4, 'province' => 'Sulawesi Tenggara'],
        'Gorontalo' => ['zone' => 4, 'province' => 'Gorontalo'],
        'Manado' => ['zone' => 4, 'province' => 'Sulawesi Utara'],

        // Zona 5 — Maluku & Papua
        'Ambon' => ['zone' => 5, 'province' => 'Maluku'],
        'Ternate' => ['zone' => 5, 'province' => 'Maluku Utara'],
        'Sorong' => ['zone' => 5, 'province' => 'Papua Barat Daya'],
        'Manokwari' => ['zone' => 5, 'province' => 'Papua Barat'],
        'Jayapura' => ['zone' => 5, 'province' => 'Papua'],
        'Timika' => ['zone' => 5, 'province' => 'Papua Tengah'],
        'Merauke' => ['zone' => 5, 'province' => 'Papua Selatan'],
    ],

    // Link cek resi alternatif yang bisa dipakai lintas kurir.
    'tracking_aggregator' => [
        'name' => 'CekResi',
        'url' => 'https://cekresi.com/?noresi=%s',
    ],

    // -------------------------------------------------------------------------
    // Tarif referensi per kg (berangkat dari Jakarta, paket ~1 kg, zona 1).
    // Ini estimasi pasar 2025, bukan tarif resmi — pakai API di 'free_apis'
    // kalau butuh angka live per kecamatan.
    // -------------------------------------------------------------------------
    'couriers' => [
        [
            'code' => 'jne', 'name' => 'JNE Express', 'type' => 'reguler',
            'services' => 'REG · YES · OKE', 'per_kg' => 9000, 'min_charge' => 9000,
            'etd' => '2–4 hari', 'coverage' => 'Nasional (kota & kabupaten)',
            'tracking' => true, 'tracking_page' => 'https://www.jne.co.id/id/tracking/trace',
            'tracking_query' => null,
            'notes' => 'YES untuk sampai besok; OKE opsi ekonomi yang lebih lambat.',
        ],
        [
            'code' => 'jnt', 'name' => 'J&T Express', 'type' => 'reguler',
            'services' => 'EZ (reguler)', 'per_kg' => 8000, 'min_charge' => 8000,
            'etd' => '2–4 hari', 'coverage' => 'Nasional',
            'tracking' => true, 'tracking_page' => 'https://www.jet.co.id/track',
            'tracking_query' => null,
            'notes' => 'Jaringan agen paling rapat di daerah; pickup harian.',
        ],
        [
            'code' => 'sicepat', 'name' => 'SiCepat Ekspres', 'type' => 'reguler',
            'services' => 'REG · BEST · GOKIL', 'per_kg' => 8500, 'min_charge' => 8500,
            'etd' => '2–3 hari', 'coverage' => 'Nasional',
            'tracking' => true, 'tracking_page' => 'https://www.sicepat.com/checkAwb',
            'tracking_query' => null,
            'notes' => 'Tarif dihitung per kecamatan, jadi bisa beda walau sekota.',
        ],
        [
            'code' => 'ninja', 'name' => 'Ninja Xpress', 'type' => 'reguler',
            'services' => 'Standard · Express', 'per_kg' => 9000, 'min_charge' => 9500,
            'etd' => '2–4 hari', 'coverage' => 'Nasional',
            'tracking' => true, 'tracking_page' => 'https://www.ninjaxpress.co/id-id/tracking',
            'tracking_query' => null,
            'notes' => 'Kuat di rute luar Jawa; COD banyak kota.',
        ],
        [
            'code' => 'anteraja', 'name' => 'AnterAja', 'type' => 'reguler',
            'services' => 'Reguler · Same Day', 'per_kg' => 8000, 'min_charge' => 8000,
            'etd' => '2–3 hari', 'coverage' => 'Nasional',
            'tracking' => true, 'tracking_page' => 'https://anteraja.id/tracking',
            'tracking_query' => null,
            'notes' => 'Same day tersedia di kota besar (Jabodetabek, Bandung, Surabaya).',
        ],
        [
            'code' => 'tiki', 'name' => 'TIKI', 'type' => 'reguler',
            'services' => 'REG · ONS · ECO', 'per_kg' => 9500, 'min_charge' => 10000,
            'etd' => '2–5 hari', 'coverage' => 'Nasional',
            'tracking' => true, 'tracking_page' => 'https://www.tiki.id/id/tracking',
            'tracking_query' => null,
            'notes' => 'Sering jadi andalan untuk paket ke wilayah terpencil.',
        ],
        [
            'code' => 'pos', 'name' => 'POS Indonesia', 'type' => 'reguler',
            'services' => 'Pos Reguler · Nextday', 'per_kg' => 8500, 'min_charge' => 9000,
            'etd' => '3–6 hari', 'coverage' => 'Nasional (termasuk pelosok & pulau kecil)',
            'tracking' => true, 'tracking_page' => 'https://www.posindonesia.co.id/id/tracking',
            'tracking_query' => null,
            'notes' => 'Jangkauan paling luas & tarif paling stabil ke daerah terpencil.',
        ],
        [
            'code' => 'wahana', 'name' => 'Wahana Express', 'type' => 'reguler',
            'services' => 'Reguler', 'per_kg' => 7000, 'min_charge' => 7500,
            'etd' => '3–6 hari', 'coverage' => 'Nasional (fokus Jawa, Sumatera, Kalimantan)',
            'tracking' => true, 'tracking_page' => 'https://www.wahana.com/',
            'tracking_query' => null,
            'notes' => 'Salah satu termurah; pastikan dulu kota tujuan terlayani.',
        ],
        [
            'code' => 'lion', 'name' => 'Lion Parcel', 'type' => 'reguler',
            'services' => 'REGPACK · BIGPACK · ONEPACK', 'per_kg' => 8500, 'min_charge' => 8500,
            'etd' => '2–4 hari', 'coverage' => 'Nasional',
            'tracking' => true, 'tracking_page' => 'https://lionparcel.com/tracking',
            'tracking_query' => null,
            'notes' => 'BIGPACK juara untuk paket berat (>5 kg) seperti snack borongan.',
        ],
        [
            'code' => 'idexpress', 'name' => 'ID Express', 'type' => 'reguler',
            'services' => 'Standard · Cargo', 'per_kg' => 7500, 'min_charge' => 7500,
            'etd' => '2–4 hari', 'coverage' => 'Nasional',
            'tracking' => true, 'tracking_page' => 'https://www.idexpress.com/tracking',
            'tracking_query' => null,
            'notes' => 'Tarif kompetitif untuk pengiriman rutin volume kecil–sedang.',
        ],
        [
            'code' => 'sap', 'name' => 'SAP Express', 'type' => 'reguler',
            'services' => 'Reguler · Cargo', 'per_kg' => 8000, 'min_charge' => 8000,
            'etd' => '2–4 hari', 'coverage' => 'Nasional (kuat di Jawa & Sumatera)',
            'tracking' => true, 'tracking_page' => 'https://www.sap-express.id/',
            'tracking_query' => null,
            'notes' => 'Kadang lebih murah dari kurir besar di rute tertentu.',
        ],
        [
            'code' => 'jntcargo', 'name' => 'J&T Cargo', 'type' => 'kargo',
            'services' => 'Cargo (min. 10 kg)', 'per_kg' => 4000, 'min_charge' => 45000,
            'min_weight_kg' => 10, 'etd' => '3–6 hari', 'coverage' => 'Nasional (kota besar)',
            'tracking' => true, 'tracking_page' => 'https://www.jet.co.id/track',
            'tracking_query' => null,
            'notes' => 'Untuk titipan banyak/berat: snack, skincare, atau fashion borongan.',
        ],
        [
            'code' => 'paxel', 'name' => 'Paxel', 'type' => 'instant',
            'services' => 'Same Day (titik PaxelBox)', 'per_kg' => 15000, 'min_charge' => 15000,
            'available_zones' => [1, 2], 'etd' => 'Hari yang sama',
            'coverage' => 'Jabodetabek, Bandung, Surabaya, Yogyakarta, Semarang, Malang, Bali',
            'tracking' => true, 'tracking_page' => 'https://paxel.co/',
            'tracking_query' => null,
            'notes' => 'Diantar ke titik PaxelBox/agen; pas untuk kiriman kilat.',
        ],
        [
            'code' => 'gosend', 'name' => 'GoSend Instant', 'type' => 'instant',
            'services' => 'Instant · Same Day', 'per_kg' => 20000, 'min_charge' => 20000,
            'available_zones' => [1], 'etd' => '1–3 jam (dalam kota)',
            'coverage' => 'Jabodetabek & kota besar (dalam kota saja)',
            'tracking' => true, 'tracking_page' => 'https://www.gojek.com/gosend/',
            'tracking_query' => null,
            'notes' => 'Tarif aplikasi dihitung per km — angka di sini referensi awal saja.',
        ],
        [
            'code' => 'grabexpress', 'name' => 'GrabExpress', 'type' => 'instant',
            'services' => 'Instant · Same Day', 'per_kg' => 20000, 'min_charge' => 20000,
            'available_zones' => [1], 'etd' => '1–3 jam (dalam kota)',
            'coverage' => 'Jabodetabek & kota besar (dalam kota saja)',
            'tracking' => true, 'tracking_page' => 'https://www.grab.com/id/express/',
            'tracking_query' => null,
            'notes' => 'Pakai untuk titipan yang harus sampai hari ini (dalam kota).',
        ],
    ],

    // -------------------------------------------------------------------------
    // API gratis/murah untuk ongkir & lacak resi (riset 2025–2026).
    // Opsional: halaman /ongkir tetap jalan dengan tarif referensi di atas.
    // -------------------------------------------------------------------------
    'free_apis' => [
        [
            'name' => 'api.co.id — Cek Ongkir v2 & Cek Resi',
            'url' => 'https://api.co.id/cek-ongkir/',
            'kind' => 'Ongkir + Resi',
            'free' => 'Kalkulator web gratis tanpa daftar; API Rp 5 per panggilan sukses, tanpa langganan',
            'covers' => '10 kurir sekaligus dalam 1 request: JNE REG/YES, J&T EZ & Cargo, SiCepat Regular/BEST, SAP, Ninja, IDExpress, Lion Parcel, AnterAja, Paxel',
            'notes' => 'Sampai level kecamatan (7.200+ kecamatan). Paling praktis untuk tarif asli lintas kurir.',
        ],
        [
            'name' => 'RajaOngkir (Komerce)',
            'url' => 'https://rajaongkir.com/dokumentasi/starter',
            'kind' => 'Ongkir + Resi',
            'free' => 'Tier Starter gratis — kurir & kuota harian terbatas',
            'covers' => 'JNE, POS, TIKI (Starter); kurir lain di tier berbayar',
            'notes' => 'Paling populer di ekosistem Laravel/PHP. Upgrade kalau butuh tarif akurat semua kurir.',
        ],
        [
            'name' => 'GitHub: cakfan/ongkir-sdk',
            'url' => 'https://github.com/cakfan/ongkir-sdk',
            'kind' => 'SDK (Ongkir + Resi + Webhook)',
            'free' => 'Open source MIT — gratis, bawa API key sendiri (BYOK)',
            'covers' => 'Biteship, Komerce (RajaOngkir), Shipper dalam satu kontrak ShippingProvider',
            'notes' => 'TypeScript (Bun/Node/Deno/Workers) — cocok kalau backend API dipisah dari Laravel.',
        ],
        [
            'name' => 'GitHub: nandasafiqalfiansyah/cekresi-api',
            'url' => 'https://github.com/nandasafiqalfiansyah/cekresi-api',
            'kind' => 'Resi (tracking)',
            'free' => 'Gratis, self-host / deploy sendiri',
            'covers' => 'Multi-kurir: JNE, J&T, SiCepat, POS, AnterAja, Ninja, dll.',
            'notes' => 'REST API siap pakai untuk fitur "Lacak Resi" di website.',
        ],
        [
            'name' => 'GitHub: namdevel/cek-resi',
            'url' => 'https://github.com/namdevel/cek-resi',
            'kind' => 'Resi (tracking)',
            'free' => 'Gratis, library Node.js tanpa API key',
            'covers' => 'Puluhan kurir Indonesia dalam satu library',
            'notes' => 'Ringan — bisa dijadikan microservice lacak resi untuk website ini.',
        ],
        [
            'name' => 'GitHub: sejator/api-cekresi',
            'url' => 'https://github.com/sejator/api-cekresi',
            'kind' => 'Resi (tracking)',
            'free' => 'Gratis, self-host (Node.js + headless browser)',
            'covers' => 'Kurir besar Indonesia',
            'notes' => 'Alternatif scraping yang kamu deploy sendiri; butuh server kecil.',
        ],
        [
            'name' => 'Biteship',
            'url' => 'https://biteship.com',
            'kind' => 'Ongkir + Resi + Buat Resi',
            'free' => 'Sandbox gratis untuk development; produksi bayar per pemakaian',
            'covers' => 'Banyak kurir nasional + instant (GoSend / GrabExpress)',
            'notes' => 'Ada mode test, jadi aman dipakai untuk development website ini.',
        ],
        [
            'name' => 'GitHub: api-wilayah-indonesia',
            'url' => 'https://github.com/cakfan/api-wilayah-indonesia',
            'kind' => 'Data wilayah',
            'free' => 'Gratis, open source & bisa self-host',
            'covers' => 'Provinsi → kota/kabupaten → kecamatan → kelurahan',
            'notes' => 'Untuk memperbaiki dropdown kota → kecamatan di halaman Cek Ongkir.',
        ],
    ],
];





