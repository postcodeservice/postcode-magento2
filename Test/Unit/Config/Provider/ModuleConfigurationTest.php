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
namespace TIG\Postcode\Test\Unit\Config\Provider;

use TIG\Postcode\Config\Provider\ModuleConfiguration;

class ModuleConfigurationTest extends AbstractConfigurationTest
{
    protected $instanceClass = ModuleConfiguration::class;

    public function liveModusProvider()
    {
        return [
            'ModuleOutput disabled' => [false, false, '0'],
            'ModuleOutput enabled'  => [true, true, '1'],
        ];
    }

    /**
     * @dataProvider liveModusProvider
     *
     * @param $moduleOutput
     * @param $expected
     * @param $modus
     */
    public function testIsModusLive($moduleOutput, $expected, $modus)
    {
        $instance = $this->getInstance();
        $this->setModuleOutputEnabled($moduleOutput);
        $this->setModus($modus);

        $this->assertEquals($expected, $instance->isModusLive());
    }

    public function testModusProvider()
    {
        return [
            'ModuleOutput disabled' => [false, false, '0'],
            'ModuleOutput enabled'  => [true, true, '2'],
        ];
    }

    /**
     * @dataProvider testModusProvider
     *
     * @param $moduleOutput
     * @param $expected
     * @param $modus
     */
    public function testIsModusTest($moduleOutput, $expected, $modus)
    {
        $instance = $this->getInstance();
        $this->setModuleOutputEnabled($moduleOutput);
        $this->setModus($modus);

        $this->assertEquals($expected, $instance->isModusTest());
    }

    public function offModusProvider()
    {
        return [
            'ModuleOutput disabled' => [false, true, '0'],
            'ModuleOutput enabled'  => [true, true, '0'],
        ];
    }

    /**
     * @dataProvider offModusProvider
     *
     * @param $moduleOutput
     * @param $expected
     * @param $modus
     */
    public function testIsModusOff($moduleOutput, $expected, $modus)
    {
        $instance = $this->getInstance();
        $this->setModuleOutputEnabled($moduleOutput);
        $this->setModus($modus);

        $this->assertEquals($expected, $instance->isModusOff());
    }

    public function modusProvider()
    {
        return [
            'Live modus and moduleOutput disabled' => ['1', false, '0'],
            'Live modus and moduleOutput enabled'  => ['1', true, '1'],
            'Test modus and moduleOutput disabled' => ['2', false, '0'],
            'Test modus and moduleOutput enabled'  => ['2', true, '2'],
            'Off modus and moduleOutput disabled'  => ['0', false, '0'],
            'Off modus and moduleOutput enabled'   => ['0', true, '0'],
        ];
    }

    /**
     * @dataProvider modusProvider
     *
     * @param $modus
     * @param $moduleOutput
     * @param $expectedResult
     */
    public function testGetModus($modus, $moduleOutput, $expectedResult)
    {
        $instance = $this->getInstance();

        $this->setModuleOutputEnabled($moduleOutput);
        $this->setModus($modus);

        $this->assertEquals($expectedResult, $instance->getModus());

    }

    public function testGetStability()
    {
        $value = $this->getRandomSyntax();
        $instance = $this->getInstance();

        $this->setXpath(ModuleConfiguration::XPATH_MODULE_STABILITY, $value);
        $this->assertEquals($value, $instance->getStability());
    }

    public function testGetSupportedMagentoVersions()
    {
        $value = $this->getRandomSyntax();
        $instance = $this->getInstance();

        $this->setXpath(ModuleConfiguration::XPATH_SUPPORTED_MAGENTO_VERSION, $value);
        $this->assertEquals($value, $instance->getSupportedMagentoVersions());
    }


}
