<?php
namespace Ampersand\CategoryCode\Model;

use Ampersand\CategoryCode\Api\CodedCategoryLinkRepositoryInterface;
use Ampersand\CategoryCode\Api\Data\CodedCategoryProductLinkInterface;
use Magento\Catalog\Api\CategoryLinkRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryProductLinkExtensionFactory;
use Magento\Catalog\Api\Data\CategoryProductLinkInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class CodedCategoryLinkRepository implements CodedCategoryLinkRepositoryInterface
{
    private $categoryLinkRepository;
    private $categoryCodeRepository;
    private $categoryProductLinkFactory;
    private $categoryProductLinkExtensionFactory;

    public function __construct(
        CategoryLinkRepositoryInterface $categoryLinkRepository,
        CategoryCodeRepository $categoryCodeRepository,
        CategoryProductLinkInterfaceFactory $categoryProductLinkFactory,
        CategoryProductLinkExtensionFactory $categoryProductLinkExtensionFactory
    ) {
        $this->categoryLinkRepository = $categoryLinkRepository;
        $this->categoryCodeRepository = $categoryCodeRepository;
        $this->categoryProductLinkFactory = $categoryProductLinkFactory;
        $this->categoryProductLinkExtensionFactory = $categoryProductLinkExtensionFactory;
    }

    public function save(CodedCategoryProductLinkInterface $productLink)
    {
        $categoryId = $this->categoryCodeRepository->getId($productLink->getCategoryCode());

        if (null === $categoryId) {
            throw new NoSuchEntityException;
        }

        $categoryProductLink = $this->categoryProductLinkFactory->create()
            ->setCategoryId($categoryId)
            ->setSku($productLink->getSku())
            ->setPosition($productLink->getPosition())
            ->setExtensionAttributes($this->categoryProductLinkExtensionFactory->create());

        return $this->categoryLinkRepository->save($categoryProductLink);
    }

    public function deleteByIds($categoryCode, $sku)
    {
        $categoryId = $this->categoryCodeRepository->getId($categoryCode);

        return $this->categoryLinkRepository->deleteByIds($categoryId, $sku);
    }
}
