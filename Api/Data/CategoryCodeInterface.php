<?php
namespace Ampersand\CategoryCode\Api\Data;

/**
 * @api
 */
interface CategoryCodeInterface
{
    const CATEGORY_CODE = 'category_code';
    const CATEGORY_ID = 'category_id';

    /**
     * @return string|null
     */
    public function getCategoryCode();

    /**
     * @param string $code
     * @return $this
     */
    public function setCategoryCode($code);

    /**
     * @return int|null
     */
    public function getCategoryId();

    /**
     * @param int $id
     * @return $this
     */
    public function setCategoryId($id);
}
