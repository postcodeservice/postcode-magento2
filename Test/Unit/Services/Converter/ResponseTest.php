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
namespace TIG\Postcode\Test\Unit\Services\Converter;

use TIG\Postcode\Services\Converter;
use TIG\Postcode\Services\Validation\Response as ValidationResponse;
use Magento\Framework\Serialize\Serializer\Json as JsonHelper;

class ResponseTest extends TestInterface
{
    protected $instanceClass = Converter\Response::class;

    public function responseDataProvider()
    {
        return [
            'Correct Response Data' => [
                '{"success":true,"straatnaam":"Kabelweg","woonplaats":"Amsterdam"}',
                ['success' => true, 'straatnaam' => 'Kabelweg', 'woonplaats' => 'Amsterdam'],
                ['success' => true, 'straatnaam' => 'Kabelweg', 'woonplaats' => 'Amsterdam'],
                true
            ],
            'In correct Response Data' => [
                '{"success":true,"woonplaats":"Amsterdam"}',
                ['success' => true, 'woonplaats' => 'Amsterdam'],
                false, false
            ]
        ];
    }

    /**
     * @dataProvider responseDataProvider
     * @param $dataString
     * @param $data
     * @param $expected
     * @param $validation
     */
    public function testResponseConverter($dataString, $data, $expected, $validation)
    {
        $instance = $this->getInstance([
            'validation' => $this->validationResponseMock($data, $validation)
        ]);

        $instance->setValidationKeys(['success', 'straat', 'woonplaats']);

        $this->assertSame($expected, $instance->convert($dataString));
    }

    /**
     * @param $data
     * @param $return
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function validationResponseMock($data, $return)
    {
        $mock = $this->getMock(ValidationResponse::class);
        $mockExpects = $mock->expects($this->once());
        $mockExpects->method('validate')->with($data);
        $mockExpects->willReturn($return);

        return $mock;
    }

    /**
     * @param $dataString
     * @param $data
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function jsonHelperMock($dataString, $data)
    {
        $mock = $this->getFakeMock(JsonHelper::class)->disableOriginalConstructor()->getMock();
        $mockExpects = $mock->expects($this->once());
        $mockExpects->method('unserialize')->with($dataString);
        $mockExpects->willReturn($data);

        return $mock;
    }
}
