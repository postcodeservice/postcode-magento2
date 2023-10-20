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
namespace TIG\Postcode\Config\CheckoutConfiguration;

use Magento\Framework\UrlInterface;

class ActionUrl implements CheckoutConfigurationInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * ActionUrl constructor.
     *
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get value and return NL, Belgium and German postcode URLs
     *
     * @return array
     */
    public function getValue()
    {
        return [
            'postcode_service'        => $this->urlBuilder->getUrl('postcode/address/service', ['_secure' => true]),
            'postcode_be_getpostcode' => $this->urlBuilder->getUrl(
                'postcode/address/service/be/getpostcode',
                ['_secure' => true]
            ),
            'postcode_be_getstreet'   => $this->urlBuilder->getUrl(
                'postcode/address/service/be/getstreet',
                ['_secure' => true]
            ),
            'postcode_de_getpostcode' => $this->urlBuilder->getUrl(
                'postcode/address/service/de/getpostcode',
                ['_secure' => true]
            ),
            'postcode_de_getstreet'   => $this->urlBuilder->getUrl(
                'postcode/address/service/de/getstreet',
                ['_secure' => true]
            )
        ];
    }
}
