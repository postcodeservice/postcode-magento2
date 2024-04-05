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
namespace TIG\Postcode\Test\Unit\Controller\Address;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Controller\Address\Service;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use TIG\Postcode\Services\Converter\Factory;
use TIG\Postcode\Webservices\Endpoints\GetNLAddressValidation;
use TIG\Postcode\Webservices\Endpoints\GetBEZipcodeFind;
use TIG\Postcode\Webservices\Endpoints\GetBEStreetFind;
use TIG\Postcode\Webservices\Endpoints\GetDEZipcodeFind;
use TIG\Postcode\Webservices\Endpoints\GetDEStreetFind;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Json;

class ServiceTest extends TestCase
{
    /** @var Service */
    protected $instanceClass = Service::class;

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            'request converter fails' => [
                false,
                []
            ],
            'call fails'              => [
                [
                    'postcode' => '1014BA', 'huisnummer' => '37'
                ],
                [
                    'postcode' => '1014BA', 'huisnummer' => '37'
                ]
            ],
            'be postcode succeeds'    => [
                [
                    'be' => 'getpostcode', 'zipcodezone' => '1000'
                ],
                [
                    'be' => 'getpostcode', 'zipcodezone' => '1000'
                ]
            ],
            'be street succeeds'      => [
                [
                    'be'      => 'getstreet',
                    'city'    => 'Brussel',
                    'zipcode' => '1000',
                    'street'  => 'Zandstraat'
                ],
                [
                    'be'      => 'getstreet',
                    'city'    => 'Brussel',
                    'zipcode' => '1000',
                    'street'  => 'Zandstraat'
                ]
            ],
            'de postcode succeeds'    => [
                [
                    'de' => 'getpostcode', 'zipcodezone' => '40723'
                ],
                [
                    'de' => 'getpostcode', 'zipcodezone' => '40723'
                ]
            ],
            'de street succeeds'      => [
                [
                    'de'      => 'getstreet',
                    'city'    => 'Hilden',
                    'zipcode' => '40723',
                    'street'  => 'Kalstert'
                ],
                [
                    'de'      => 'getstreet',
                    'city'    => 'Hilden',
                    'zipcode' => '40723',
                    'street'  => 'Kalstert'
                ]
            ]
        ];
    }

    /**
     * @param $converterFails
     * @param $params
     *
     * @dataProvider dataProvider
     * @throws \Exception
     */
    public function testExecuteShouldAlwaysReturnJsonObject($converterFails, $params)
    {
        $instance = $this->getInstance([
            'context' => $this->getContextMock($params),
            'jsonFactory' => $this->getJsonFactory(),
            'converterFactory' => $this->getConverterMock($converterFails, $params),
            'getAddress' => $this->getAddressCallMock($converterFails, $params),
            'getBePostcode' => $this->getBePostcodeCallMock($converterFails, $params),
            'getBeStreet' => $this->getBeStreetCallMock($converterFails, $params),
            'getDePostcode' => $this->getDePostcodeCallMock($converterFails, $params),
            'getDeStreet' => $this->getDeStreetCallMock($converterFails, $params),
        ]);

        $result = $instance->execute();
        $this->assertTrue($result instanceof \Magento\Framework\Controller\Result\Json);
    }

    /**
     * @param $converterFails
     * @param $params
     *
     * @dataProvider dataProvider
     * @throws \Exception
     */
    public function testExecuteWithEmptyResultShouldAlwaysReturnJsonObject($converterFails, $params)
    {
        $instance = $this->getInstance([
            'context' => $this->getContextMock($params),
            'jsonFactory' => $this->getJsonFactory(),
            'converterFactory' => $this->getConverterMock($converterFails, $params),
            'getAddress' => $this->getAddressCallMock($converterFails, $params, true),
            'getBePostcode' => $this->getBePostcodeCallMock($converterFails, $params),
            'getBeStreet' => $this->getBeStreetCallMock($converterFails, $params),
            'getDePostcode' => $this->getDePostcodeCallMock($converterFails, $params),
            'getDeStreet' => $this->getDeStreetCallMock($converterFails, $params),
        ]);

        $result = $instance->execute();
        $this->assertTrue($result instanceof \Magento\Framework\Controller\Result\Json);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getJsonFactory()
    {
        $jsonFactoryMock = $this->getFakeMock(JsonFactory::class)->setMethods(['create'])->getMock();
        $jsonFactoryMock->expects($this->once())->method('create')->willReturn($this->getObject(Json::class));

        return $jsonFactoryMock;
    }

    /**
     * @param $params
     * @param bool $returns
     * @param bool $failResponse
     * @return mixed
     */
    private function getAddressCallMock($params, bool $returns = false, bool $failResponse = false)
    {
        $addressMock = $this->getFakeMock(GetNLAddressValidation::class)->setMethods([
            'setRequestData', 'call', 'getCountry', 'getMethod'
        ])->getMock();

        $setExpects = $addressMock->expects($this->any());
        $setExpects->method('setRequestData')->with($params);

        $callExpects = $addressMock->expects($this->any());

        if ($failResponse) {
            $callExpects->method('call')->willReturn(false);
        } else {
            $callExpects->method('call')->willReturn($returns);
        }

        $countryExpects = $addressMock->expects($this->any());
        $countryExpects->method('getCountry')->willReturn('nl');

        $methodExpects = $addressMock->expects($this->any());
        $methodExpects->method('getMethod')->willReturn('postcodecheck');

        return $addressMock;
    }

    /**
     * @param $params
     * @param bool $returns
     * @return mixed
     */
    private function getBePostcodeCallMock($params, bool $returns = false)
    {
        $addressMock = $this->getFakeMock(GetBEZipcodeFind::class)->setMethods([
            'setRequestData', 'call', 'getCountry', 'getMethod'
        ])->getMock();

        $setExpects = $addressMock->expects($this->any());
        $setExpects->method('setRequestData')->with($params);

        $callExpects = $addressMock->expects($this->any());
        $callExpects->method('call')->willReturn($returns);

        $countryExpects = $addressMock->expects($this->any());
        $countryExpects->method('getCountry')->willReturn('be');

        $methodExpects = $addressMock->expects($this->any());
        $methodExpects->method('getMethod')->willReturn('getpostcode');

        return $addressMock;
    }

    /**
     * @param $params
     * @param bool $returns
     * @return mixed
     */
    private function getDePostcodeCallMock($params, bool $returns = false)
    {
        $addressMock = $this->getFakeMock(GetDEZipcodeFind::class)->setMethods([
            'setRequestData', 'call', 'getCountry', 'getMethod'
        ])->getMock();

        $setExpects = $addressMock->expects($this->any());
        $setExpects->method('setRequestData')->with($params);

        $callExpects = $addressMock->expects($this->any());
        $callExpects->method('call')->willReturn($returns);

        $countryExpects = $addressMock->expects($this->any());
        $countryExpects->method('getCountry')->willReturn('de');

        $methodExpects = $addressMock->expects($this->any());
        $methodExpects->method('getMethod')->willReturn('getpostcode');

        return $addressMock;
    }

    /**
     * @param $params
     * @param bool $returns
     * @return mixed
     */
    private function getBeStreetCallMock($params, bool $returns = false)
    {
        $addressMock = $this->getFakeMock(GetBEStreetFind::class)->setMethods([
            'setRequestData', 'call', 'getCountry', 'getMethod'
        ])->getMock();

        $setExpects = $addressMock->expects($this->any());
        $setExpects->method('setRequestData')->with($params);

        $callExpects = $addressMock->expects($this->any());
        $callExpects->method('call')->willReturn($returns);

        $countryExpects = $addressMock->expects($this->any());
        $countryExpects->method('getCountry')->willReturn('be');

        $methodExpects = $addressMock->expects($this->any());
        $methodExpects->method('getMethod')->willReturn('getstreet');

        return $addressMock;
    }

    /**
     * @param $params
     * @param bool $returns
     * @return mixed
     */
    private function getDeStreetCallMock($params, bool $returns = false)
    {
        $addressMock = $this->getFakeMock(GetDEStreetFind::class)->setMethods([
            'setRequestData', 'call', 'getCountry', 'getMethod'
        ])->getMock();

        $setExpects = $addressMock->expects($this->any());
        $setExpects->method('setRequestData')->with($params);

        $callExpects = $addressMock->expects($this->any());
        $callExpects->method('call')->willReturn($returns);

        $countryExpects = $addressMock->expects($this->any());
        $countryExpects->method('getCountry')->willReturn('de');

        $methodExpects = $addressMock->expects($this->any());
        $methodExpects->method('getMethod')->willReturn('getstreet');

        return $addressMock;
    }

    /**
     * @param $returns
     * @param $params
     * @return mixed
     */
    private function getConverterMock($returns = true, $params = [])
    {
        $converterMock = $this->getFakeMock(Factory::class)->setMethods(['convert'])->getMock();
        $converterMock->expects($this->once())->method('convert')->with('request', $params)->willReturn($returns);

        return $converterMock;
    }

    /**
     * @param $returns
     * @return mixed
     */
    private function getContextMock($returns = [])
    {
        $requestMock = $this->getFakeMock(RequestInterface::class)->getMockForAbstractClass();
        $requestMock->expects($this->once())->method('getParams')->willReturn($returns);

        $contextMock = $this->getFakeMock(Context::class)->setMethods(['getRequest'])->getMock();
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);

        return $contextMock;
    }
}
