<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Product;
use App\Shopify\ProductResponse;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Handle new product is created.
     *
     * @param Request $request
     */
    public function handleCreate(Request $request)
    {
        $response = json_decode($request->getContent());
        $shopifyProductResponse = new ProductResponse(
            $response->id,
            $response->title,
            $response->vendor,
            $response->product_type,
            $response->handle,
            $response->published_at,
            $response->tags,
            $response->body_html,
            $response->created_at,
            $response->updated_at
        );

        Product::updateOrCreate(
            ['id' => $shopifyProductResponse->getId()],
            $shopifyProductResponse->toArray()
        );
    }

    /**
     * Handle product is updated.
     *
     * @param Request $request
     */
    public function handleUpdate(Request $request)
    {
        $response = json_decode($request->getContent());
        $shopifyProductResponse = new ProductResponse(
            $response->id,
            $response->title,
            $response->vendor,
            $response->product_type,
            $response->handle,
            $response->published_at,
            $response->tags,
            $response->body_html,
            $response->created_at,
            $response->updated_at
        );

        Product::updateOrCreate(
            [
                'shopify_product_id' => $shopifyProductResponse->getId()
            ],
            $shopifyProductResponse->toArray()
        );
    }

    /**
     * Handle product is deleted.
     *
     * @param Request $request
     */
    public function handleDelete(Request $request)
    {
        $deletedProduct = $request->getContent();

        Product::destroy($deletedProduct->id);
    }
}
