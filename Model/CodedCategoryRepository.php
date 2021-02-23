<?php
namespace Ampersand\CategoryCode\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Ampersand\CategoryCode\Api\CodedCategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use SnowIO\Lock\Api\LockService;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class CodedCategoryRepository implements CodedCategoryRepositoryInterface
{
    /** @var CategoryRepositoryInterface */
    private $categoryRepository;
    /** @var LockService */
    private $lockService;
    /** @var CategoryCodeRepository */
    private $categoryCodeRepository;
    /** @var CategoryCollectionFactory */
    private $categoryCollectionFactory;

    /**
     * CodedCategoryRepository constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     * @param LockService $lockService
     * @param CategoryCodeRepository $categoryCodeRepository
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        LockService $lockService,
        CategoryCodeRepository $categoryCodeRepository,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->lockService = $lockService;
        $this->categoryCodeRepository = $categoryCodeRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @param CategoryInterface $category
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
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

    /**
     * @param string $categoryCode
     * @param int $storeId
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    public function get($categoryCode, $storeId = null)
    {
        $categoryId = $this->categoryCodeRepository->getId($categoryCode);

        if (null === $categoryId) {
            throw new NoSuchEntityException;
        }

        return $this->categoryRepository->get($categoryId, $storeId);
    }

    /**
     * @param array $categoryCodes
     * @param array|null $attributesToSelect
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(array $categoryCodes, $attributesToSelect = null)
    {
        $collection = $this->categoryCollectionFactory->create();
        $categoryIds = array_values($this->categoryCodeRepository->getIds($categoryCodes));

        if ($attributesToSelect) {
            $collection->addAttributeToSelect($attributesToSelect);
        }
        $collection->addAttributeToFilter('entity_id', ['in' => $categoryIds]);

        return $collection;
    }

    /**
     * @param string $categoryCode
     * @return bool
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
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

        return true;
    }

    /**
     * @param string $code
     */
    private function acquireLock($code)
    {
        if (!$this->lockService->acquireLock($this->getLockName($code), 0)) {
            throw new \RuntimeException('A conflict occurred while saving or deleting the category. No changes were applied.');
        }
    }

    /**
     * @param string $code
     */
    private function releaseLock($code)
    {
        $this->lockService->releaseLock($this->getLockName($code));
    }

    /**
     * @param string $code
     * @return string
     */
    private function getLockName($code)
    {
        return "category.{$code}";
    }

    /**
     * @param string $categoryCode
     * @param CategoryInterface $category
     */
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
