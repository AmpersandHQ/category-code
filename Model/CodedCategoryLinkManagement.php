<?php
namespace Ampersand\CategoryCode\Model;

class CodedCategoryLinkManagement implements \Ampersand\CategoryCode\Api\CodedCategoryLinkManagementInterface
{
    /** @var \Ampersand\CategoryCode\Model\CategoryCodeRepository */
    private $categoryCodeRepository;
    /** @var \Magento\Catalog\Api\CategoryLinkManagementInterface */
    private $categoryLinkManagement;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $productRepository;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement,
        \Ampersand\CategoryCode\Model\CategoryCodeRepository $categoryCodeRepository
    ) {
        $this->categoryCodeRepository = $categoryCodeRepository;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritdoc
     */
    public function assignProductToCategories($productSku, array $categoryCodes)
    {
        $categoryCodes = array_unique($categoryCodes);

        /** @var array $categoryIds */
        $categoryIds = $this->categoryCodeRepository->getIds($categoryCodes);

        $codes = array_keys($categoryIds);
        $missingCodes = array_diff($categoryCodes, $codes);

        if (!empty($missingCodes)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('Given category codes [%1], can not be found in the system', implode(", ", $missingCodes))
            );
        }

        // Assert product exists. Not found exception will be thrown if not exists
        $this->productRepository->get($productSku);

        return $this->categoryLinkManagement->assignProductToCategories($productSku, $categoryIds);
    }

    /**
     * @inheritdoc
     */
    public function assignProductsToCategory(array $productSkus, $categoryCode)
    {
        $categoryId = $this->categoryCodeRepository->getId($categoryCode);

        if (empty($categoryId)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Given category code can not be found in the system'));
        }

        // Assert product exists. Not found exception will be thrown if not exists
        array_walk($productSkus, [$this->productRepository, 'get']);

        foreach ($productSkus as $productSku) {
            $this->categoryLinkManagement->assignProductToCategories($productSku, $categoryId);
        }

        return true;
    }
}
