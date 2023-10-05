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
    public const XPATH_API_BASE                = 'tig_postcode/api/base';

    public const XPATH_API_VERSION             = 'tig_postcode/api/version';

    public const XPATH_API_TYPE                = 'tig_postcode/api/type';

    public const XPATH_API_BE_BASE             = 'tig_postcode/api_be/base';

    public const XPATH_API_BE_POSTCODE_VERSION = 'tig_postcode/api_be/postcode_version';

    public const XPATH_API_BE_STREET_VERSION   = 'tig_postcode/api_be/street_version';

    /**
     * Get base Uri
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->getBase() . '/' . $this->getVersion() . '/';
    }

    /**
     * Get Belgium base Uri
     *
     * @param string $endpoint
     *
     * @return string
     */
    public function getBEBaseUri(string $endpoint): string
    {
        return $this->getBase('BE') . '/' . $this->getVersion('BE', $endpoint) . '/';
    }

    /**
     * Get base path via country and store ID
     *
     * @param string          $country
     * @param int|string|null $store
     *
     * @return mixed
     */
    public function getBase(string $country = 'NL', int|string $store = null): mixed
    {
        $xpath = static::XPATH_API_BASE;
        if ($country == 'BE') {
            $xpath = static::XPATH_API_BE_BASE;
        }

        return $this->getConfigFromXpath($xpath, $store);
    }

    /**
     * Versioning handling for multiple countries
     *
     * @param string          $country
     * @param string|null     $endpoint
     * @param int|string|null $store
     *
     * @return mixed
     */
    public function getVersion(string $country = 'NL', string $endpoint = null, int|string $store = null): mixed
    {
        $xpath = static::XPATH_API_VERSION;

        if ($country == 'BE') {
            switch ($endpoint) {
                case 'zipcode-find/':
                    $xpath = static::XPATH_API_BE_POSTCODE_VERSION;
                    break;
                case 'street-find/':
                    $xpath = static::XPATH_API_BE_STREET_VERSION;
                    break;
            }
        }

        return $this->getConfigFromXpath($xpath, $store);
    }

    /**
     * Get type via store ID
     *
     * @param int|string|null $store
     *
     * @return mixed
     */
    public function getType(int|string $store = null): mixed
    {
        return $this->getConfigFromXpath(static::XPATH_API_TYPE, $store);
    }
}
