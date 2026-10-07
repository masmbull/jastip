<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImportController extends Controller
{
    public function importForm()
    {
        return view('admin.import.form');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);

        $file = $request->file('file');
        $data = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($data);

        $imported = 0;
        foreach ($data as $row) {
            $product = array_combine($header, $row);
            Product::updateOrCreate(
                ['sku' => $product['sku'] ?? null],
                [
                    'name' => $product['name'] ?? null,
                    'description' => $product['description'] ?? null,
                    'price' => (int) ($product['price'] ?? 0),
                    'stock' => (int) ($product['stock'] ?? 0),
                    'category_id' => $product['category_id'] ?? 1,
                    'is_active' => true,
                ]
            );
            $imported++;
        }

        return redirect()->back()->with('success', "$imported produk berhasil diimpor");
    }
}
