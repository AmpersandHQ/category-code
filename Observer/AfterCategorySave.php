<?php
namespace Ampersand\CategoryCode\Observer;

use Magento\Framework\App\DeploymentConfig;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Ampersand\CategoryCode\Model\CategoryCodeRepository;

class AfterCategorySave implements ObserverInterface
{
    private $categoryCodeRepository;
    private $deploymentConfig;

    public function __construct(CategoryCodeRepository $categoryCodeRepository, DeploymentConfig $deploymentConfig)
    {
        $this->categoryCodeRepository = $categoryCodeRepository;
        $this->deploymentConfig = $deploymentConfig;
    }

    public function execute(Observer $observer)
    {
        /** @var CategoryInterface $category */
        $category = $observer->getDataByKey('entity');

        if ($this->deploymentConfig->isAvailable() && $extensionAttributes = $category->getExtensionAttributes()) {
            if (null !== $code = $extensionAttributes->getCode()) {
                $this->categoryCodeRepository->save($code, $category->getId());
            }
        }
    }
}
