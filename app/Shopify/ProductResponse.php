<?php

namespace App\Shopify;

final class ProductResponse
{
    private $shopifyProductId;

    private $title;

    private $vendor;

    private $productType;

    private $handle;

    private $publishedAt;

    private $tags;

    private $bodyHtml;

    private $createdAt;

    private $updatedAt;

    /**
     * @param $shopifyProductId
     * @param $title
     * @param $vendor
     * @param $productType
     * @param $handle
     * @param $publishedAt
     * @param $tags
     * @param $bodyHtml
     * @param $createdAt
     * @param $updatedAt
     */
    public function __construct(
        $shopifyProductId,
        $title,
        $vendor,
        $productType,
        $handle,
        $publishedAt,
        $tags,
        $bodyHtml = null,
        $createdAt = null,
        $updatedAt = null
    ) {
        $this->shopifyProductId = $shopifyProductId;
        $this->title = $title;
        $this->vendor = $vendor;
        $this->productType = $productType;
        $this->handle = $handle;
        $this->publishedAt = $publishedAt;
        $this->tags = $tags;
        $this->bodyHtml = $bodyHtml;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getShopifyProductId()
    {
        return $this->shopifyProductId;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @return string
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return string
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getBodyHtml()
    {
        return $this->bodyHtml;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'shopify_product_id' => $this->shopifyProductId,
            'title' => $this->title,
            'body_html' => $this->bodyHtml,
            'vendor' => $this->vendor,
            'product_type' => $this->productType,
            'tags' => $this->tags,
        ];
    }
}
