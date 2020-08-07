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
 * to support@postcodeservice.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Postcode\Test\Unit\Block\Adminhtml\Config\Credentials;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Block\Frontend\Checkout\DataProvider;
use TIG\Postcode\Config\Provider\ModuleConfiguration;

class DataProviderTest extends TestCase
{
    protected $instanceClass = DataProvider::class;

    public function testIsPostcodeBeOn()
    {
        $instance = $this->getInstance([
            'configuration' => $this->getConfigurationMock(true)
        ]);

        $this->assertEquals(true, $instance->isPostcodeBeCheckOn());
    }

    public function testIsPostcodeBeOff()
    {
        $instance = $this->getInstance([
            'configuration' => $this->getConfigurationMock(false)
        ]);

        $this->assertEquals(false, $instance->isPostcodeBeCheckOn());
    }

    /**+
     * @param bool $enabled
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getConfigurationMock($enabled)
    {
        $mock = $this->getFakeMock(ModuleConfiguration::class)->getMock();
        $mockExpects = $mock->expects($this->once());
        $mockExpects->method('isBeCheckEnabled');
        $mockExpects->willReturn($enabled);

        return $mock;
    }
}
