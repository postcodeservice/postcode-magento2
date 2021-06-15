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
namespace TIG\Postcode\Test\Unit\Services\Converter;

use TIG\Postcode\Services\Converter;
use TIG\Postcode\Services\Validation\Request as ValidationRequest;

class RequestTest extends TestInterface
{
    protected $instanceClass = Converter\Request::class;

    public function requestDataProvider()
    {
        return [
            'Correct Request data' => [
                ['postcode' => '1014BA', 'huisnummer' => '37'],
                ['postcode' => '1014BA', 'huisnummer' => '37'],
                true,
            ],
            'Incorrect keys in Data array' => [
                ['zipcode' => '1014BA', 'huisnummer' => '37'],
                false,
                false
            ],
        ];
    }

    /**
     * @dataProvider requestDataProvider
     * @param $data
     * @param $expected
     * @param $validation
     */
    public function testRequestConverter($data, $expected, $validation)
    {
        $instance = $this->getInstance([
            'validation' => $this->validationRequestMock($data, $validation)
        ]);

        $instance->setValidationKeys(['postcode', 'huisnummer']);

        $this->assertSame($expected, $instance->convert($data));
    }

    /**
     * @param $data
     * @param $return
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function validationRequestMock($data, $return)
    {
        $mock = $this->getMock(ValidationRequest::class);
        $mockExpects = $mock->expects($this->once());
        $mockExpects->method('validate')->with($data);
        $mockExpects->willReturn($return);

        if ($return) {
            $mockExpects = $mock->expects($this->once());
            $mockExpects->method('getKeys');
            $mockExpects->willReturn(['postcode', 'huisnummer']);
        }

        return $mock;
    }
}
