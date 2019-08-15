<?php
namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Route;
use Socialite;

class LoginShopifyController extends Controller
{
    /**
     * Redirect to provider.
     *
     * @param Request $request
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function redirectToProvider(Request $request)
    {
        $this->validate($request, [
            'domain' => 'string|required',
        ]);

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
        if (!$shopifyUser) {
            return redirect('/');
        }

        $user = User::firstOrCreate([
            'name' => $shopifyUser->nickname,
            'email' => $shopifyUser->email,
            'password' => '',
        ]);

        $shopDomain = $shopifyUser->user['domain'] ?? '';
        $shopToken = $shopifyUser->accessTokenResponseBody["access_token"] ?? '';
        if (!$shopDomain || !$shopToken) {
            return redirect('/');
        }

        $this->registerWebhooks($shopDomain, $shopToken);
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

                logger("Registered web hook: " .  json_encode($requestParam));
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
