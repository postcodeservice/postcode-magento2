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
namespace TIG\Postcode\Test\Unit\Services\Address;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Services\Address\StreetFields;
use TIG\Postcode\Config\Provider\ParserConfiguration;
use Magento\Quote\Api\Data\AddressExtensionInterface;

class StreetFieldsTest extends TestCase
{
    protected $instanceClass = StreetFields::class;

    public function parseProvider()
    {
        $attributes = ['tig_housenumber' => 37, 'tig_housenumber_addition' => 'A'];

        return [
            'parse level one' => [
                ['kabelweg'], $attributes, ParserConfiguration::PARSE_TYPE_ONE, ['kabelweg 37 A']
            ],
            'parse level two' => [
                ['kabelweg'], $attributes, ParserConfiguration::PARSE_TYPE_TWO, ['kabelweg', '37 A']
            ],
            'parse level three' => [
                ['kabelweg'], $attributes, ParserConfiguration::PARSE_TYPE_THREE, ['kabelweg', 37, 'A']
            ],
            'parse level four' => [
                ['kabelweg'], $attributes, ParserConfiguration::PARSE_TYPE_FOUR, ['kabelweg', '', '37 A']
            ],
        ];
    }

    /**
     * @dataProvider parseProvider
     * @param $street
     * @param $attributes
     * @param $parseSetting
     * @param $expected
     */
    public function testParse($street, $attributes, $parseSetting, $expected)
    {
        $parseConfigurationMock = $this->getFakeMock(ParserConfiguration::class)->getMock();
        $parseConfigurationExpects = $parseConfigurationMock->expects($this->once());
        $parseConfigurationExpects->method('getMergeType');
        $parseConfigurationExpects->willReturn($parseSetting);

        $extensionAttributes = $this->getFakeMock(AddressExtensionInterface::class)
            ->setMethods(
                ['getTigHousenumber',
                 'getTigHousenumberAddition',
                 'setTigHousenumber',
                 'setTigHousenumberAddition',
                 'getCheckoutFields',
                 'setCheckoutFields',
                 'getTigZipcodezone',
                 'setTigZipcodezone'
                ]
            )->getMock();

        $attributeHousenumeber = $extensionAttributes->expects($this->any())->method('getTigHousenumber');
        $attributeHousenumeber->willReturn($attributes['tig_housenumber']);
        $attributeAddition = $extensionAttributes->expects($this->any())->method('getTigHousenumberAddition');
        $attributeAddition->willReturn($attributes['tig_housenumber_addition']);

        $instance = $this->getInstance([
            'parserConfiguration' => $parseConfigurationMock
        ]);

        $result = $instance->parse($street, $extensionAttributes);
        $this->assertEquals($expected, $result);
    }
}
