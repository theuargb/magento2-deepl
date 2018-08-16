<?php
/**
 * @category  Aromicon
 * @package   Aromicon_
 * @author    Stefan Richter <richter@aromicon.de>
 * @copyright 2018 aromicon GmbH (http://www.aromicon.de)
 * @license   Commercial https://www.aromicon.de/magento-download-extensions-modules/de/license
 */

namespace Aromicon\Deepl\Block\Adminhtml\Cms\Page\Edit;

class TranslateButton extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var \Magento\Store\Api\StoreManagementInterface
     */
    protected $storeManagement;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Aromicon\Deepl\Helper\Config
     */
    protected $config;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Store\Api\StoreManagementInterface $storeManagement
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManagement,
        \Aromicon\Deepl\Helper\Config $config,
        array $data = []
    ) {
        $this->storeManagement = $storeManagement;
        $this->coreRegistry = $coreRegistry;
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Prepare button and grid
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'translate_cms_page',
            'label' => __('Translate'),
            'class' => 'add',
            'button_class' => '',
            'class_name' => \Magento\Backend\Block\Widget\Button\SplitButton::class,
            'options' => $this->_getTranslateOptions(),
        ];

        if ($this->config->hasApiKey()) {
            $this->buttonList->add('translate', $addButtonProps);
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'Add Product' split button
     *
     * @return array
     */
    protected function _getTranslateOptions()
    {
        $splitButtonOptions = [];
        $stores = $this->storeManagement->getStores();

        foreach ($stores as $key => $store) {
            $splitButtonOptions[$key] = [
                'label' => __($store->getName().' '.$this->config->getStoreLanguage($store)),
                'onclick' => "setLocation('" . $this->_getTranslateUrl($store) . "')"
            ];
        }

        return $splitButtonOptions;
    }

    /**
     * Retrieve product create url by specified product type
     *
     * @param string $type
     * @return string
     */
    protected function _getTranslateUrl($store)
    {
        return $this->getUrl(
            'aromicon_deepl/cms_page/translate',
            ['store' => $store->getId(), 'page_id' => $this->getCurrentPage()->getId()]
        );
    }

    /**
     * @return \Magento\Cms\Api\Data\PageInterface
     */
    public function getCurrentPage()
    {
        return $this->coreRegistry->registry('cms_page');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
