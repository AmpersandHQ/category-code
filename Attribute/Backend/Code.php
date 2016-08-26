<?php

namespace Ampersand\CategoryCode\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Catalog\Model\Category;
use Magento\Framework\DataObject;

class Code extends AbstractBackend
{
    const CODE = 'code';

    /**
     * @param DataObject|Category $object
     * @return $this
     *
     * @author Dan Kenny <dk@amp.co>
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $code = $object->getData($attributeName);

        $categoryNeedsCode = ($code === null || $code === '' || $code === false);
        if ($categoryNeedsCode && $object instanceof Category) {
            $object->setData($attributeName, $this->buildCategoryCode($object));
        }

        return $this;
    }

    /**
     * @param Category $category
     * @return string
     *
     * @author Dan Kenny <dk@amp.co>
     */
    private function buildCategoryCode(Category $category) : string
    {
        $codeParts = [];
        $parents = $category->getParentCategories();
        foreach ($parents as $parent) {
            if ($parent->getLevel() > 1 && $parent->getId() != $category->getId()) {
                $codeParts[] = $parent->getName();
            }
        }

        $codeParts[] = $category->getName();
        return $category->formatUrlKey(implode('-', $codeParts));
    }
}
