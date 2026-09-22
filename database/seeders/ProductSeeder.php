<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name')->toArray();
        $products = $this->catalog();

        $seededSkus = [];
        foreach ($products as $product) {
            $seededSkus[] = $product['sku'];
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'category_id' => $categories[$product['category']],
                    'name'        => $product['name'],
                    'slug'        => Str::slug($product['name']) . '-' . strtolower(substr($product['sku'], -4)),
                    'description' => $product['description'],
                    'price'       => $product['price'],
                    'image'       => $product['image'],
                    'stock'       => $product['stock'],
                    'unit'        => $product['unit'],
                    'is_active'   => true,
                    'is_featured' => $product['is_featured'],
                ]
            );
        }

        // Drop products no longer in catalog so the table settles at exactly 100.
        // Produk viral dikelola ViralProductSeeder, jadi jangan dihapus di sini.
        Product::whereNotIn('sku', $seededSkus)->where('is_viral', false)->delete();
    }

    private function catalog(): array
    {
        $img = static fn (string $sku) => 'https://picsum.photos/seed/' . urlencode($sku) . '/600/600';
        return array_merge(
            $this->parfum($img),
            $this->tumbler($img),
            $this->beauty($img),
            $this->fashion($img),
            $this->lifestyle($img),
        );
    }

    private function parfum(callable $img): array
    {
        return $this->rows('Parfum', 'PRF', 'botol', [
            ['My Konos EDP Signature',     'EDP 50ml · aroma woody musky, tahan 8–10 jam.', 149000, 50, true],
            ['My Konos Body Mist Bloom',   'Body mist 100ml · floral segar untuk harian.',   89000, 60, false],
            ['Lucida Royale EDP',          'EDP 75ml · French-inspired, sillage kuat.',     245000, 30, true],
            ['Sakura Mist Perfume',        'EDP 30ml · cherry blossom + musk.',              119000, 40, false],
            ['Verveine Cologne',           'Cologne 50ml · citrus segar unisex.',             99000, 35, false],
            ['Oud Royale Intense',         'EDP 50ml · oud + amber, malam hari.',           389000, 18, true],
            ['Vanilla Silk Body Mist',     'Body mist 150ml · vanilla lembut.',               79000, 70, false],
            ['Bergamot Fresh EDT',         'EDT 100ml · citrus woody untuk pria.',          165000, 25, false],
            ['Rose Velvet Perfume',        'EDP 40ml · rose + patchouli feminin.',          219000, 22, true],
            ['Amber Night EDP',            'EDP 50ml · amber hangat.',                      279000, 20, false],
            ['White Tea Body Mist',        'Body mist 120ml · tea fresh.',                    69000, 80, false],
            ['Sandalwood Reserve',         'EDP 60ml · sandalwood premium.',                199000, 28, true],
            ['Citrus Splash Cologne',      'Cologne 75ml · orange lemon splash.',            109000, 45, false],
            ['Jasmine Dream EDP',          'EDP 50ml · jasmine night bloom.',               169000, 30, false],
            ['Leather & Wood EDT',         'EDT 100ml · maskulin modern.',                  229000, 18, true],
            ['Lavender Calm Mist',         'Body mist 100ml · lavender soothing.',            59000, 90, false],
            ['Aqua Marine Cologne',        'Cologne 50ml · aquatic fresh.',                   89000, 50, false],
            ['Mystic Oud Premium',         'EDP 30ml · oud arabian.',                       449000, 12, true],
            ['Peony Bloom Perfume',        'EDP 40ml · peony + musk.',                      189000, 24, false],
            ['Noir Velvet EDP',            'EDP 50ml · dark mysterious.',                   259000, 20, false],
        ], $img);
    }

    private function tumbler(callable $img): array
    {
        return $this->rows('Tumbler', 'TMB', 'buah', [
            ['Tyeso Tumbler 500ml',           'Stainless steel, double wall, hot/cold 12 jam.', 189000, 25, true],
            ['Tyeso Bottle Sport 750ml',     'Sport bottle BPA free, anti slip grip.',        129000, 40, false],
            ['Tyeso Travel Mug 350ml',       'Tumbler mini, pas di cup holder mobil.',        119000, 35, false],
            ['Aurora Tumbler Gradient',      'Tumbler 500ml motif gradient pastel.',          139000, 30, true],
            ['Aurora Bottle Pastel 600ml',   'Botol pastel dengan strap.',                   109000, 40, false],
            ['Lyra Insulated Tumbler 480ml','Insulated 12 jam, warna matte.',                159000, 28, false],
            ['Lyra Kids Bottle 350ml',   'Botol anak dengan karakter lucu.',               79000, 60, false],
            ['Nordic Glass Tumbler 450ml',  'Tumbler kaca dengan sleeve kayu.',              175000, 22, true],
            ['Nordic Glass Bottle 500ml',  'Botol kaca + sleeve silikon.',                  145000, 26, false],
            ['Polar Steel Tumbler 600ml',   'Tumbler steel polos 600ml.',                    169000, 24, false],
            ['Polar Steel Bottle 1L',       'Botol steel jumbo kapasitas 1L.',                199000, 18, true],
            ['Nimble Foldable Bottle',   'Botol lipat silikon, travel-friendly.',           59000, 70, false],
            ['Heritage Ceramic Tumbler',    'Tumbler keramik premium dengan tutup kayu.',    249000, 15, true],
            ['Heritage Ceramic Mug',       'Mug keramik handmade 300ml.',                   139000, 25, false],
            ['Glacier Frosted Bottle 500ml','Botol kaca frosted premium.',                   119000, 30, false],
            ['Glacier Frosted Tumbler 400ml','Tumbler kaca frosted minimalis.',                99000, 40, false],
            ['Bamboo Eco Tumbler 450ml',    'Tumbler bambu eco-friendly.',                   189000, 20, true],
            ['Bamboo Eco Bottle 500ml',   'Botol bambu tutup stainless.',                  165000, 22, false],
            ['Sunset Copper Tumbler 500ml','Tumbler finishing copper premium.',             219000, 18, true],
            ['Sunset Copper Bottle 700ml',  'Botol copper 700ml, koleksi eksklusif.',        259000, 14, false],
        ], $img);
    }

    private function beauty(callable $img): array
    {
        return $this->rows('Beauty', 'BTY', 'pcs', [
            ['Glow Serum Vitamin C 30ml',     'Serum brightening 10% vitamin C.',                         89000, 60, true],
            ['Glow Serum Niacinamide 30ml',   'Serum 5% niacinamide anti-pores.',                         99000, 55, false],
            ['Hydra Moisturizer 50ml',      'Pelembab ringan untuk normal-kombinasi.',                  79000, 80, false],
            ['Hydra Moisturizer Rich 50ml','Pelembab rich untuk kulit kering.',                       109000, 50, true],
            ['Sunscreen SPF50 PA++++',       'Sunscreen ringan tidak whitecast.',                         89000, 90, false],
            ['Sunscreen Tone-Up 40ml',      'Sunscreen tone-up natural finish.',                       109000, 45, false],
            ['Cleansing Foam Gentle 100ml','Pembersih wajah pH seimbang.',                             49000,100, false],
            ['Cleansing Foam Acne 100ml',   'Pembersih wajah anti-acne tea tree.',                       59000, 70, false],
            ['Toner Centella 200ml',        'Toner soothing centella asiatica.',                         79000, 80, true],
            ['Toner BHA 200ml',             'Toner exfoliasi BHA untuk pori-pori.',                      99000, 60, false],
            ['Lip Tint Velvet 4g',          'Lip tint tahan lama 8 jam.',                               65000, 80, false],
            ['Lip Cream Matte 4g',          'Lip cream matte intens.',                                  55000, 90, false],
            ['Mascara Volume 8ml',          'Mascara volumizing waterproof.',                           89000, 50, true],
            ['Eyeliner Liquid Precision',   'Eyeliner waterproof presisi.',                             59000, 70, false],
            ['Foundation Dewy 30ml',       'Foundation dewy light coverage.',                         119000, 40, false],
            ['Foundation Matte 30ml',      'Foundation matte medium coverage.',                       129000, 35, false],
            ['Setting Spray 100ml',       'Setting spray long-lasting 16 jam.',                       99000, 45, true],
            ['Makeup Remover Micellar 200ml','Micellar water untuk makeup waterproof.',                  65000, 90, false],
            ['Hair Serum Argan 50ml',      'Serum rambut argan anti-frizz.',                          109000, 40, false],
            ['Body Lotion Shea 250ml',     'Lotion body shea butter.',                                 79000, 70, false],
        ], $img);
    }

    private function fashion(callable $img): array
    {
        return $this->rows('Fashion', 'FSH', 'pcs', [
            ['Linen Shirt Oversize',      'Kemeja linen oversize unisex.',                            159000, 30, true],
            ['Cotton Tee Boxy Fit',       'Kaos cotton combed 24s boxy fit.',                          89000, 60, false],
            ['Crewneck Sweater Knit',     'Sweater rajut crewneck 350gsm.',                           189000, 25, true],
            ['Hoodie Pullover Fleece',    'Hoodie fleece tebal, hangat.',                            219000, 22, false],
            ['Cargo Pants Tactical',   'Celana cargo tactical 6 saku.',                           239000, 20, true],
            ['Chino Pants Slim',        'Celana chino slim fit premium.',                          179000, 28, false],
            ['Pleated Skirt Midi',      'Rok midi pleated feminin.',                               149000, 30, false],
            ['A-Line Dress Casual',       'Dress kasual a-line.',                                    189000, 22, false],
            ['Denim Jacket Vintage Wash','Jaket denim vintage wash.',                               289000, 16, true],
            ['Bomber Jacket Satin',     'Jaket bomber unisex satin finish.',                        199000, 20, false],
            ['Cap Dad Hat Classic',       'Topi dad hat klasik 6-panel.',                             59000, 80, false],
            ['Beanie Wool Knit',          'Beanie wool rajut hangat.',                                49000, 70, false],
            ['Canvas Tote Bag',          'Tote bag canvas 38cm.',                                    99000, 50, false],
            ['Leather Crossbody Sling',   'Sling bag kulit sintetis premium.',                       159000, 35, true],
            ['Silk Scarf Square 90cm','Scarf silk motif floral.',                                 89000, 45, false],
            ['Belt Leather Brown',      'Ikat pinggang kulit 110cm.',                              119000, 40, false],
            ['Socks Cotton Pack 3',     'Kaos kaki katun pack 3 pasang.',                            29000,150, false],
            ['Wrist Watch Minimalist',   'Jam tangan minimalist mesh strap.',                       249000, 18, true],
            ['Sun Hat Wide Brim',        'Topi lebar anti UV.',                                     109000, 35, false],
            ['Sneakers Low White',       'Sneakers low putih casual.',                              289000, 20, true],
        ], $img);
    }

    private function lifestyle(callable $img): array
    {
        return $this->rows('Lifestyle', 'LFS', 'pcs', [
            ['Aromatherapy Diffuser',         'Diffuser essential oil ultrasonik 300ml.',           189000, 25, true],
            ['Aromatherapy Candle Soy 200g','Lilin aromaterapi soy wax.',                           79000, 50, false],
            ['Essential Oil Lavender 10ml',   'Essential oil lavender murni.',                        49000, 80, false],
            ['Essential Oil Peppermint 10ml', 'Essential oil peppermint menyegarkan.',               49000, 80, false],
            ['Notebook A5 Dot Grid',          'Notebook dot grid 180 halaman.',                       59000, 90, false],
            ['Fountain Pen Classic',          'Pulpen fountain klasik stainless.',                   89000, 40, false],
            ['Desk Mat Felt XL',              'Desk mat felt 90x40cm.',                               99000, 35, false],
            ['Reading Lamp LED',              'Lampu baca LED adjustable warm/cool.',               149000, 22, true],
            ['Plant Pot Ceramic 12cm',        'Pot keramik handmade 12cm.',                           59000, 60, false],
            ['Succulent Mini Artificial',     'Tanaman sukulen artifisial mini.',                     39000,100, false],
            ['Phone Stand Aluminum',          'Stand phone aluminum adjustable.',                     79000, 50, false],
            ['Wireless Charger 15W',          'Wireless charger fast charge 15W.',                   119000, 40, true],
            ['Powerbank 10000mAh Slim',       'Powerbank slim 10000mAh dual port.',                  179000, 30, false],
            ['Cable USB-C Fast 1m',           'Kabel USB-C fast charge braided 1m.',                  49000, 90, false],
            ['Bluetooth Earbuds TWS',         'Earbuds TWS bluetooth 5.3 + charging case.',           199000, 25, true],
            ['Mini Speaker Portable',         'Speaker portable bluetooth waterproof.',              159000, 20, false],
            ['Yoga Mat 6mm TPE',              'Matras yoga TPE 6mm eco-friendly.',                    139000, 30, false],
            ['Resistance Band Set',           'Resistance band latex set 5 tingkat.',                 79000, 50, false],
            ['Water Bottle Straw 700ml',      'Botol drinking straw BPA free 700ml.',                  69000, 70, false],
            ['Lunch Box Bento 2 Tier',        'Lunch box stainless 2 susun.',                         109000, 35, false],
        ], $img);
    }

    private function rows(string $category, string $skuPrefix, string $unit, array $items, callable $img): array
    {
        $rows = [];
        foreach ($items as $i => [$name, $desc, $price, $stock, $featured]) {
            $n = $i + 1;
            $sku = sprintf('%s-%03d', $skuPrefix, $n);
            $rows[] = [
                'name'        => $name,
                'category'    => $category,
                'description' => $desc,
                'price'       => (int) $price,
                'image'       => $img($sku),
                'stock'       => (int) $stock,
                'sku'         => $sku,
                'unit'        => $unit,
                'is_featured' => (bool) $featured,
            ];
        }
        return $rows;
    }
}