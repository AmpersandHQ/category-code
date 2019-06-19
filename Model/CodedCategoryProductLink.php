<?php
namespace Ampersand\CategoryCode\Model;

use Ampersand\CategoryCode\Api\Data\CodedCategoryProductLinkInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class CodedCategoryProductLink extends AbstractExtensibleModel implements CodedCategoryProductLinkInterface
{
    public function getSku()
    {
        return $this->_getData(self::SKU);
    }

    /**
     * @return \Ampersand\CategoryCode\Api\Data\CodedCategoryProductLinkInterface
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    public function getPosition()
    {
        return $this->_getData(self::POSITION);
    }

    /**
     * @return \Ampersand\CategoryCode\Api\Data\CodedCategoryProductLinkInterface
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    public function getCategoryCode()
    {
        return $this->_getData(self::CATEGORY_CODE);
    }

    /**
     * @return \Ampersand\CategoryCode\Api\Data\CodedCategoryProductLinkInterface
     */
    public function setCategoryCode($categoryCode)
    {
        return $this->setData(self::CATEGORY_CODE, $categoryCode);
    }

    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @return \Ampersand\CategoryCode\Api\Data\CodedCategoryProductLinkInterface
     */
    public function setExtensionAttributes(\Ampersand\CategoryCode\Api\Data\CodedCategoryProductLinkExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
