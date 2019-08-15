<?php

namespace App\Shopify;

final class ProductResponse
{
    private $id;

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
     * @param $id
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
        $id,
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
        $this->id = $id;
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @return mixed
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return mixed
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return null
     */
    public function getBodyHtml()
    {
        return $this->bodyHtml;
    }

    /**
     * @return null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return null
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
            'shopify_product_id' => $this->id,
            'title' => $this->title,
            'body_html' => $this->bodyHtml,
            'vendor' => $this->vendor,
            'product_type' => $this->productType,
            'tags' => $this->tags,
        ];
    }
}
