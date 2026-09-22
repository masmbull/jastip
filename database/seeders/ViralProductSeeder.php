<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Katalog "Produk Viral" â€” produk yang sedang tren di Indonesia (2024â€“2025)
 * berdasarkan pantauan TikTok Shop, Shopee, dan Google Trends Indonesia.
 *
 * Foto produk disimpan di public/images/viral/*.jpg (foto stok Pexels sebagai
 * ilustrasi) sehingga tidak bergantung pada hotlink pihak ketiga.
 * Harga yang diisi = harga pasaran bawah, 'price_max' = batas atas rentang.
 */
class ViralProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'slug')->toArray();

        $seededSkus = [];

        foreach ($this->catalog() as $row) {
            $sku = $row['sku'];
            $seededSkus[] = $sku;

            Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'category_id' => $categories[$row['category']] ?? null,
                    'name'        => $row['name'],
                    'brand'       => $row['brand'],
                    'slug'        => Str::slug($row['name']) . '-' . strtolower(substr($sku, -4)),
                    'description' => $row['description'],
                    'price'       => $row['price'],
                    'price_max'   => $row['price_max'],
                    'image'       => 'images/viral/' . $row['image'],
                    'stock'       => $row['stock'],
                    'rating'      => $row['rating'],
                    'sold_count'  => $row['sold_count'],
                    'unit'        => $row['unit'] ?? 'pcs',
                    'is_active'   => true,
                    'is_featured' => $row['is_featured'] ?? false,
                    'is_viral'    => true,
                    'viral_rank'  => $row['rank'],
                ]
            );
        }

        // Produk yang sudah tidak masuk daftar viral: matikan flag-nya saja
        // (jangan dihapus supaya histori order lama tetap valid).
        Product::where('is_viral', true)
            ->whereNotIn('sku', $seededSkus)
            ->update(['is_viral' => false, 'viral_rank' => null]);
    }

    private function catalog(): array
    {
        return array_merge(
            $this->beauty(),
            $this->fashion(),
            $this->makanan(),
            $this->lifestyle(),
            $this->aksesoris(),
            $this->parfum(),
        );
    }

    private function beauty(): array
    {
        return [
            [
                'sku' => 'VRL-BTY-001', 'rank' => 1, 'category' => 'beauty',
                'name' => 'Skintific 5X Ceramide Barrier Repair Moisture Gel', 'brand' => 'Skintific',
                'price' => 65000, 'price_max' => 89000, 'rating' => 4.9, 'sold_count' => 12400,
                'stock' => 120, 'image' => 'skintific-5x-ceramide-moisture-gel.jpg', 'is_featured' => true,
                'description' => 'Moisturizer viral sejak 2022 dan masih jadi juara di TikTok Shop. Tekstur gel ringan, 5X ceramide untuk memperkuat skin barrier â€” cocok untuk kulit kering maupun berjerawat.',
            ],
            [
                'sku' => 'VRL-BTY-002', 'rank' => 2, 'category' => 'beauty',
                'name' => 'The Originote HyaluCera Moisturizer', 'brand' => 'The Originote',
                'price' => 39000, 'price_max' => 55000, 'rating' => 4.8, 'sold_count' => 9800,
                'stock' => 150, 'image' => 'the-originote-hyalucera-moisturizer.jpg',
                'description' => 'Salah satu produk lokal paling laris karena harga di bawah Rp 50 ribu dengan kombinasi hyaluronic acid + ceramide. Lembap tapi tidak lengket, aman untuk pemakaian pagi dan malam.',
            ],
            [
                'sku' => 'VRL-BTY-003', 'rank' => 3, 'category' => 'beauty',
                'name' => 'Azarine Hydrasoothe Sunscreen SPF45 PA++++', 'brand' => 'Azarine',
                'price' => 45000, 'price_max' => 65000, 'rating' => 4.9, 'sold_count' => 15000,
                'stock' => 100, 'image' => 'azarine-hydrasoothe-sunscreen.jpg', 'is_featured' => true,
                'description' => 'Sunscreen lokal yang konsisten trending di e-commerce: SPF45 PA++++, ada varian gel dan lotion. Ringan, tidak whitecast, dan sering direkomendasikan dermatolog untuk kulit sensitif.',
            ],
            [
                'sku' => 'VRL-BTY-004', 'rank' => 4, 'category' => 'beauty',
                'name' => 'Somethinc Niacinamide + Sabi Beet Serum', 'brand' => 'Somethinc',
                'price' => 79000, 'price_max' => 119000, 'rating' => 4.8, 'sold_count' => 7600,
                'stock' => 80, 'image' => 'somethinc-niacinamide-sabi-beet-serum.jpg',
                'description' => 'Serum pencerah yang ramai dibahas untuk bekas jerawat dan kulit kusam. Kombinasi niacinamide + sabi beet (AHA alami) â€” pakai bertahap karena bisa terasa tingling.',
            ],
            [
                'sku' => 'VRL-BTY-005', 'rank' => 5, 'category' => 'beauty',
                'name' => 'Hanasui Mattedorable Lip Cream', 'brand' => 'Hanasui',
                'price' => 25000, 'price_max' => 35000, 'rating' => 4.8, 'sold_count' => 21000,
                'stock' => 200, 'image' => 'hanasui-mattedorable-lip-cream.jpg',
                'description' => 'Lip cream murah meriah yang jadi langganan haul video TikTok. Matte tapi tidak kering, banyak shade nude yang cocok untuk kulit sawo matang.',
            ],
            [
                'sku' => 'VRL-BTY-006', 'rank' => 6, 'category' => 'beauty',
                'name' => 'Glad2Glow Centella Acne Soothing Moisturizer', 'brand' => 'Glad2Glow',
                'price' => 30000, 'price_max' => 45000, 'rating' => 4.7, 'sold_count' => 11300,
                'stock' => 110, 'image' => 'glad2glow-centella-moisturizer.jpg',
                'description' => 'Pelembap centella yang difavoritkan pemilik kulit berjerawat. Sering viral karena review "kulit kalem dalam seminggu" dengan harga di bawah Rp 50 ribu.',
            ],
            [
                'sku' => 'VRL-BTY-007', 'rank' => 7, 'category' => 'beauty',
                'name' => 'Avoskin Miraculous Refining Toner', 'brand' => 'Avoskin',
                'price' => 99000, 'price_max' => 139000, 'rating' => 4.9, 'sold_count' => 5400,
                'stock' => 60, 'image' => 'avoskin-miraculous-refining-toner.jpg',
                'description' => 'Eksfoliasi AHA-BHA-PHA yang rutin masuk daftar "produk lokal terbaik". Membantu tekstur kulit dan komedo; wajib pakai sunscreen setelahnya.',
            ],
            [
                'sku' => 'VRL-BTY-008', 'rank' => 8, 'category' => 'beauty',
                'name' => 'Whitelab Niacinamide Brightening Serum', 'brand' => 'Whitelab',
                'price' => 40000, 'price_max' => 60000, 'rating' => 4.7, 'sold_count' => 8900,
                'stock' => 130, 'image' => 'whitelab-niacinamide-serum.jpg',
                'description' => 'Serum brightening dengan harga kantong pelajar, sering muncul di live shopping. Fokus mencerahkan dan menyamarkan noda hitam dengan pemakaian rutin.',
            ],
        ];
    }

    private function fashion(): array
    {
        return [
            [
                'sku' => 'VRL-FSH-001', 'rank' => 9, 'category' => 'fashion',
                'name' => 'Buttonscarves Voile Hijab Signature', 'brand' => 'Buttonscarves',
                'price' => 89500, 'price_max' => 125000, 'rating' => 4.9, 'sold_count' => 6800,
                'stock' => 90, 'image' => 'buttonscarves-voile-hijab.jpg', 'is_featured' => true,
                'description' => 'Voile premium yang jadi ikon brand lokal ini: bahan adem, jatuh rapi, dan warnanya konsisten. Selalu sold out saat color baru rilis karena diborong reseller.',
            ],
            [
                'sku' => 'VRL-FSH-002', 'rank' => 10, 'category' => 'fashion',
                'name' => 'Ventela Public Low Sneakers', 'brand' => 'Ventela',
                'price' => 255000, 'price_max' => 329000, 'rating' => 4.8, 'sold_count' => 4200,
                'stock' => 45, 'image' => 'ventela-public-low-sneakers.jpg',
                'description' => 'Sneakers lokal yang viral karena gaya retro 60-an-nya dan kompatibel untuk kerja maupun kuliah. Perpaduan kanvas + sol karet, nyaman dipakai harian.',
            ],
            [
                'sku' => 'VRL-FSH-003', 'rank' => 11, 'category' => 'fashion',
                'name' => 'Erigo Oversized Hoodie Fleece', 'brand' => 'Erigo',
                'price' => 199000, 'price_max' => 279000, 'rating' => 4.7, 'sold_count' => 3900,
                'stock' => 55, 'image' => 'erigo-oversized-hoodie.jpg',
                'description' => 'Hoodie oversized best seller Erigo: fleece tebal, tersedia size up sampai XXL. Sering jadi target checkout saat flash sale dan kupon toko.',
            ],
            [
                'sku' => 'VRL-FSH-004', 'rank' => 12, 'category' => 'fashion',
                'name' => 'Abaya Dubai Premium Nida', 'brand' => 'Zahra Modest',
                'price' => 189000, 'price_max' => 349000, 'rating' => 4.8, 'sold_count' => 3100,
                'stock' => 40, 'image' => 'abaya-dubai-premium.jpg',
                'description' => 'Abaya bahan nida yang tidak tembus pandang dan jatuh mewah. Tren modest fashion 2025 membuat model Dubai laris di marketplace maupun live streaming.',
            ],
        ];
    }

    private function makanan(): array
    {
        return [
            [
                'sku' => 'VRL-FOD-001', 'rank' => 13, 'category' => 'makanan',
                'name' => 'Coklat Dubai Kunafa Pistachio', 'brand' => 'Fix Dessert Chocolatier',
                'price' => 65000, 'price_max' => 150000, 'rating' => 4.8, 'sold_count' => 14500,
                'stock' => 60, 'image' => 'coklat-dubai-kunafa-pistachio.jpg', 'is_featured' => true,
                'description' => 'Fenomena "Dubai chocolate" yang meledak di TikTok sepanjang 2024â€“2025: coklat tebal diisi kunafa renyah dan pasta pistachio. Banyak versi lokal dengan harga jauh lebih ramah.',
            ],
            [
                'sku' => 'VRL-FOD-002', 'rank' => 14, 'category' => 'makanan',
                'name' => 'Samyang Buldak Carbonara Ramen', 'brand' => 'Samyang',
                'price' => 25000, 'price_max' => 35000, 'rating' => 4.9, 'sold_count' => 32000,
                'stock' => 300, 'image' => 'samyang-buldak-carbonara.jpg',
                'description' => 'Mie pedas yang tidak pernah keluar dari daftar trending. Varian carbonara paling dicari karena creamy tapi tetap pedas; sering jadi bahan konten challenge pedas.',
            ],
            [
                'sku' => 'VRL-FOD-003', 'rank' => 15, 'category' => 'makanan',
                'name' => 'Matcha KitKat Japan Import', 'brand' => 'KitKat Japan',
                'price' => 35000, 'price_max' => 55000, 'rating' => 4.8, 'sold_count' => 9100,
                'stock' => 180, 'image' => 'matcha-kitkat-japan.jpg',
                'description' => 'Coklat matcha khas Jepang yang jadi jastip paling dicari. Rasa matcha-nya lebih pekat dibanding versi lokal â€” cocok untuk oleh-oleh atau stok snack kantor.',
            ],
            [
                'sku' => 'VRL-FOD-004', 'rank' => 16, 'category' => 'makanan',
                'name' => 'Paket Snack Jepang Viral (Mix 10 Item)', 'brand' => 'Japan Snack Bundle',
                'price' => 165000, 'price_max' => 250000, 'rating' => 4.7, 'sold_count' => 3800,
                'stock' => 70, 'image' => 'snack-jepang-bundle.jpg',
                'description' => 'Bundle snack campur dari Don Quijote/Matsumoto Kiyoshi: Pocky, Pretz, mochi, dan wafer edisi terbatas. Format bundle begini paling laris untuk konten unboxing.',
            ],
        ];
    }

    private function lifestyle(): array
    {
        return [
            [
                'sku' => 'VRL-LFS-001', 'rank' => 17, 'category' => 'lifestyle',
                'name' => 'Labubu Lazy Yoga Series Blind Box', 'brand' => 'Pop Mart',
                'price' => 195000, 'price_max' => 450000, 'rating' => 4.9, 'sold_count' => 18600,
                'stock' => 50, 'image' => 'labubu-lazy-yoga-series.jpg', 'is_featured' => true,
                'description' => 'Blind box paling ramai diburu di Indonesia 2024â€“2025. Seri Lazy Yoga punya pose menggemaskan dan chase secret yang harganya di pasar sekunder bisa berkali-kali lipat.',
            ],
            [
                'sku' => 'VRL-LFS-002', 'rank' => 18, 'category' => 'lifestyle',
                'name' => 'Labubu Macaron Keychain Blind Box', 'brand' => 'Pop Mart',
                'price' => 85000, 'price_max' => 195000, 'rating' => 4.8, 'sold_count' => 22400,
                'stock' => 120, 'image' => 'labubu-macaron-keychain.jpg',
                'description' => 'Versi gantungan kunci dengan warna pastel yang paling sering dijual "sold out" di TikTok Live. Cocok untuk hias tas, set kunci, atau hadiah ulang tahun.',
            ],
            [
                'sku' => 'VRL-LFS-003', 'rank' => 19, 'category' => 'lifestyle',
                'name' => 'Tyeso Tumbler Stainless 500ml', 'brand' => 'Tyeso',
                'price' => 55000, 'price_max' => 89000, 'rating' => 4.8, 'sold_count' => 16700,
                'stock' => 160, 'image' => 'tyeso-tumbler-500ml.jpg',
                'description' => 'Tumbler stainless yang viral karena harganya di bawah Rp 100 ribu tapi tahan dingin sampai 12 jam. Sering jadi item wajib di konten "back to campus".',
            ],
            [
                'sku' => 'VRL-LFS-004', 'rank' => 20, 'category' => 'lifestyle',
                'name' => 'Stanley Quencher H2.0 FlowState 890ml', 'brand' => 'Stanley',
                'price' => 289000, 'price_max' => 599000, 'rating' => 4.9, 'sold_count' => 7300,
                'stock' => 35, 'image' => 'stanley-quencher-h2o.jpg',
                'description' => 'Tumbler ikonik yang sempat jadi "status symbol" di media sosial. Kapasitas 890 ml, tahan dingin hingga 11 jam â€” banyak dicari varian warna limited.',
            ],
        ];
    }

    private function aksesoris(): array
    {
        return [
            [
                'sku' => 'VRL-ACC-001', 'rank' => 21, 'category' => 'accessories',
                'name' => 'Casio Vintage A158WA Digital Watch', 'brand' => 'Casio',
                'price' => 320000, 'price_max' => 450000, 'rating' => 4.9, 'sold_count' => 11800,
                'stock' => 75, 'image' => 'casio-vintage-a158.jpg', 'is_featured' => true,
                'description' => 'Jam retro yang tidak pernah mati trennya â€” dipakai dari anak sekolah sampai stylist. Case resin, strap stainless, tahan air, dan baterainya awet bertahun-tahun.',
            ],
            [
                'sku' => 'VRL-ACC-002', 'rank' => 22, 'category' => 'accessories',
                'name' => 'Tas Selempang Kanvas Dubai Style', 'brand' => 'Local Bag Co.',
                'price' => 79000, 'price_max' => 145000, 'rating' => 4.7, 'sold_count' => 9400,
                'stock' => 110, 'image' => 'tas-selempang-kanvas-dubai.jpg',
                'description' => 'Tas selempang kanvas yang viral karena muat banyak dan cocok untuk OOTD maupun kerja. Banyak dimodifikasi dengan gantungan Labubu â€” kombo yang sedang hype.',
            ],
            [
                'sku' => 'VRL-ACC-003', 'rank' => 23, 'category' => 'accessories',
                'name' => 'Marhen.J Shoulder Bag Bali', 'brand' => 'Marhen.J',
                'price' => 119000, 'price_max' => 235000, 'rating' => 4.8, 'sold_count' => 6100,
                'stock' => 65, 'image' => 'marhenj-shoulder-bag.jpg',
                'description' => 'Tas lokal yang jadi favorit karena desainnya mirip brand Korea/kartun populer dengan harga jauh lebih ramah. Bahan kanvas tebal, ringan, dan warnanya netral.',
            ],
            [
                'sku' => 'VRL-ACC-004', 'rank' => 24, 'category' => 'accessories',
                'name' => 'Gantungan Kunci Labubu Plush Peluche', 'brand' => 'Pop Mart',
                'price' => 65000, 'price_max' => 175000, 'rating' => 4.7, 'sold_count' => 19800,
                'stock' => 140, 'image' => 'gantungan-kunci-labubu.jpg',
                'description' => 'Peluche kecil yang jadi aksesori wajib tas 2025. Dipakai sebagai charm tas, gantungan kunci mobil, sampai hiasan tote bag â€” stok cepat habis tiap restock.',
            ],
        ];
    }

    private function parfum(): array
    {
        return [
            [
                'sku' => 'VRL-PRF-001', 'rank' => 25, 'category' => 'parfum',
                'name' => 'HMNS Perfume Alpha Eau de Parfum', 'brand' => 'HMNS',
                'price' => 169000, 'price_max' => 249000, 'rating' => 4.8, 'sold_count' => 12800,
                'stock' => 90, 'image' => 'hmns-perfume-alpha.jpg', 'is_featured' => true,
                'description' => 'Parfum lokal yang berhasil masuk toko retail modern: aroma woody-citrus tahan lama 6â€“8 jam. Alpha salah satu varian terseksi dan paling sering repeat order.',
            ],
            [
                'sku' => 'VRL-PRF-002', 'rank' => 26, 'category' => 'parfum',
                'name' => 'Mykonos EDP Signature Edition', 'brand' => 'Mykonos',
                'price' => 129000, 'price_max' => 199000, 'rating' => 4.7, 'sold_count' => 10500,
                'stock' => 95, 'image' => 'mykonos-edp-signature.jpg',
                'description' => 'Local brand yang naik lewat TikTok: aroma sweet-floral dengan proyeksi kuat, sering disebut mirip parfum niche mahal. Cocok untuk cuaca hangat Indonesia.',
            ],
            [
                'sku' => 'VRL-PRF-003', 'rank' => 27, 'category' => 'parfum',
                'name' => 'Lucida Royale Eau de Parfum', 'brand' => 'Lucida',
                'price' => 89000, 'price_max' => 139000, 'rating' => 4.6, 'sold_count' => 8700,
                'stock' => 105, 'image' => 'lucida-royale-edp.jpg',
                'description' => 'Pilihan parfum lokal paling ramah kantong dengan banyak varian aroma. Sering dijadikan isi hampers dan parcel karena botolnya rapi untuk difoto.',
            ],
        ];
    }
}

