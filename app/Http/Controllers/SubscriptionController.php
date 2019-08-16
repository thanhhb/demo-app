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
     * @return \Illuminate\Http\RedirectResponse
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
                    'name' => config('app.name'),
                    'price' => 15, // config
                    'trial_days' => 15, // config
                    'return_url' => route('shopify.buy.callback', ['storeId' => $store->id]),
                    'test' => $isDevEnv,
                ],
            ],
        ]);

        $resBody = $resp->getBody();
        $json = json_decode($resBody->getContents(), true);

        return redirect()->away($json['recurring_application_charge']['confirmation_url']);
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function callback(Request $request)
    {
        $chargeId = $request->input('charge_id');

        /** @var User $user */
        $user = auth()->user();
        $userToken = $user->getShopifyAccessToken();

        /** @var Store $store */
        $store = optional($user->stores)->first();
        $storeDomain = $store->domain;

        try {
            $client = new Client();
            $endpoint = sprintf('https://%s/admin/api/2019-07/recurring_application_charges/%s.json', $storeDomain, $chargeId);
            $resp = $client->request('GET', $endpoint, [
                'headers' => [
                    'X-Shopify-Access-Token' => $userToken,
                    'Content-Type' => 'application/json'
                ]
            ]);

            $resBody = $resp->getBody();
            $json = json_decode($resBody->getContents(), true);

            dd($json);
        } catch(Exception $ex) {

        }
    }
}
