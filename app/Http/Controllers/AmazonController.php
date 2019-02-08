<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OhMyBrew\ShopifyApp\ShopifyApp;

class AmazonController extends Controller
{
    public function updatelist()
    {

        $shop = \OhMyBrew\ShopifyApp\Facades\ShopifyApp::shop();

        $productsCount = $shop->api()->rest('GET', '/admin/products/count.json');

        $productPages = ceil($productsCount->body->count / 10);

        $j = 0;

        for ($i = 0; $i < $productPages; $i++) {

            sleep(0.5);
            $products = $shop->api()->rest('GET', '/admin/products.json?limit=10&page=' . ($i + 1));

            foreach ($products->body->products as $product) {

                sleep(0.5);
                $variants = $shop->api()->rest('GET', '/admin/products/' . $product->id . '/variants.json?fields=barcode,sku,price,inventory_quantity');
                foreach ($variants->body->variants as $variant) {

                    if (!is_null($variant->barcode)) {
                        $product = \App\Product::updateOrCreate(
                            [
                                'barcode' => $variant->barcode,
                                'sku' => $variant->sku
                            ],
                            [
                                'price' => $variant->price,
                                'inventory' => $variant->inventory_quantity,
                                'updated' => 1
                            ]);
                        $j++;
                    } else dump($variant);
                }
            }
        }
        dump($j);
    }
}
