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

namespace TIG\Postcode\Test\Unit\Config\CheckoutConfiguration;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Config\CheckoutConfiguration\ActionUrl;
use Magento\Framework\UrlInterface;

class ActionUrlTest extends TestCase
{
    /** @var ActionUrl */
    protected $instanceClass = ActionUrl::class;

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetValue()
    {
        $expectsPostcodeNLUrl = 'http://test.nl/postcode/address/service/';
        $expectsPostcodeBEUrl = 'http://test.nl/postcode/address/service/be/getpostcode';
        $expectsStreetBEUrl   = 'http://test.nl/postcode/address/service/be/getstreet';
        $expectsPostcodeDEUrl = 'http://test.nl/postcode/address/service/de/getpostcode';
        $expectsStreetDEUrl   = 'http://test.nl/postcode/address/service/de/getstreet';
        $expectsPostcodeFRUrl = 'http://test.nl/postcode/address/service/fr/getpostcode';
        $expectsStreetFRUrl   = 'http://test.nl/postcode/address/service/fr/getstreet';

        $urlInterfaceMock = $this->getFakeMock(UrlInterface::class)->getMock();
        $expects          = $urlInterfaceMock->expects($this->exactly(3));
        $expects->method('getUrl')->withConsecutive(
            ['postcode/address/service', ['_secure' => true]],
            ['postcode/address/service/be/getpostcode', ['_secure' => true]],
            ['postcode/address/service/be/getstreet', ['_secure' => true]],
            ['postcode/address/service/de/getpostcode', ['_secure' => true]],
            ['postcode/address/service/de/getstreet', ['_secure' => true]],
            ['postcode/address/service/fr/getpostcode', ['_secure' => true]],
            ['postcode/address/service/fr/getstreet', ['_secure' => true]]
        );
        $expects->willReturnOnConsecutiveCalls($expectsPostcodeNLUrl, $expectsPostcodeBEUrl, $expectsStreetBEUrl, $expectsPostcodeDEUrl, $expectsStreetDEUrl, $expectsPostcodeFRUrl, $expectsStreetFRUrl);

        $instance = $this->getInstance(
            [
                'urlBuilder' => $urlInterfaceMock
            ]
        );

        $returns = [
            'postcode_service'        => $expectsPostcodeNLUrl,
            'postcode_be_getpostcode' => $expectsPostcodeBEUrl,
            'postcode_be_getstreet'   => $expectsStreetBEUrl,
            'postcode_de_getpostcode' => $expectsPostcodeDEUrl,
            'postcode_de_getstreet'   => $expectsStreetDEUrl,
            'postcode_fr_getpostcode' => $expectsPostcodeFRUrl,
            'postcode_fr_getstreet'   => $expectsStreetFRUrl
        ];

        $this->assertEquals($returns, $instance->getValue());
    }
}
