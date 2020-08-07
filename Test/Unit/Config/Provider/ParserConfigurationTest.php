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
namespace TIG\Postcode\Test\Unit\Config\Provider;

use TIG\Postcode\Config\Provider\ParserConfiguration;
use TIG\Postcode\Config\Source\Parser;

class ParserConfigurationTest extends AbstractConfigurationTest
{
    protected $instanceClass = ParserConfiguration::class;

    /**
     * @var ParserConfiguration
     */
    private $instance;

    public function setUp()
    {
        parent::setUp();
        $this->instance = $this->getInstance();
    }

    public function mergeProvider()
    {
        return [
            'Default Parse Line, when some one has alterd the street input' => [
                Parser::STREETFIELD_THREE, Parser::STREETFIELD_TWO, Parser::STREETFIELD_TWO, ParserConfiguration::DEFAULT_PARSELINE
            ],
            'Number and addition on line two' => [
                Parser::STREETFIELD_ONE, Parser::STREETFIELD_TWO, Parser::STREETFIELD_TWO, ParserConfiguration::PARSE_TYPE_TWO
            ],
            'Number on line two and addition on three.' => [
                Parser::STREETFIELD_ONE, Parser::STREETFIELD_TWO, Parser::STREETFIELD_THREE, ParserConfiguration::PARSE_TYPE_THREE
            ],
            'Number and addition on line three.' => [
                Parser::STREETFIELD_ONE, Parser::STREETFIELD_THREE, Parser::STREETFIELD_THREE, ParserConfiguration::PARSE_TYPE_FOUR
            ]
        ];
    }

    /**
     * @dataProvider mergeProvider
     *
     * @param $parseStreet
     * @param $parseHouseNumber
     * @param $parseAddition
     * @param $expected
     */
    public function testGetMergeType($parseStreet, $parseHouseNumber, $parseAddition, $expected)
    {
        $this->setXpathConsecutive(
            [
                ParserConfiguration::XPATH_STREETMERGING,
                ParserConfiguration::XPATH_HOUSENUMBERMERGING,
                ParserConfiguration::XPATH_ADDITIONMERGING
            ],
            [
                $parseStreet, $parseHouseNumber, $parseAddition
            ]
        );

        $this->assertEquals($expected, $this->instance->getMergeType());
    }

    public function testGetStreetMerging()
    {
        $this->setXpath(ParserConfiguration::XPATH_STREETMERGING, Parser::STREETFIELD_ONE);
        $this->assertEquals(Parser::STREETFIELD_ONE, $this->instance->getStreetMerging());
    }

    public function testGetHousenumberMerging()
    {
        $this->setXpath(ParserConfiguration::XPATH_HOUSENUMBERMERGING, Parser::STREETFIELD_TWO);
        $this->assertEquals(Parser::STREETFIELD_TWO, $this->instance->getHousenumberMerging());
    }

    public function testGetAdditionMerging()
    {
        $this->setXpath(ParserConfiguration::XPATH_ADDITIONMERGING, Parser::STREETFIELD_THREE);
        $this->assertEquals(Parser::STREETFIELD_THREE, $this->instance->getAdditionMerging());
    }
}
