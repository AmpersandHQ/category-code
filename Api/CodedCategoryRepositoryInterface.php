<?php
namespace Ampersand\CategoryCode\Api;

use Magento\Catalog\Api\Data\CategoryInterface;

/**
 * @api
 */
interface CodedCategoryRepositoryInterface
{
    /**
     * Create category service
     *
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \RuntimeException when a conflict occurs
     */
    public function save(CategoryInterface $category);

    /**
     * Get info about category by category code
     *
     * @param string $categoryCode
     * @param int $storeId
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($categoryCode, $storeId = null);

    /**
     * Delete category by code
     *
     * @param string $categoryCode
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \RuntimeException when a conflict occurs
     */
    public function deleteByIdentifier($categoryCode);
}
