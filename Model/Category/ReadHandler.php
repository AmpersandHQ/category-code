<?php
namespace Ampersand\CategoryCode\Model\Category;

use Ampersand\CategoryCode\Model\CategoryCodeRepository;
use Magento\Catalog\Api\Data\CategoryExtensionFactory;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class ReadHandler implements ExtensionInterface
{
    private $categoryCodeRepository;
    private $categoryExtensionFactory;

    public function __construct(
        CategoryCodeRepository $categoryCodeRepository,
        CategoryExtensionFactory $categoryExtensionFactory
    ) {
        $this->categoryCodeRepository = $categoryCodeRepository;
        $this->categoryExtensionFactory = $categoryExtensionFactory;
    }

    /**
     * @param CategoryInterface $entity
     * @param array $arguments
     * @return object|bool
     */
    public function execute($entity, $arguments = [])
    {
        $code = $this->categoryCodeRepository->getCode($entity->getId());
        $extensionAttributes = $entity->getExtensionAttributes() ?: $this->categoryExtensionFactory->create();
        $extensionAttributes->setCode($code);
        $parentCode = $this->categoryCodeRepository->getCode($entity->getParentId());
        $extensionAttributes->setParentCode($parentCode);
        $entity->setExtensionAttributes($extensionAttributes);

        return $entity;
    }
}
