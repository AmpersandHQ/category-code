<?php
namespace Ampersand\CategoryCode\Model;

use Ampersand\CategoryCode\Api\Data\CategoryCodeInterface;
use Ampersand\CategoryCode\Api\Data\CategoryCodeInterfaceFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\EntityManager;

class CategoryCodeRepository
{
    private $entityManager;
    private $resourceConnection;
    private $categoryCodeFactory;

    public function __construct(
        EntityManager $entityManager,
        ResourceConnection $resourceConnection,
        CategoryCodeInterfaceFactory $categoryCodeFactory
    ) {
        $this->entityManager = $entityManager;
        $this->resourceConnection = $resourceConnection;
        $this->categoryCodeFactory = $categoryCodeFactory;
    }

    public function save($code, $int)
    {
        $this->validateCategoryCode($code);
        $entity = $this->categoryCodeFactory->create()
            ->setCategoryCode($code)
            ->setCategoryId($int);
        $this->entityManager->save($entity);
    }

    public function getId($code)
    {
        $this->validateCategoryCode($code);
        $entity = $this->entityManager->load($this->categoryCodeFactory->create(), (string)$code);
        return $entity->getCategoryId();
    }

    /**
     * Get a list of category ids of given codes
     *
     * @param array $codes
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getIds(array $codes)
    {
        array_walk($codes, [$this, 'validateCategoryCode']);

        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(CategoryCodeInterface::CATEGORY_CODE)
            ->where(CategoryCodeInterface::CATEGORY_CODE . ' IN (?)', $codes);

        $records = $connection->fetchAll($select);

        $idByCode = [];
        foreach ($records as $record) {
            $idByCode[$record[\Ampersand\CategoryCode\Api\Data\CategoryCodeInterface::CATEGORY_CODE]] = $record[\Ampersand\CategoryCode\Api\Data\CategoryCodeInterface::CATEGORY_ID];
        }

        return $idByCode;
    }

    public function getCode($id)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(CategoryCodeInterface::CATEGORY_CODE)
            ->where(CategoryCodeInterface::CATEGORY_ID . ' = ?', (int)$id);

        $record = $connection->fetchRow($select);

        return isset($record[CategoryCodeInterface::CATEGORY_CODE])
            ? $record[CategoryCodeInterface::CATEGORY_CODE] : null;
    }

    private function validateCategoryCode($categoryCode)
    {
        if ('' === $categoryCode || null === $categoryCode) {
            throw new \Exception('Missing required code.');
        }

        if (!\is_string($categoryCode) && !\is_int($categoryCode)) {
            throw new \InvalidArgumentException('Category code must be a string or an int.');
        }
    }
}
