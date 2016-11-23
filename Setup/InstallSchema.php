<?php
namespace Ampersand\CategoryCode\Setup;

use Ampersand\CategoryCode\Model\CategoryCode;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists(CategoryCode::CATEGORY_CODE)) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable(CategoryCode::CATEGORY_CODE))
                ->addColumn(
                    CategoryCode::CATEGORY_CODE,
                    Table::TYPE_TEXT,
                    255,
                    ['primary' => true, 'nullable' => false]
                )
                ->addColumn(
                    CategoryCode::CATEGORY_ID,
                    Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false, 'unsigned' => true]
                )
                ->addForeignKey(
                    $installer->getFkName(CategoryCode::CATEGORY_CODE, CategoryCode::CATEGORY_ID, 'catalog_category_entity', 'entity_id'),
                    CategoryCode::CATEGORY_ID,
                    'catalog_category_entity',
                    'entity_id',
                    Table::ACTION_CASCADE // remove code to id mapping when a category is removed.
                )
                ->setComment(
                    'Category Code'
                );

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
