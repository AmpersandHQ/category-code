<?php
namespace Ampersand\CategoryCode\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Ampersand\CategoryCode\Attribute\Backend\Code;
use Ampersand\CategoryCode\Api\CodedCategoryRepositoryInterface;
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
        $lockName = $this->getLockName($category->getCustomAttribute(Code::CODE)->getValue(), 'save');

        if (!$this->lockService->acquireLock($lockName, 0)) {
            throw new \RuntimeException('A conflict occurred while saving the category. No changes were applied.');
        }

        try {
            $this->replaceCodesWithIds($category);
            $this->categoryRepository->save($category);
        } finally {
            $this->lockService->releaseLock($lockName);
        }
    }

    public function get($categoryCode, $storeId = null)
    {
        return $this->categoryRepository->get($this->replaceCodeWithId($categoryCode));
    }

    public function deleteByIdentifier($categoryCode)
    {
        $lockName = $this->getLockName($categoryCode, 'delete');

        if (!$this->lockService->acquireLock($lockName, 0)) {
            throw new \RuntimeException('A conflict occurred while deleting the category. No changes were applied.');
        }

        try {
            $this->categoryRepository->deleteByIdentifier($this->replaceCodeWithId($categoryCode));
        } finally {
            $this->lockService->releaseLock($lockName);
        }
    }

    private function getLockName($code, $action)
    {
        return "category_{$action}.{$code}";
    }

    private function replaceCodesWithIds(CategoryInterface $category)
    {
        if ($id = $this->replaceCodeWithId($category->getCustomAttribute(Code::CODE)->getValue())) {
            $category->setId($id);
        }

        if ($parentId = $this->replaceCodeWithId($category->getExtensionAttributes()->getParentCode())) {
            $category->setParentId($parentId);
        }
    }

    /**
     * @param $code
     * @return false|int
     */
    private function replaceCodeWithId($code)
    {
        if ($code && ($id = $this->categoryCodeRepository->getId($code))) {
            return $id;
        } else {
            return false;
        }
    }
}
