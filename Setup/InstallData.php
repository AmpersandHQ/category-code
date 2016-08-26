<?php

namespace Ampersand\CategoryCode\Setup;

use Ampersand\CategoryCode\Attribute\Backend\Code;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{
    /** @var EavSetupFactory */
    private $eavSetupFactory;

    /**
     * @param EavSetupFactory $eavSetupFactory
     *
     * @author Dan Kenny <dk@amp.co>
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }


    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     *
     * @author Dan Kenny <dk@amp.co>
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            Category::ENTITY,
            Code::CODE,
            [
                'backend' => Code::class,
                'global' => Attribute::SCOPE_GLOBAL,
                'input' => 'text',
                'label' => 'Code',
                'note' => 'Unique identifier',
                'required' => false,
                'type' => 'varchar',
                'unique' => true,
                'used_in_product_listing' => 1,
                'user_defined' => true,
                'visible' => true,
            ]
        );

        $entityTypeId = $eavSetup->getEntityTypeId(Category::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
        $attributeGroupId = $eavSetup->getAttributeGroupId(
            $entityTypeId,
            $attributeSetId,
            'General Information'
        );

        $eavSetup->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $attributeGroupId,
            Code::CODE,
            20
        );
    }
}
