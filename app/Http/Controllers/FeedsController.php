<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class FeedsController extends Controller
{
    public function inventoryfeed()
    {
        $products = \App\Product::all();
        return response()->view('inventory-feed', compact('products'))->header('Content-Type', 'text/xml');
    }

    public function pricesfeed()
    {
        $products = \App\Product::all();
        return response()->view('prices-feed', compact('products'))->header('Content-Type', 'text/xml');
    }

    public function asinmappingfeed()
    {
        $products = \App\Product::take(5)->get();
        return response()->view('asin-mapping', compact('products'))->header('Content-Type', 'text/xml');
    }

    public function sendFeed()
    {

        $i = 1;
        $feed = '<?xml version="1.0"?><AmazonEnvelope xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Header><DocumentVersion>1.01</DocumentVersion><MerchantIdentifier>A39USQT4A3RBVR</MerchantIdentifier></Header><MessageType>Product</MessageType><PurgeAndReplace>false</PurgeAndReplace>';
        foreach (Product::all() as $product) {
            $feed .= '<Message><MessageID>' . $i . '</MessageID><OperationType>Update</OperationType><Product><SKU>' . $product->sku . '</SKU><StandardProductID><Type>'.$product->IdType.'</Type><Value>' . $product->barcode . '</Value></StandardProductID><Condition><ConditionType>New</ConditionType></Condition></Product></Message>';
            $i++;
        }
        $feed .= '</AmazonEnvelope>';
        $feed = trim($feed);
        try {
            $amz = new \AmazonFeed(); //if there is only one store in config, it can be omitted
            $amz->setFeedType("_POST_PRODUCT_DATA_"); //feed types listed in documentation
            $amz->setFeedContent($feed); //can be either XML or CSV data; a file upload method is available as well
            $amz->submitFeed(); //this is what actually sends the request
            dump($amz->getResponse());

        } catch (Exception $ex) {
            echo 'There was a problem with the Amazon library. Error: ' . $ex->getMessage();
        }
        $i = 1;
        $feed = '<?xml version="1.0" encoding="UTF-8"?><AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd"><Header><DocumentVersion>1.01</DocumentVersion><MerchantIdentifier>A39USQT4A3RBVR</MerchantIdentifier></Header><MessageType>Inventory</MessageType>';
        foreach (Product::all() as $product) {
            $feed .= '<Message><MessageID>' . $i . '</MessageID><OperationType>Update</OperationType><Inventory><SKU>' . $product->sku . '</SKU><Quantity>' . $product->inventory . '</Quantity></Inventory></Message>';
            $i++;
        }
        $feed .= '</AmazonEnvelope>';
        try {
            $amz = new \AmazonFeed(); //if there is only one store in config, it can be omitted
            $amz->setFeedType("_POST_INVENTORY_AVAILABILITY_DATA_"); //feed types listed in documentation
            $amz->setFeedContent($feed); //can be either XML or CSV data; a file upload method is available as well
            $amz->submitFeed(); //this is what actually sends the request
            dump($amz->getResponse());
        } catch (Exception $ex) {
            echo 'There was a problem with the Amazon library. Error: ' . $ex->getMessage();
        }
        $i = 1;
        $feed = '<?xml version="1.0" encoding="UTF-8"?><AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd"><Header><DocumentVersion>1.01</DocumentVersion><MerchantIdentifier>A39USQT4A3RBVR</MerchantIdentifier></Header><MessageType>Price</MessageType>';
        foreach (Product::all() as $product) {
            $feed .= '<Message><MessageID>' . $i . '</MessageID><Price><SKU>' . $product->sku . '</SKU><StandardPrice currency="EUR">' . $product->price . '</StandardPrice></Price></Message>';
            $i++;
            $product->update(['updated'=>0]);
        }
        $feed .= '</AmazonEnvelope>';
        try {
            $amz = new \AmazonFeed(); //if there is only one store in config, it can be omitted
            $amz->setFeedType("_POST_PRODUCT_PRICING_DATA_"); //feed types listed in documentation
            $amz->setFeedContent($feed); //can be either XML or CSV data; a file upload method is available as well
            $amz->submitFeed(); //this is what actually sends the request
            dump($amz->getResponse());
        } catch (Exception $ex) {
            echo 'There was a problem with the Amazon library. Error: ' . $ex->getMessage();
        }
    }
}
