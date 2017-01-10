<?php
namespace Ampersand\CategoryCode\Model;

use Ampersand\CategoryCode\Api\CodedCategoryManagementInterface;
use Magento\Catalog\Api\CategoryManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class CodedCategoryManagement implements CodedCategoryManagementInterface
{
    private $categoryCodeRepository;
    private $categoryManagement;

    public function __construct(CategoryCodeRepository $codeRepository, CategoryManagementInterface $categoryManagement)
    {
        $this->categoryCodeRepository = $codeRepository;
        $this->categoryManagement = $categoryManagement;
    }

    public function move($categoryCode, $parentCode)
    {
        $categoryId = $this->categoryCodeRepository->getId($categoryCode);
        if (null === $categoryId) {
            throw new NoSuchEntityException;
        }

        $parentId = $this->categoryCodeRepository->getId($parentCode);
        if (null === $parentId) {
            throw new NoSuchEntityException;
        }

        return $this->categoryManagement->move($categoryId, $parentId);
    }
}
