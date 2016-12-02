<?php
namespace Ampersand\CategoryCode\Api;
use Ampersand\CategoryCode\Api\Data\CodedCategoryProductLinkInterface;

/**
 * @api
 */
interface CodedCategoryLinkRepositoryInterface
{
    /**
     * Assign a product to the required category
     *
     * @param \Ampersand\CategoryCode\Api\Data\CodedCategoryProductLinkInterface $productLink
     * @return bool will returned True if assigned
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function save(CodedCategoryProductLinkInterface $productLink);

    /**
     * Remove the product assignment from the category by category code and sku
     *
     * @param string $categoryCode
     * @param string $sku
     * @return bool will returned True if products successfully deleted
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteByIds($categoryCode, $sku);
}
