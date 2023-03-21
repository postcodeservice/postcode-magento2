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

class ApiConfiguration extends AbstractConfigProvider
{
    public const XPATH_API_BASE                 = 'tig_postcode/api/base';
    public const XPATH_API_VERSION              = 'tig_postcode/api/version';
    public const XPATH_API_TYPE                 = 'tig_postcode/api/type';

    public const XPATH_API_BE_BASE              = 'tig_postcode/api_be/base';
    public const XPATH_API_BE_POSTCODE_VERSION  = 'tig_postcode/api_be/postcode_version';
    public const XPATH_API_BE_STREET_VERSION    = 'tig_postcode/api_be/street_version';

    /**
     * Get base Uri
     *
     * @return string
     */
    public function getBaseUri()
    {
        return $this->getBase() . '/' . $this->getVersion() . '/' . $this->getType() . '/';
    }

    /**
     * Get Belgium base Uri
     *
     * @param string $endpoint
     *
     * @return string
     */
    public function getBeBaseUri($endpoint)
    {
        return $this->getBase('BE') . '/' . $this->getVersion('BE', $endpoint) . '/';
    }

    /**
     * Get base path via country and store ID
     *
     * @param string            $country
     * @param string|int|null   $store
     *
     * @return mixed
     */
    public function getBase($country = 'NL', $store = null)
    {
        $xpath = static::XPATH_API_BASE;
        if ($country == 'BE') {
            $xpath = static::XPATH_API_BE_BASE;
        }

        return $this->getConfigFromXpath($xpath, $store);
    }

    /**
     * Versioning for BE is not live yet. Implement this function in getBeBaseUri when this goes live.
     *
     * @param string                $country
     * @param string|null           $endpoint
     * @param string|int|null       $store
     *
     * @return mixed
     */
    public function getVersion($country = 'NL', $endpoint = null, $store = null)
    {
        $xpath = static::XPATH_API_VERSION;
        if ($country == 'BE' && $endpoint == 'postcode-find/') {
            $xpath = static::XPATH_API_BE_POSTCODE_VERSION;
        }

        if ($country == 'BE' && $endpoint == 'street-find/') {
            $xpath = static::XPATH_API_BE_STREET_VERSION;
        }

        return $this->getConfigFromXpath($xpath, $store);
    }

    /**
     * Get type via store ID
     *
     * @param string|int|null $store
     *
     * @return mixed
     */
    public function getType($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_API_TYPE, $store);
    }
}
