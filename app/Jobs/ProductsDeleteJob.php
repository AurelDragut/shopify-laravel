<?php namespace App\Jobs;

use App\SentFeed;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProductsDeleteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var string
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param string $shopDomain The shop's myshopify domain
     * @param object $webhook The webhook data (JSON decoded)
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        http_response_code(200);
        $product = $this->data;
        $i = 1;
        $feed = '<?xml version="1.0" encoding="UTF-8"?><AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd"><Header><DocumentVersion>1.01</DocumentVersion>';
        $feed .= '<MerchantIdentifier>A39USQT4A3RBVR</MerchantIdentifier></Header><MessageType>Product</MessageType>';
        foreach ($product->variants as $variant) {
            $feed .= '<Message><MessageID>' . $i . '</MessageID><OperationType>Delete</OperationType><Product>';
            $feed .= '<SKU>' . $variant->sku . '</SKU></Product></Message>';
            $i++;
        }
        $feed .= '</AmazonEnvelope>';
        $feed = trim($feed);
        try {
	    sleep(120);
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
