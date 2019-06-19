<?php
namespace Ampersand\CategoryCode\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
interface CodedCategoryProductLinkInterface extends ExtensibleDataInterface
{
    const SKU = 'sku';
    const POSITION = 'position';
    const CATEGORY_CODE = 'category_code';

    /**
     * @return string|null
     */
    public function getSku();

    /**
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * @return int|null
     */
    public function getPosition();

    /**
     * @param int $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * Get category code
     *
     * @return string
     */
    public function getCategoryCode();

    /**
     * Set category code
     *
     * @param string $categoryCode
     * @return $this
     */
    public function setCategoryCode($categoryCode);

    /**
     * Retrieve existing extension attributes object.
     *
     * @return \Ampersand\CategoryCode\Api\Data\CodedCategoryProductLinkExtensionInterface|\Magento\Framework\Api\ExtensionAttributesInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Ampersand\CategoryCode\Api\Data\CodedCategoryProductLinkExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Ampersand\CategoryCode\Api\Data\CodedCategoryProductLinkExtensionInterface $extensionAttributes
    );
}
