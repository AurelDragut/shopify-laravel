@extends('shopify-app::layouts.default')

@section('content')
    <p>You are: {{ ShopifyApp::shop()->shopify_domain }}</p>

    <?php

    $shop = \OhMyBrew\ShopifyApp\Facades\ShopifyApp::shop();

    \Illuminate\Support\Facades\Log::info('welcome');
    echo '<ul>';
    $results = $shop->api()->rest('GET', '/admin/webhooks.json');

    //$results = $shop->api()->rest('POST', '/admin/webhooks.json', array("webhook" => array( "topic"=>"products/delete", "address"=>  "https://shopify.aureldragut.com/webhooks/products-delete", "format"=> "json")));
    dd($results);
    $results = $results->body->products;
    foreach ($results as $result) {
        echo '<li>' . $result->title . '</li>';
    }
    echo '</ul>';

    /*$client = new \Weengs\AmazonMwsClient(
        env('AWS_ACCESS_KEY'),
        env('MWS_CLIENT_SECRET'),
        env('MWS_SELLER_ID'),
        [],
        env('MWS_AUTHORISATION_TOKEN'),
        'https://mws.amazonservices.it'
    );

    $productsCount = $shop->api()->rest('GET', '/admin/products/count.json');

    $productPages = ceil($productsCount->body->count / 10);

    $j = 0;

    for($i=0;$i<$productPages;$i++) {

        sleep(0.5);
        $products = $shop->api()->rest('GET', '/admin/products.json?limit=10&page='.($i+1));

        foreach($products->body->products as $product) {

            sleep(0.5);
            $variants = $shop->api()->rest('GET', '/admin/products/'.$product->id.'/variants.json?fields=barcode,sku,price,inventory_quantity');
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
                            'updated' => true
                        ]);
                    $j++;
                } else dump($variant);
            }
        }
    }
    dump($j);*/

    /*76837716014 bags
    76837748782 shoes
    76837683246 Clothing
    76837781550 Accessories*/
    ?>

@endsection

@section('scripts')
    @parent

    <script type="text/javascript">
        // ESDK page and bar title
        window.mainPageTitle = 'Welcome Page';
        ShopifyApp.ready(function () {
            ShopifyApp.Bar.initialize({
                title: 'Welcome'
            })
        });
    </script>
@endsection