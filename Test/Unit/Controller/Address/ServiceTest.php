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
namespace TIG\Postcode\Test\Unit\Controller\Address;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Controller\Address\Service;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use TIG\Postcode\Services\Converter\Factory;
use TIG\Postcode\Webservices\Endpoints\GetAddress;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Json;

class ServiceTest extends TestCase
{
    protected $instanceClass = Service::class;

    public function dataProvider()
    {
        return [
            'request converter fails' => [false, []],
            'call fails' => [['postcode' => '1014BA', 'huisnummer' => '37'], ['postcode' => '1014BA', 'huisnummer' => '37']]
        ];
    }

    /**
     * @param $converterFails
     * @param $params
     *
     * @dataProvider dataProvider
     */
    public function testExecuteShouldAlwaysReturnJsonObject($converterFails, $params)
    {
        $instance = $this->getInstance([
            'context' => $this->getContextMock($params),
            'jsonFactory' => $this->getJsonFactory(),
            'converterFactory' => $this->getConverterMock($converterFails, $params),
            'getAddress' => $this->getAddressCallMock(false, $params)
        ]);

        $result = $instance->execute();
        $this->assertTrue($result instanceof \Magento\Framework\Controller\Result\Json);
    }

    private function getJsonFactory()
    {
        $jsonFactoryMock = $this->getFakeMock(JsonFactory::class)->setMethods(['create'])->getMock();
        $jsonFactoryMock->expects($this->once())->method('create')->willReturn($this->getObject(Json::class));

        return $jsonFactoryMock;

    }

    private function getAddressCallMock($returns = false, $params)
    {
        $addressMock = $this->getFakeMock(GetAddress::class)->setMethods([
            'setRequestData', 'call'
        ])->getMock();

        $setExpects = $addressMock->expects($this->any());
        $setExpects->method('setRequestData')->with($params);

        $callExpects = $addressMock->expects($this->any());
        $callExpects->method('call')->willReturn($returns);

        return $addressMock;
    }

    private function getConverterMock($returns = true, $params = [])
    {
        $converterMock = $this->getFakeMock(Factory::class)->setMethods(['convert'])->getMock();
        $converterMock->expects($this->once())->method('convert')->with('request', $params)->willReturn($returns);

        return $converterMock;
    }

    private function getContextMock($returns = [])
    {
        $requestMock = $this->getFakeMock(RequestInterface::class)->getMockForAbstractClass();
        $requestMock->expects($this->once())->method('getParams')->willReturn($returns);

        $contextMock = $this->getFakeMock(Context::class)->setMethods(['getRequest'])->getMock();
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);

        return $contextMock;
    }
}