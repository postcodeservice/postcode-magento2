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
use TIG\Postcode\Config\CheckoutConfiguration\GetCheckoutCompatibility;
use TIG\Postcode\Config\Provider\ModuleConfiguration;

class GetCheckoutCompatibilityTest extends TestCase
{
    /** @var GetCheckoutCompatibility */
    protected $instanceClass = GetCheckoutCompatibility::class;

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetValue()
    {
        $moduleConfigMock = $this->getFakeMock(ModuleConfiguration::class)->getMock();
        $expects = $moduleConfigMock->expects($this->once());
        $expects->method('getCheckoutCompatibility');
        $expects->willReturn('default');

        $instance = $this->getInstance([
            'moduleConfiguration' => $moduleConfigMock
        ]);

        $this->assertEquals('default', $instance->getValue());
    }
}
