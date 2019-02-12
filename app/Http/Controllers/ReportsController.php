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
                'IdType'  => 'EAN',
                'IdList.Id.1' => $product->barcode,
            ];
            $response = $client->send('GetMatchingProductForId', '/Products/2011-10-01', $optionalParams);
            dd($response);
        }
    }
}
