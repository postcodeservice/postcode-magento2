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

use TIG\Postcode\Config\Provider\ClientConfiguration;
use TIG\Postcode\Config\Provider\ModuleConfiguration;

class ClientConfigurationTest extends AbstractConfigurationTest
{
    protected $instanceClass = ClientConfiguration::class;

    /**
     * @var ModuleConfiguration|\PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleMock;

    /**
     * @return array
     */
    public function modusXpathProvider()
    {
        return [
            'get client xpath where modus is test'  => [
                true,
                ClientConfiguration::XPATH_CONFIGURATION_CLIENT_ID,
                ClientConfiguration::XPATH_CONFIGURATION_CLIENT_ID.'_test'
            ],
            'get client xpath where modus is not test' => [
                false,
                ClientConfiguration::XPATH_CONFIGURATION_CLIENT_ID,
                ClientConfiguration::XPATH_CONFIGURATION_CLIENT_ID
            ],
            'get apikey xpath where modus is test'  => [
                true,
                ClientConfiguration::XPATH_CONFIGURATION_API_KEY,
                ClientConfiguration::XPATH_CONFIGURATION_API_KEY.'_test'
            ],
            'get apikey xpath where modus is not test' => [
                false,
                ClientConfiguration::XPATH_CONFIGURATION_API_KEY,
                ClientConfiguration::XPATH_CONFIGURATION_API_KEY
            ]
        ];
    }

    public function testGetClientId()
    {
        $this->setModuleMock(false);

        $instance = $this->getInstance([
            'moduleConfiguration' => $this->moduleMock
        ]);

        $value = $this->getRandomSyntax();
        $this->setXpath(ClientConfiguration::XPATH_CONFIGURATION_CLIENT_ID, $value);

        $this->assertEquals($value, $instance->getClientId());
    }

    /**
     * @dataProvider modusXpathProvider
     *
     * @param $testModus
     * @param $value
     * @param $expected
     */
    public function testGetModusXpath($testModus, $value, $expected)
    {
        $this->setModuleMock($testModus);

        $instance = $this->getInstance([
           'moduleConfiguration' => $this->moduleMock
        ]);

        $this->assertEquals($expected, $instance->getModusXpath($value));
    }

    private function setModuleMock($testMode)
    {
        $this->moduleMock = $this->getFakeMock(ModuleConfiguration::class)->disableOriginalConstructor()->getMock();
        $moduleConfigExpects = $this->moduleMock->expects($this->any());
        $moduleConfigExpects->method('isModusTest');
        $moduleConfigExpects->willReturn($testMode);
    }
}