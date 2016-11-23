<?php
namespace Ampersand\CategoryCode\Model;

use Ampersand\CategoryCode\Api\Data\CategoryCodeInterface;
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
        CategoryCodeFactory $categoryCodeFactory
    ) {
        $this->entityManager = $entityManager;
        $this->resourceConnection = $resourceConnection;
        $this->categoryCodeFactory = $categoryCodeFactory;
    }

    public function save($code, $int)
    {
        $entity = $this->categoryCodeFactory->create()
            ->setCategoryCode($code)
            ->setCategoryId($int);
        $this->entityManager->save($entity);
    }

    public function getId($code)
    {
        $entity = $this->entityManager->load($this->categoryCodeFactory->create(), (string)$code);
        return $entity->getCategoryId();
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
}
