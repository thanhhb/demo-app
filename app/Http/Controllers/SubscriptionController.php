<?php

namespace App\Http\Controllers;

use App\Store;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SubscriptionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * @param int $storeId
     * @throws Exception
     * @throws GuzzleException
     */
    public function index(Request $request, $storeId)
    {
        $store = Store::find($storeId);
        if (!$store) {
            throw new Exception("Store not found.");
        }

        $user = auth()->user();
        $userShopify = $user->providers->where('provider', 'shopify')->first();

        $isDevEnv = false;
        if (App::environment(['local', 'staging'])) {
            $isDevEnv = true;
        }

        $client = new Client();
        $endpoint = sprintf('https://%s/admin/api/2019-07/recurring_application_charges.json', $store->domain);

        $resp = $client->request('POST', $endpoint, [
            'headers' => [
                'X-Shopify-Access-Token' => $userShopify->provider_token,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'recurring_application_charge' => [
                    "name" => trans('app.charge_name', ['appTitle' => config('app.name')]),
                    "price" => 15, // config
                    "trial_days" => 15, // config
                    "return_url" => route('shopify.buy.callback', ['storeId' => $store->id]),
                    "test" => $isDevEnv,
                ],
            ],
        ]);

        $resBody = $resp->getBody();
        dd($resBody);
        $json = json_decode($resBody->getContents(), true);
    }
}
