<?php

namespace JustCoded\BackInStockConfigurable\Setup;

use JustCoded\BackInStockConfigurable\Model\ResourceModel\Subscription;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface as ModuleContext;
use Magento\Framework\Setup\SchemaSetupInterface as SchemaSetup;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetup $installer
     */
    private function installSubscriptionsTable(SchemaSetup $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(Subscription::TABLE_NAME)
        )->addColumn(
            Subscription::ID_FIELD_NAME,
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ],
            'Entity ID'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ],
            'Email'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            255,
            [ 'nullable' => false, ],
            'Product Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Store ID'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            [ 'nullable' => false, 'default' => Table::TIMESTAMP_INIT, ],
            'Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            [ 'nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE, ],
            'Modification Time'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            [ 'nullable' => false, 'default' => '1', ],
            'Is Active'
        )->addIndex(
            $installer->getIdxName(
                Subscription::TABLE_NAME,
                ['email', 'product_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['email', 'product_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex($installer->getIdxName(Subscription::TABLE_NAME, ['store_id']), ['store_id']
        )->addForeignKey(
            $installer->getFkName(Subscription::TABLE_NAME, 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE,
            Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * @inheritdoc
     */
    public function install(SchemaSetup $setup, ModuleContext $context)
    {
        $installer = $setup;
        $installer->startSetup();
        
        $this->installSubscriptionsTable($installer);

        $installer->endSetup();
    }
}
