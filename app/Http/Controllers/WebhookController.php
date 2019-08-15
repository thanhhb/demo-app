<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * @param Request $request
     */
    public function productsCreate(Request $request)
    {

    }

    /**
     * @param Request $request
     */
    public function productsUpdate(Request $request)
    {
        logger($request->getContent());
    }

    /**
     * @param Request $request
     */
    public function productsDelete(Request $request)
    {

    }
}
