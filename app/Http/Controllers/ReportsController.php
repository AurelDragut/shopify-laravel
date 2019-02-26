<?php

namespace App\Http\Controllers;

use App\Product;
use App\SentFeed;
use function Composer\Autoload\includeFile;
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

        foreach (Product::all() as $product) { //where('response','like','{"Product"%')
            dump($product->barcode);
            $optionalParams = [
                'MarketplaceId' => env('MWS_MARKETPLACE_ID'),
                'IdType'  => $product->IdType,
                'IdList.Id.1' => $product->barcode,
            ];
            sleep(5);
            $result = '';
            $response = $client->send('GetMatchingProductForId', '/Products/2011-10-01', $optionalParams);
            if($response->GetMatchingProductForIdResult->Products->count() < 1) {
                $product->update(['response' => \GuzzleHttp\json_encode($response->GetMatchingProductForIdResult->Products->count())]);
            } else {
                if($response->GetMatchingProductForIdResult->Products->Product->count() <= 1) {
                    $product->update(['response' => \GuzzleHttp\json_encode($response->GetMatchingProductForIdResult->Products->Product->count())]);
                } else {
                    $rank = [];
                    $i=0;
                    foreach ($response->GetMatchingProductForIdResult->Products->Product as $variant) {
                        $rankPosition = optional($variant->SalesRankings->SalesRank[0])->Rank[0];
                        $rank[$i]['rank'] = $rankPosition;
                        $rank[$i]['asin'] = $variant->Identifiers->MarketplaceASIN->ASIN[0];
                        $i++;
                        dump($variant);
                    }
                    if (count($rank)>1)
                        $product->update(['response' => json_encode($rank)]);
                    else
                        $product->update(['response' => \GuzzleHttp\json_encode($response->GetMatchingProductForIdResult->Products->Product->count())]);
                }
            }
        }
    }

    public function getRanks() {

        $client = new \Weengs\AmazonMwsClient(
            env('AWS_ACCESS_KEY'),
            env('MWS_CLIENT_SECRET'),
            env('MWS_SELLER_ID'),
            [],
            env('MWS_AUTHORISATION_TOKEN'),
            'https://mws.amazonservices.it'
        );

        $feed = '<?xml version="1.0" encoding="UTF-8"?><AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd"><Header><DocumentVersion>1.01</DocumentVersion>';
        $feed .= '<MerchantIdentifier>A39USQT4A3RBVR</MerchantIdentifier></Header><MessageType>Product</MessageType>';
        $i = 1;
        foreach (Product::where('response','like','[{%')->get() as $rank) {
            $optionalParams = [
                'MarketplaceId' => env('MWS_MARKETPLACE_ID'),
                'IdType'  => $rank->IdType,
                'IdList.Id.1' => $rank->barcode,
            ];
            sleep(5);
            $response = $client->send('GetMatchingProductForId', '/Products/2011-10-01', $optionalParams);

            $bestRank['rank'] = 0;
            $bestRank['asin'] = 0;
            foreach ($response->GetMatchingProductForIdResult->Products->Product as $variant) {
                $rankPosition = optional($variant->SalesRankings->SalesRank[0])->Rank[0];
                if($bestRank['rank'] == 0 and !is_null($rankPosition) ) {
                    $bestRank['rank'] = $rankPosition;
                    $bestRank['asin'] = $variant->Identifiers->MarketplaceASIN->ASIN[0];
                } else {
                    if ($bestRank['rank'] > $rankPosition and !is_null($rankPosition)) {
                        $bestRank['rank'] = $rankPosition;
                        $bestRank['asin'] = $variant->Identifiers->MarketplaceASIN->ASIN[0];
                    }
                }
                $i++;
                dump($variant);
            }
            if(!is_null($bestRank['rank'][0])) {
                dump($bestRank['asin'][0]);

                $feed .= '<Message><MessageID>' . $i . '</MessageID><OperationType>Delete</OperationType><Product>';
                $feed .= '<SKU>' . $rank->sku . '</SKU></Product></Message>';
                $i++;
                $rank->update(['IdType' => 'ASIN', 'barcode' => $bestRank['asin'][0]]);
            }
        }

        $feed .= '</AmazonEnvelope>';
        dump($feed);
        $feed = trim($feed);
        try {
            $amz = new \AmazonFeed(); //if there is only one store in config, it can be omitted
            $amz->setFeedType("_POST_PRODUCT_DATA_"); //feed types listed in documentation
            $amz->setFeedContent($feed); //can be either XML or CSV data; a file upload method is available as well
            $amz->submitFeed(); //this is what actually sends the request
            $response = $amz->getResponse();
            extract($response);
            $sentFeed = SentFeed::create(
                ['FeedSubmissionId' => $FeedSubmissionId,
                    'FeedType' => $FeedType,
                    'SubmittedDate' => $SubmittedDate,
                    'FeedProcessingStatus' => $FeedProcessingStatus
                ]);

        } catch (\Exception $ex) {
            echo 'There was a problem with the Amazon library. Error: ' . $ex->getMessage();
        }
    }
}