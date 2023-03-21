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
namespace TIG\Postcode\Test\Unit\Config\Provider;

use TIG\Postcode\Test\TestCase;
use \TIG\Postcode\Config\Provider\CheckoutConfiguration;
use \TIG\Postcode\Config\CheckoutConfiguration\GetCheckoutCompatibility;
use TIG\Postcode\Test\Unit\Config\CheckoutConfiguration\GetCheckoutCompatibilityTest;

class CheckoutConfigurationTest extends TestCase
{
    /** @var CheckoutConfiguration */
    protected $instanceClass = CheckoutConfiguration::class;

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetConfig()
    {
        $checkoutCompatibleMock = $this->getFakeMock(GetCheckoutCompatibility::class)->disableOriginalConstructor()
            ->getMock();
        $checkoutCompatibleMock->expects($this->once())->method('getValue')->willReturn('this is a test');

        $instance = $this->getInstance([
            'postcodeConfiguration' => [
                'test' => $checkoutCompatibleMock
            ]
        ]);

        $result = [
            'postcode' => [
                'test' => 'this is a test'
            ]
        ];

        $this->assertEquals($result, $instance->getConfig());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetConfigWithInvalidClass()
    {
        $wrongClassMock = $this->getFakeMock(GetCheckoutCompatibilityTest::class)->disableOriginalConstructor()
            ->getMock();

        $instance = $this->getInstance([
            'postcodeConfiguration' => [
                'test' => $wrongClassMock
            ]
        ]);

        try {
            $instance->getConfig();
        } catch (\Exception $exception) {
            $shouldReceive = 'test is not an implementation of ' .
                \TIG\Postcode\Config\CheckoutConfiguration\CheckoutConfigurationInterface::class;
            $this->assertEquals($shouldReceive, $exception->getMessage());
            return;
        }

        $this->fail('Should trow an exception, but got none');
    }
}
