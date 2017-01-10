<?php
namespace Ampersand\CategoryCode\Observer;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Ampersand\CategoryCode\Model\CategoryCodeRepository;

class AfterCategorySave implements ObserverInterface
{
    private $categoryCodeRepository;

    public function __construct(CategoryCodeRepository $categoryCodeRepository)
    {
        $this->categoryCodeRepository = $categoryCodeRepository;
    }

    public function execute(Observer $observer)
    {
        /** @var CategoryInterface $category */
        $category = $observer->getDataByKey('entity');

        $this->categoryCodeRepository->save($category->getExtensionAttributes()->getCode(), $category->getId());
    }
}
