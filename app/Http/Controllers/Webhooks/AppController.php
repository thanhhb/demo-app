<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppController extends Controller
{
    /**
     * @param Request $request
     */
    public function uninstall(Request $request)
    {
        logger($request->getContent());
    }
}
