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
namespace TIG\Postcode\Test\Unit\Services\Validation;

use TIG\Postcode\Test\TestCase;
    use TIG\Postcode\Services\Validation;

class FactoryTest extends TestCase
{
    /** @var Validation\Factory */
    protected $instanceClass = Validation\Factory::class;

    /**
     * @var Validation\Factory
     */
    public $instance;

    /**
     * @return void
     * @throws \Exception
     */
    public function setUp()
    {
        parent::setUp();

        $this->instance = $this->getInstance([
            'validators' => [
                'request'  => $this->getObject(Validation\Request::class),
                'response' => $this->getObject(Validation\Response::class),
            ]
        ]);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testIncorrectClassImplementation()
    {
        $instance = $this->getInstance([
            'validators' => [
                'incorrect' => static::class
            ]
        ]);

        try {
            $instance->validate('type', 'data');
        } catch (\TIG\Postcode\Exception $exception) {
            $souldReceive = 'Class is not an implementation of '. Validation\ValidationInterface::class;
            $this->assertEquals($souldReceive, $exception->getMessage());
            return;
        }

        $this->fail('Should trow an exception, but got none');
    }

    /**
     * @return void
     */
    public function testIncorrectValidationTypeGiven()
    {
        try {
            $this->instance->validate('incorrect-typ', 'some-data');
        } catch (\TIG\Postcode\Exception $exception) {
            $souldReceive = 'Could not find type incorrect-typ as validator';
            $this->assertEquals($souldReceive, $exception->getMessage());
            return;
        }

        $this->fail('Should trow an exception, but got none');
    }

    /**
     * @return array[]
     */
    public function requestDataProvider()
    {
        return [
            'Correct Data array' => [
                ['postcode' => '1014BA', 'huisnummer' => '37'],
                true
            ],
            'Incorrect keys in Data array' => [
                ['zipcode' => '1014BA', 'huisnummer' => '37'],
                false
            ],
            'Incorrect format of Data' => [
                'This should be an array',
                false
            ]
        ];
    }

    /**
     * @dataProvider requestDataProvider
     * @param $data
     * @param $expected
     * @throws \TIG\Postcode\Exception
     */
    public function testRequestValidator($data, $expected)
    {
        $this->assertSame($expected, $this->instance->validate('request', $data));
    }

    /**
     * @return array[]
     */
    public function responseDataProvider()
    {
        return [
            'Correct Data array' => [
                ['street' => 'kabelweg', 'city' => 'Amsterdam'],
                true
            ],
            'Incorrect keys in Data array' => [
                ['street' => 'kabelweg', 'city' => 'Amsterdam'],
                false
            ],
            'Incorrect format of Data' => [
                'This should be an array',
                false
            ],
            'Limit calls response' => [
                ['street' => 'Opvraag limiet bereikt', 'city' => 'Amsterdam'],
                false
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function responseDataProviderBe()
    {
        return [
            'Correct Recursive Data array' => [
                [['postcode' => '1000', 'plaats' => 'Brussel'], ['postcode' => '1060', 'plaats' => 'Brussel']],
                true
            ],
            'Incorrect Recursive Data array' => [
                [['zipcode' => '1000', 'woonplaats' => 'Brussel'], ['zipcode' => '1060', 'woonplaats' => 'Brussel']],
                false
            ]
        ];
    }

    /**
     * @dataProvider responseDataProvider
     * @param $data
     * @param $expected
     * @throws \Exception
     */
    public function testResponseValidator($data, $expected)
    {
        $this->assertSame($expected, $this->instance->validate('response', $data));
    }

    /**
     * @dataProvider responseDataProviderBe
     * @param $data
     * @param $expected
     * @throws \Exception
     */
    public function testResponseValidatorForBECall($data, $expected)
    {
        $object = $this->getObject(Validation\Response::class);
        $object->setKeys(['zipcode', 'city']);

        $instance = $this->getInstance([
            'validators' => [
                'response' => $object
            ]
        ]);

        $this->assertSame($expected, $instance->validate('response', $data));
    }
}
