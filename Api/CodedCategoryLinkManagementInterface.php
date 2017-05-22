<?php
namespace Ampersand\CategoryCode\Api;

/**
 * @api
 */
interface CodedCategoryLinkManagementInterface
{
    /**
     * Assign product to given categories
     *
     * @param string $productSku
     * @param string[] $categoryCodes
     * @return bool
     */
    public function assignProductToCategories($productSku, array $categoryCodes);

    /**
     * Assign products to given category.
     *
     * Creating all product-category associations and assuming there are P number of products and
     * C number of categories.
     *
     * Setting up complete associations requires up to O(C) requests this API while the vanilla
     * requires up to O(P) requests.
     *
     * Beside the dramatically saving on the number of request, the operation time of each request
     * is also highly reduced.
     *
     * This is base on the operation time required by saving a category link (i.e., by saving
     * a category model). More than 70% of cost is used by running:
     * \Magento\Catalog\Model\Category::save
     *
     * @see \Magento\Catalog\Model\CategoryLinkRepository::save
     *
     * For each O(C) request, it saves against one category and create a O(1) operation time.
     * So it requires O(C * 1)
     *
     * For each O(P) request, it saves against C category and create a O(C) operation time.
     * So it requires O(P * C)
     *
     * @param string[] $productSkus
     * @param string $categoryCode
     *
     * @return bool
     */
    public function assignProductsToCategory(array $productSkus, $categoryCode);
}
