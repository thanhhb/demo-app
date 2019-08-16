<?php
namespace App\Http\Controllers\Auth;

use App\Store;
use App\UserProvider;
use Auth;
use App\User;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Route;
use Socialite;

class LoginShopifyController extends Controller
{
    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'domain' => ['required'],
        ]);
    }

    /**
     * Redirect to provider.
     *
     * @param Request $request
     * @return void
     */
    public function redirectToProvider(Request $request)
    {
        $providerConfig = new \SocialiteProviders\Manager\Config(
            config('shopify.key'),
            config('shopify.secret'),
            config('shopify.redirect'),
            [
                'subdomain' => $request->get('domain'),
            ]
        );

        $shopifyScopes = config('shopify.scopes');

        return Socialite::with('shopify')
            ->setConfig($providerConfig)
            ->scopes($shopifyScopes)
            ->redirect();
    }

    /**
     * Handle Shopify callback.
     */
    public function handleProviderCallback()
    {
        $shopifyUser = Socialite::driver('shopify')->user();

        if (!$shopifyUser || !$shopifyUser->id) {
            return redirect('/');
        }

        $user = User::firstOrCreate([
            'name' => $shopifyUser->nickname,
            'email' => $shopifyUser->email,
            'password' => '',
        ]);

        UserProvider::updateOrCreate([
            'user_id' => $user->id,
            'provider' => 'shopify',
            'provider_user_id' => $shopifyUser->id
        ], [
            'provider_token' => $shopifyUser->token,
        ]);

        $store = Store::firstOrCreate([
            'name' => $shopifyUser->name,
            'domain' => $shopifyUser->nickname,
        ]);

        $store->users()->syncWithoutDetaching([$user->id]);

        $this->registerWebhooks($shopifyUser->nickname, $shopifyUser->token);
        Auth::login($user, true);

        return redirect('/home');
    }

    /**
     * Register web hooks
     *
     * @param $shopDomain
     * @param $accessToken
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function registerWebhooks($shopDomain, $accessToken)
    {
        $webHookRoutes = config('shopify.webhook_routes');
        $webhookVersion = config('shopify.webhook_version');
        $registrationEndpoint = config('shopify.webhook_registration_endpoint');
        $endpoint = sprintf($registrationEndpoint, $shopDomain, $webhookVersion);

        $client = new Client();
        foreach ($webHookRoutes as $topic => $webHookRoute) {
            if (!Route::has($webHookRoute)) {
                continue;
            }

            $addressUrl = config('shopify.webhook_url') . route($webHookRoute, [], false);
            $requestParam = [
                'headers' => [
                    'X-Shopify-Access-Token' => $accessToken,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'webhook' => [
                        "topic" => $topic,
                        "address" => $addressUrl,
                        "format" => "json",
                    ],
                ],
            ];

            try {
                $client->request('POST', $endpoint, $requestParam);

                logger("Registered web hook: " . json_encode($requestParam));
            } catch (ClientException $ex) {
                // 422 status code: webhook had already registered, ignore exception
                if ($ex->getCode() != 422) {
                    Auth::logout();

                    return redirect()->route('identity-sign-in');
                }
            }
        }
    }
}
