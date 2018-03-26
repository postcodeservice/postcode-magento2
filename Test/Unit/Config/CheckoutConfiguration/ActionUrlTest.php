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
namespace TIG\Postcode\Test\Unit\Config\CheckoutConfiguration;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Config\CheckoutConfiguration\ActionUrl;
use Magento\Framework\UrlInterface;

class ActionUrlTest extends TestCase
{
    protected $instanceClass = ActionUrl::class;

    public function testGetValue()
    {
        $expectsUrl = 'http://test.nl/postcode/address/service/';

        $urlInterfaceMock = $this->getFakeMock(UrlInterface::class)->getMock();
        $expects = $urlInterfaceMock->expects($this->once());
        $expects->method('getUrl')->with('postcode/address/service', ['_secure' => true]);
        $expects->willReturn($expectsUrl);

        $instance = $this->getInstance([
            'urlBuilder' => $urlInterfaceMock
        ]);

        $returns = [
            'postcode_service' => $expectsUrl
        ];

        $this->assertEquals($returns, $instance->getValue());
    }
}
