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
        $categoryIds = $this->categoryCodeRepository->getIds($categoryCodes);

        if (empty($categoryIds)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Given category codes can not be found in the system'));
        }

        // Assert product exists. Not found exception will be thrown if not exists
        $this->productRepository->get($productSku);

        return $this->categoryLinkManagement->assignProductToCategories($productSku, $categoryIds);
    }
}
