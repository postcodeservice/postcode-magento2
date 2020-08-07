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
namespace TIG\Postcode\Test\Unit\Services\Address;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Services\Address\AttributeParser;

class AttributeParserTest extends TestCase
{
    protected $instanceClass = AttributeParser::class;

    public function provider()
    {
        return [
            'valid attributes' => [
                ['tig_housenumber' => '37', 'tig_housenumber_addition' => 'A'], '37', 'A'
            ],
            'not valid attribtus' => [
                ['this_is_incorrect' => '37'], null, null
            ]
        ];
    }

    /**
     * @param $attributes
     * @param $expectedHousenumber
     * @param $expectedAddition
     *
     * @dataProvider provider
     */
    public function testSet($attributes, $expectedHousenumber, $expectedAddition)
    {
        $instance = $this->getInstance();
        $instance->set($attributes);

        $housenumber = $instance->getTigHousenumber();
        $this->assertEquals($expectedHousenumber, $housenumber);

        $addition = $instance->getTigHousenumberAddition();
        $this->assertEquals($expectedAddition, $addition);
    }
}
