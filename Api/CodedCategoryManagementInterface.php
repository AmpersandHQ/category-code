<?php
namespace Ampersand\CategoryCode\Api;

interface CodedCategoryManagementInterface
{
    /**
     * Move category
     *
     * @param string $categoryCode
     * @param string $parentCode
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function move($categoryCode, $parentCode);
}
