<?php

namespace JustCoded\BackInStockConfigurable\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface as ModuleContext;
use Magento\Framework\Setup\ModuleDataSetupInterface as ModuleDataSetup;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\BlockRepository;

class InstallData implements InstallDataInterface
{
    const DEFAULT_POPUP_HEADER_CMS_BLOCK_ID = 'back-in-stock-configurable-popup-header';
    const DEFAULT_POPUP_HEADER_CMS_BLOCK_CONTENT = '<div class="back-in-stock-notify-popup-header" style="font-weight: 200;line-height: 1.2;font-size: 1.6rem;margin-bottom: 15px;">Please select the options and enter your email, and we will notify you once this product is back in stock.</div>';
    const DEFAULT_POPUP_HEADER_CMS_BLOCK_TITLE = 'Back In Stock Configurable Popup Header';

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var BlockRepository
     */
    private $blockRepository;

    public function __construct(
        BlockFactory $blockFactory,
        BlockRepository $blockRepository
    ) {
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
    }

    /**
     * @param ModuleDataSetup $installer
     */
    public function installPopupCmsBlocks(ModuleDataSetup $installer)
    {
        $block = $this->blockFactory->create()->setData([
            'identifier' => self::DEFAULT_POPUP_HEADER_CMS_BLOCK_ID,
            'content'    => self::DEFAULT_POPUP_HEADER_CMS_BLOCK_CONTENT,
            'title'      => self::DEFAULT_POPUP_HEADER_CMS_BLOCK_TITLE,
            'is_active'  => 1,
            'stores'     => [0]
        ]);

        $this->blockRepository->save($block);
    }

    /**
     * @inheritdoc
     */
    public function install(ModuleDataSetup $setup, ModuleContext $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->installPopupCmsBlocks($installer);

        $installer->endSetup();
    }
}
