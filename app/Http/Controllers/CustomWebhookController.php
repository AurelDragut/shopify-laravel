<?php

namespace App\Http\Controllers;

use App\Jobs\ProductsCreateJob;
use App\Jobs\ProductsDeleteJob;
use App\Jobs\ProductsUpdateJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomWebhookController extends Controller
{
    /**
     * Handles an incoming webhook.
     *
     * @param string $type The type of webhook
     *
     * @return \Illuminate\Http\Response
     */
    public function handle($type)
    {
        define('SHOPIFY_APP_SECRET', '6573851be9c4c8f5c0530b1856d735b0d0ebbf1c0a1060e3d5fe745ff42b7008');

        Log::alert('Handle Activated'. $type);

        function verify_webhook($data, $hmac_header)
        {
            $calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_APP_SECRET, true));
            return hash_equals($hmac_header, $calculated_hmac);
        }


        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $data = file_get_contents('php://input');
        $verified = verify_webhook($data, $hmac_header);
        Log::info('Webhook verified: '.var_export($verified, true)); //check error.log to see the result

        $classPath = $this->getJobClassFromType($type);
        if (!class_exists($classPath)) {
            // Can not find a job for this webhook type
            Log::alert("Missing webhook job: {$classPath}");
        }

        // Dispatch
        $shopDomain = request()->header('x-shopify-shop-domain');
        $data = json_decode(request()->getContent());
        dispatch(new $classPath($shopDomain, $data))->delay(now()->addSecond());

        return response('', 201);
    }

    /**
     * Converts type into a class string.
     *
     * @param string $type The type of webhook
     *
     * @return string
     */
    protected function getJobClassFromType($type)
    {
        Log::info('\\App\\Jobs\\'.str_replace('-', '', ucwords($type, '-')).'Job');
        return '\\App\\Jobs\\'.str_replace('-', '', ucwords($type, '-')).'Job';
    }
}
