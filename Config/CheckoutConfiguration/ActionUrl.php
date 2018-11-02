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
     * @return array
     */
    public function getValue()
    {
        return [
            'postcode_service'        => $this->urlBuilder->getUrl('postcode/address/service', ['_secure' => true]),
            'postcode_be_getpostcode' => $this->urlBuilder->getUrl(
                'postcode/address/service/be/getpostcode', ['_secure' => true]
            ),
            'postcode_be_getstreet'   => $this->urlBuilder->getUrl(
                'postcode/address/service/be/getstreet', ['_secure' => true]
            )
        ];
    }
}
