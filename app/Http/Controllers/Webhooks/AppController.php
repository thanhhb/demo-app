<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Store;
use Illuminate\Http\Request;

class AppController extends Controller
{
    /**
     * @param Request $request
     */
    public function uninstall(Request $request)
    {
        logger($request->getContent());
        $response = json_decode($request->getContent());
        $storeId = $response->id;
    }
}
