<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function getReport() {
        $client = new \Weengs\AmazonMwsClient(
            env('AWS_ACCESS_KEY'),
            env('MWS_CLIENT_SECRET'),
            env('MWS_SELLER_ID'),
            [],
            env('MWS_AUTHORISATION_TOKEN'),
            'https://mws.amazonservices.it'
        );

        foreach (Product::all() as $product) {
            $optionalParams = [
                'MarketplaceId' => env('MWS_MARKETPLACE_ID'),
                'IdType'  => 'EAN',
                'IdList.Id.1' => $product->barcode,
            ];
            sleep(5);
            $response = $client->send('GetMatchingProductForId', '/Products/2011-10-01', $optionalParams);
            $product->update(['response' => \GuzzleHttp\json_encode($response)]);
            dd($response);
        }
    }
}
