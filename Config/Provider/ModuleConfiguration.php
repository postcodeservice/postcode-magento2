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
 * to support@postcodeservice.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Postcode\Config\Provider;

class ModuleConfiguration extends AbstractConfigProvider
{
    const XPATH_CONFIGURATION_MODUS       = 'tig_postcode/configuration/modus';
    const XPATH_CHECKOUT_COMPATIBILITY    = 'tig_postcode/configuration/checkout_compatible';
    const XPATH_MODULE_STABILITY          = 'tig_postcode/stability';
    const XPATH_SUPPORTED_MAGENTO_VERSION = 'tig_postcode/supported_magento_version';
    const XPATH_NETHERLANDS_CHECK         = 'tig_postcode/countries/enable_nl_check';
    const XPATH_BELGIUM_CHECK             = 'tig_postcode/countries/enable_be_check';

    /**
     * Should return on of these values
     *  '1' => live ||
     *  '2' => test ||
     *  '0' => off
     *
     * @param null|int $store
     * @return mixed
     */
    public function getModus($store = null)
    {
        if (!$this->isModuleOutputEnabled()) {
            return '0';
        }

        return $this->getConfigFromXpath(static::XPATH_CONFIGURATION_MODUS, $store);
    }

    /**
     * Checks if the extension is on status live
     * @param null|int $store
     * @return bool
     */
    public function isModusLive($store = null)
    {
        if ($this->getModus($store) == '1') {
            return true;
        }

        return false;
    }

    /**
     * Checks if the extension is on status test
     * @param null|int $store
     * @return bool
     */
    public function isModusTest($store = null)
    {
        if ($this->getModus($store) == '2') {
            return true;
        }

        return false;
    }

    /**
     * Checks if the extension is on status off.
     * @param null|int $store
     * @return bool
     */
    public function isModusOff($store = null)
    {
        if ($this->getModus($store) == '0' || false == $this->getModus()) {
            return true;
        }

        return false;
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getStability($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_MODULE_STABILITY, $store);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getSupportedMagentoVersions($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_SUPPORTED_MAGENTO_VERSION, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getCheckoutCompatibility($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_CHECKOUT_COMPATIBILITY, $store);
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isNLCheckEnabled($store = null)
    {
        return (bool) $this->getConfigFromXpath(static::XPATH_NETHERLANDS_CHECK, $store);
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isBECheckEnabled($store = null)
    {
        return (bool) $this->getConfigFromXpath(static::XPATH_BELGIUM_CHECK, $store);
    }
}
