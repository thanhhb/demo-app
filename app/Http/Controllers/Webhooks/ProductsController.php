<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * @param Request $request
     */
    public function handleCreate(Request $request)
    {
        logger($request->getContent());
    }

    /**
     * @param Request $request
     */
    public function handleUpdate(Request $request)
    {
        logger($request->getContent());
    }

    /**
     * @param Request $request
     */
    public function handleDelete(Request $request)
    {
        logger($request->getContent());
    }
}
