<?php
namespace Ampersand\CategoryCode\Model;

use Ampersand\CategoryCode\Api\Data\CategoryCodeInterface;
use Magento\Framework\DataObject;

class CategoryCode extends DataObject implements CategoryCodeInterface
{
    public function getCategoryCode()
    {
        return $this->getData(self::CATEGORY_CODE);
    }

    public function setCategoryCode($code)
    {
        return $this->setData(self::CATEGORY_CODE, $code);
    }

    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    public function setCategoryId($id)
    {
        return $this->setData(self::CATEGORY_ID, $id);
    }
}
