<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Socialite;
use Auth;
use App\User;

class LoginShopifyController extends Controller
{
    /**
     * Redirect to provider
     *
     * @param Request $request
     * @return void
     */
    public function redirectToProvider(Request $request) {
        $this->validate($request, [
            'domain' => 'string|required'
        ]);

        $config = new \SocialiteProviders\Manager\Config(
            env('SHOPIFY_KEY'),
            env('SHOPIFY_SECRET'),
            env('SHOPIFY_REDIRECT'),
            ['subdomain' => $request->get('domain')]
        );

        return Socialite::with('shopify')
            ->setConfig($config)
            ->scopes(['read_products','write_products'])
            ->redirect();
    }

    /**
     * Handle shopify callback.
     */
    public function handleProviderCallback()
    {
        $shopifyUser = Socialite::driver('shopify')->user();

        $user = User::firstOrCreate([
            'name' => $shopifyUser->nickname,
            'email' => $shopifyUser->email,
            'password' => '',
        ]);

        Auth::login($user, true);

        return redirect('/home');
    }
}
