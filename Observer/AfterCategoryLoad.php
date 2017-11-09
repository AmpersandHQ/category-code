<?php

namespace Ampersand\CategoryCode\Observer;

class AfterCategoryLoad implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Magento\Catalog\Model\Indexer\Category\Flat\State */
    private $flatState;
    /** @var \Ampersand\CategoryCode\Model\Category\ReadHandler */
    private $categoryReadHandler;

    public function __construct(
        \Magento\Catalog\Model\Indexer\Category\Flat\State $flatState,
        \Ampersand\CategoryCode\Model\Category\ReadHandler $categoryReadHandler
    ) {
        $this->flatState = $flatState;
        $this->categoryReadHandler = $categoryReadHandler;
    }

    /**
     * @inheritdoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * Work around flat table $model->load() to load category code just like EAV does
         *
         * @see \Magento\Catalog\Model\Category::_construct
         * @see \Ampersand\CategoryCode\Model\Category\ReadHandler::execute
         */
        if (!$this->flatState->isAvailable()) {
            return;
        }

        /** @var \Magento\Catalog\Model\Category $category */
        $category = $observer->getData('category');

        $this->categoryReadHandler->execute($category);
    }
}
