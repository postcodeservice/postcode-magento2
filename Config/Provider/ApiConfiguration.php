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
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Postcode\Config\Provider;

class ApiConfiguration extends AbstractConfigProvider
{
    const XPATH_API_BASE    = 'tig_postcode/api/base';
    const XPATH_API_VERSION = 'tig_postcode/api/version';
    const XPATH_API_TYPE    = 'tig_postcode/api/type';

    /**
     * @return string
     */
    public function getBaseUri()
    {
        return $this->getBase() . '/' . $this->getVersion() . '/' . $this->getType() . '/';
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getBase($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_API_BASE, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getVersion($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_API_VERSION, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getType($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_API_TYPE, $store);
    }
}
