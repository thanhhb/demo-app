<?php

namespace App\Http\Controllers;

use App\Charge;
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

        try {
            $resp = $client->request('POST', $endpoint, [
                'headers' => [
                    'X-Shopify-Access-Token' => $userShopify->provider_token,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'recurring_application_charge' => [
                        'name' => config('app.name'),
                        'price' => 15, // config
//                    'trial_days' => 15, // config
                        'return_url' => route('shopify.buy.callback', ['storeId' => $store->id]),
                        'test' => $isDevEnv,
                    ],
                ],
            ]);

            $resBody = $resp->getBody();
            $json = json_decode($resBody->getContents(), true);

            return redirect()->away($json['recurring_application_charge']['confirmation_url']);
        } catch (Exception $ex) {
            return redirect()->route('home');
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function callback(Request $request)
    {
        $chargeId = $request->input('charge_id');

        /** @var \App\User $user */
        $user = auth()->user();
        $userToken = $user->getShopifyAccessToken();

        /** @var Store $store */
        $store = optional($user->stores)->first();
        $storeDomain = $store->domain;

        try {
            /**
             * 1. Check recurring application charge status is accepted
             */
            $client = new Client();
            $endpoint = sprintf('https://%s/admin/api/2019-07/recurring_application_charges/%s.json', $storeDomain,
                $chargeId);
            $resp = $client->request('GET', $endpoint, [
                'headers' => [
                    'X-Shopify-Access-Token' => $userToken,
                    'Content-Type' => 'application/json',
                ],
            ]);
            $statusResult = json_decode($resp->getBody()->getContents(), true);

            /**
             * 2. Activate charge
             */
            if ($statusResult['recurring_application_charge']['status'] !== 'accepted') {
                throw new Exception("Invalid charge status");
            }

            $resp = $client->request('POST',
                sprintf('https://%s/admin/api/2019-07/recurring_application_charges/%s/activate.json', $storeDomain,
                    $chargeId), [
                    'headers' => [
                        'X-Shopify-Access-Token' => $userToken,
                        'Content-Type' => 'application/json',
                    ],
                ]);

            $activationResult = json_decode($resp->getBody()->getContents(), true);

            if ($activationResult['recurring_application_charge']['status'] == 'active') {
//                Charge::create([
//                    'charge_id' => $chargeId,
//                    'is_active' => true,
//                    'install_date' => $installDate
//                ]);

                \App\Charge::create([
                    'store_id' => $store->id,
                    'name' => 'default',
                    'shopify_charge_id' => $request->get('charge_id'),
                    'shopify_plan' => $activationResult['name'],
                    'quantity' => 1,
                    'charge_type' => \App\Charge::CHARGE_RECURRING,
                    'test' => $activationResult['test'],
                    'trial_ends_at' => $activationResult['trial_ends_on'],
                ]);

                // @todo: handle count usage days and trial days.
                return redirect()->route('home');
            }
        } catch (Exception $ex) {
            // @todo: log exceptions.

            // When user uninstall app during charge processing
            if ($ex->getCode() == 401) {
                auth()->logout();
            }

            return redirect()->route('login');
        }

        return redirect()->route('home');
    }
}
