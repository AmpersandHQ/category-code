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
}
