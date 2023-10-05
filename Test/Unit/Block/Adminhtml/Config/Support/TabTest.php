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
namespace TIG\Postcode\Test\Unit\Block\Adminhtml\Config\Support;

use TIG\Postcode\Block\Adminhtml\Config\Support\Tab;
use TIG\Postcode\Config\Provider\ModuleConfiguration;
use TIG\Postcode\Test\TestCase;

class TabTest extends TestCase
{
    /** @var Tab */
    protected $instanceClass = Tab::class;

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetVersionNumber()
    {
        $instance = $this->getInstance();
        $this->assertSame('1.6.0', $instance->getVersionNumber());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetSupportedMagentoVersions()
    {
        $instance = $this->getInstance([
           'moduleConfiguration' => $this->getConfigurationMock()
        ]);

        $this->assertSame('2.4.4, 2.4.5, 2.4.6', $instance->getSupportedMagentoVersions());
    }

    /**+
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getConfigurationMock()
    {
        $mock = $this->getFakeMock(ModuleConfiguration::class)->getMock();
        $mockExpects = $mock->expects($this->once());
        $mockExpects->method('getSupportedMagentoVersions');
        $mockExpects->willReturn('2.4.4, 2.4.5, 2.4.6');

        return $mock;
    }
}
