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

class ModuleConfiguration extends AbstractConfigProvider
{
    public const XPATH_CONFIGURATION_MODUS       = 'tig_postcode/configuration/modus';

    public const XPATH_CHECKOUT_COMPATIBILITY    = 'tig_postcode/configuration/checkout_compatible';

    public const XPATH_MODULE_STABILITY          = 'tig_postcode/stability';

    public const XPATH_SUPPORTED_MAGENTO_VERSION = 'tig_postcode/supported_magento_version';

    public const XPATH_NETHERLANDS_CHECK         = 'tig_postcode/countries/enable_nl_check';

    public const XPATH_BELGIUM_CHECK             = 'tig_postcode/countries/enable_be_check';

    public const XPATH_GERMANY_CHECK             = 'tig_postcode/countries/enable_de_check';

    /**
     * Should return on of these values
     *  '1' => live ||
     *  '2' => test ||
     *  '0' => off
     *
     * @param string|int|null $store
     *
     * @return mixed
     */
    public function getModus(int|string|null $store = null)
    {
        if (!$this->isModuleOutputEnabled()) {
            return '0';
        }

        return $this->getConfigFromXpath(static::XPATH_CONFIGURATION_MODUS, $store);
    }

    /**
     * Checks if the extension is on status live via store ID
     *
     * @param string|int|null $store
     *
     * @return bool
     */
    public function isModusLive(int|string|null $store = null): bool
    {
        if ($this->getModus($store) == '1') {
            return true;
        }

        return false;
    }

    /**
     * Checks if the extension is on status test via store ID
     *
     * @param int|string|null $store
     *
     * @return bool
     */
    public function isModusTest(int|string|null $store = null): bool
    {
        if ($this->getModus($store) == '2') {
            return true;
        }

        return false;
    }

    /**
     * Checks if the extension is on status off via store ID.
     *
     * @param string|int|null $store
     *
     * @return bool
     */
    public function isModusOff(int|string|null $store = null): bool
    {
        if ($this->getModus($store) == '0' || empty($this->getModus())) {
            return true;
        }

        return false;
    }

    /**
     * Get stability via store ID
     *
     * @param string|int|null $store
     *
     * @return string
     */
    public function getStability(int|string|null $store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_MODULE_STABILITY, $store);
    }

    /**
     * Get supported magento versions via store ID
     *
     * @param string|int|null $store
     *
     * @return string
     */
    public function getSupportedMagentoVersions($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_SUPPORTED_MAGENTO_VERSION, $store);
    }

    /**
     * Get the Checkout compatability via store ID
     *
     * @param string|int|null $store
     *
     * @return mixed
     * @deprecated
     */
    public function getCheckoutCompatibility($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_CHECKOUT_COMPATIBILITY, $store);
    }

    /**
     * Check if NL is enabled via store ID
     *
     * @param string|int|null $store
     *
     * @return bool
     */
    public function isNLCheckEnabled($store = null): bool
    {
        return (bool) $this->getConfigFromXpath(static::XPATH_NETHERLANDS_CHECK, $store);
    }

    /**
     * Check if BE is enabled via store ID
     *
     * @param string|int|null $store
     *
     * @return bool
     */
    public function isBECheckEnabled($store = null): bool
    {
        return (bool) $this->getConfigFromXpath(static::XPATH_BELGIUM_CHECK, $store);
    }

    /**
     * Check if DE is enabled via store ID
     *
     * @param string|int|null $store
     *
     * @return bool
     */
    public function isDECheckEnabled($store = null): bool
    {
        return (bool) $this->getConfigFromXpath(static::XPATH_GERMANY_CHECK, $store);
    }
}
