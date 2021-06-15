<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@postcodeservice.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.com for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Postcode\Config\Provider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Module\Manager;
use Magento\Store\Model\StoreManagerInterface;
use TIG\Postcode\Config\CheckoutConfiguration\CheckoutConfigurationInterface;
use TIG\Postcode\Exception;

class CheckoutConfiguration extends AbstractConfigProvider implements ConfigProviderInterface
{
    const XPATH_POSTCODE_SORT_ORDER = 'tig_postcode/checkout/postcode_sort_order';
    const XPATH_CITY_SORT_ORDER     = 'tig_postcode/checkout/city_sort_order';
    const XPATH_COUNTRY_SORT_ORDER  = 'tig_postcode/checkout/country_sort_order';

    /**
     * @var array
     */
    private $postcodeConfiguration;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Manager $moduleManager,
        Encryptor $crypt,
        StoreManagerInterface $storeManager,
        $postcodeConfiguration = []
    ) {
        $this->postcodeConfiguration = $postcodeConfiguration;
        parent::__construct($scopeConfig, $moduleManager, $crypt, $storeManager);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        foreach ($this->postcodeConfiguration as $key => $configuration) {
            $this->checkImplementation($configuration, $key);
            $config[$key] = $configuration->getValue();
        }

        return [
            'postcode' => $config
        ];
    }

    /**
     * @param $configuration
     * @param $key
     *
     * @throws Exception
     */
    private function checkImplementation($configuration, $key)
    {
        if (!($configuration instanceof CheckoutConfigurationInterface)) {
            // @codingStandardsIgnoreLine
            throw new Exception(__('%1 is not an implementation of %2', $key, CheckoutConfigurationInterface::class));
        }
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getPostcodeSortOrder($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_POSTCODE_SORT_ORDER, $store);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getCitySortOrder($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_CITY_SORT_ORDER, $store);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getCountrySortOrder($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_COUNTRY_SORT_ORDER, $store);
    }
}
