<?php
namespace TIG\Postcode\Plugin\View\Page\Config;

use Magento\Framework\View\Asset\File;
use Magento\Framework\Module\ModuleList;

class Renderer
{
    const CACHE_ID = 'vendor_screenshot_css';

    protected $existingFiles = ['css/styles-l.css', 'css/styles-m.css'];

    private $config;

    /** @var ModuleList  */
    public $moduleList;

    public function __construct(
        \Magento\Framework\View\Page\Config $config,
        ModuleList $moduleList
    ){
        $this->moduleList = $moduleList;
        $this->config = $config;

    }

    /**
     * @param \Magento\Framework\View\Page\Config\Renderer $subject
     * @param $assetestlist
     *
     * @see Renderer::renderAssets()
     *
     * @return array|array[]
     */
    public function beforeRenderAssets(
        \Magento\Framework\View\Page\Config\Renderer $subject,
                                                    $assetestlist = [])
    {
        $modules = $this->getEnabledModuleList();

        $checkoutModules = [
            'Amasty_Checkout'               => 'TIG_Postcode::css/amasty_checkout.css',
            'Mageplaza_Osc'                 => 'TIG_Postcode::css/mageplaza_onestepcheckout.css',
            'Aheadworks_OneStepCheckout'    => 'TIG_Postcode::css/aheadworks_onestepcheckout.css',
            'OneStepCheckout_Iosc'          => 'TIG_Postcode::css/onestepcheckout_iosc.css',
        ];

        foreach ($checkoutModules as $key => $value) {
            if (in_array($key, $modules)){
                $this->config->addPageAsset($value);
            }
            if (in_array('TIG_Postcode',$modules)){
                $this->config->addPageAsset('TIG_Postcode::css/postcode_main.css');
                $this->config->addPageAsset('TIG_Postcode::css/postcode_nl.css');
                $this->config->addPageAsset('TIG_Postcode::css/postcode_be.css');
            }
        }

        return [$assetestlist];
    }

    /**
     * Return an array of the enabled modules (bin/magento module:status)
     *
     * @return array
     */
    private function getEnabledModuleList(){
        return $this->moduleList->getNames();
    }
}
