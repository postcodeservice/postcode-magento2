<?php
namespace TIG\Postcode\Plugin\View\Page\Config;

use Magento\Framework\Module\ModuleList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Page\Config;

class Renderer
{
    /** @var Config  */
    private $config;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var ModuleList  */
    public $moduleList;

    /**
     * @param \Magento\Framework\View\Page\Config   $config
     * @param ScopeConfigInterface                  $scopeConfig
     * @param ModuleList                            $moduleList
     */
    public function __construct(
        Config                  $config,
        ScopeConfigInterface    $scopeConfig,
        ModuleList              $moduleList
    ){
        $this->config       = $config;
        $this->scopeConfig  = $scopeConfig;
        $this->moduleList   = $moduleList;
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
        $modules = $this->moduleList->getNames();

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
                // check if NL is enabled
                if ($this->scopeConfig->getValue('tig_postcode/countries/enable_nl_check',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
                    $this->config->addPageAsset('TIG_Postcode::css/postcode_nl.css');
                }
                // check if BE is enabled
                if ($this->scopeConfig->getValue('tig_postcode/countries/enable_be_check',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
                    $this->config->addPageAsset('TIG_Postcode::css/postcode_be.css');
                }
            }
        }

        return [$assetestlist];
    }
}
