<?php
namespace Ampersand\CategoryCode\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Ampersand\CategoryCode\Api\CodedCategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use SnowIO\Lock\Api\LockService;

class CodedCategoryRepository implements CodedCategoryRepositoryInterface
{
    private $categoryRepository;
    private $lockService;
    private $categoryCodeRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        LockService $lockService,
        CategoryCodeRepository $categoryCodeRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->lockService = $lockService;
        $this->categoryCodeRepository = $categoryCodeRepository;
    }

    public function save(CategoryInterface $category)
    {
        if (null === $extensionAttributes = $category->getExtensionAttributes()) {
            throw new \Exception("Missing extension attributes.");
        }

        if (null === $categoryCode = $extensionAttributes->getCode()) {
            throw new \Exception("Missing required attribute 'code'.");
        }

        $this->acquireLock($categoryCode);

        try {
            $this->replaceCodesWithIds($categoryCode, $category);
            $this->categoryRepository->save($category);
        } finally {
            $this->releaseLock($categoryCode);
        }
    }

    public function get($categoryCode, $storeId = null)
    {
        $categoryId = $this->categoryCodeRepository->getId($categoryCode);

        if (null === $categoryId) {
            throw new NoSuchEntityException;
        }

        return $this->categoryRepository->get($categoryId, $storeId);
    }

    public function deleteByIdentifier($categoryCode)
    {
        $this->acquireLock($categoryCode);

        try {
            if (null !== $categoryId = $this->categoryCodeRepository->getId($categoryCode)) {
                $this->categoryRepository->deleteByIdentifier($categoryId);
            }
        } finally {
            $this->releaseLock($categoryCode);
        }
    }

    private function acquireLock($code)
    {
        if (!$this->lockService->acquireLock($this->getLockName($code), 0)) {
            throw new \RuntimeException('A conflict occurred while saving or deleting the category. No changes were applied.');
        }
    }

    private function releaseLock($code)
    {
        $this->lockService->releaseLock($this->getLockName($code));
    }

    private function getLockName($code)
    {
        return "category.{$code}";
    }

    private function replaceCodesWithIds($categoryCode, CategoryInterface $category)
    {
        if (null !== $categoryId = $this->categoryCodeRepository->getId($categoryCode)) {
            $category->setId($categoryId);
        }

        if ($extensionAttributes = $category->getExtensionAttributes()) {
            if (null !== $parentCode = $extensionAttributes->getParentCode()) {
                if (null === $parentId = $this->categoryCodeRepository->getId($parentCode)) {
                    throw new \RuntimeException("Parent category with code '$parentCode' does not exist.");
                }
                $category->setParentId($parentId);
            }
        }
    }
}
