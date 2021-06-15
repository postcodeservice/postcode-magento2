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
namespace TIG\Postcode\Test\Unit\Webservice;

use TIG\Postcode\Exception;
use TIG\Postcode\Webservices\Api;
use TIG\Postcode\Test\TestCase;

use Magento\Framework\HTTP\ZendClient as ZendClient;
use TIG\Postcode\Config\Provider\ApiConfiguration;
use TIG\Postcode\Config\Provider\ClientConfiguration;
use TIG\Postcode\Webservices\Endpoints\GetAddress;
use TIG\Postcode\Services\Converter\Factory;
use Magento\Framework\HTTP\PhpEnvironment\ServerAddress;


class ApiTest extends TestCase
{
    protected $instanceClass = Api::class;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $zendClient;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $apiConfiguration;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $clientConfiguration;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $converter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $serverAddress;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $endpoint;

    public function setUp()
    {
        $this->zendClient = $this->getMock(ZendClient::class);
        $this->apiConfiguration = $this->getFakeMock(ApiConfiguration::class, true);
        $this->clientConfiguration = $this->getFakeMock(ClientConfiguration::class, true);
        $this->converter = $this->getFakeMock(Factory::class, true);
        $this->serverAddress = $this->getFakeMock(ServerAddress::class)->disableOriginalConstructor()->getMock();
        $this->endpoint = $this->getFakeMock(GetAddress::class)->disableOriginalConstructor()->getMock();

        return parent::setup();
    }

    /**
     * @param array $args
     *
     * @return object
     */
    public function getInstance(array $args = [])
    {
        return parent::getInstance($args + [
                'client' => $this->zendClient,
                'apiConfiguration' => $this->apiConfiguration,
                'clientConfiguration' => $this->clientConfiguration,
                'converter' => $this->converter,
                'serverAddress' => $this->serverAddress
            ]
        );
    }

    public function testApiInstanceForVersionBelow4()
    {
        $resetZendParams = $this->zendClient->expects($this->once());
        $resetZendParams->method('resetParameters');

        //SetUri
        $uri = 'https://api.fakelocation.com/v3/json/';
        $apiConfigUri = $this->apiConfiguration->expects($this->once());
        $apiConfigUri->method('getBaseUri')->willReturn($uri);

        $endpointGetEndpoint = $this->endpoint->expects($this->once());
        $endpointGetEndpoint->method('getEndpoint')->willReturn('getAddress/');

        $zendClientUri = $this->zendClient->expects($this->once());
        $zendClientUri->method('setUri')->with($uri.'getAddress/');

        //SetHeaders
        $apiConfigVersion = $this->apiConfiguration->expects($this->once());
        $apiConfigVersion->method('getVersion')->willReturn('v3');

        $requestData = ['postcode' => '1014BA', 'huisnummer' => '37'];
        $requestDataWithHeader = ['postcode' => '1014BA', 'huisnummer' => '37', 'client_id' => '11111', 'secure_code' => 'APIKEY'];

        $this->endpoint->expects($this->any())
            ->method('getRequestData')->willReturnOnConsecutiveCalls($requestData, $requestDataWithHeader);

        $clientConfigId = $this->clientConfiguration->expects($this->once());
        $clientConfigId->method('getClientId')->willReturn('11111');

        $clientConfigApi = $this->clientConfiguration->expects($this->once());
        $clientConfigApi->method('getApiKey')->willReturn('APIKEY');

        $endpointSetData = $this->endpoint->expects($this->once());
        $endpointSetData->method('setRequestData')->with($requestDataWithHeader);

        //SetParameters
        $endpointMethod = $this->endpoint->expects($this->exactly(3));
        $endpointMethod->method('getMethod')->willReturn(ZendClient::GET);

        $zendClientSetMethod = $this->zendClient->expects($this->once());
        $zendClientSetMethod->method('setMethod')->with(ZendClient::GET);

        $clientConfigDomain = $this->clientConfiguration->expects($this->once());
        $clientConfigDomain->method('getDomainUrl')->willReturn('www.fakedomain.com');

        $serverIp = $this->serverAddress->expects($this->once());
        $serverIp->method('getServerAddress')->willReturn('127.0.0.1');

        $completedRequestParams = ['postcode' => '1014BA', 'huisnummer' => '37', 'client_id' => '11111',
                                   'secure_code' => 'APIKEY', 'domain' => 'www.fakedomain.com', 'remote_ip' => '127.0.0.1'
        ];

        $zendClientParams = $this->zendClient->expects($this->once());
        $zendClientParams->method('setParameterGet')->with($completedRequestParams);

        $fakeResponse = new \Zend_Http_Response('200', array(), '{}');

        $zendRequest = $this->zendClient->expects($this->once());
        $zendRequest->method('request')->willReturn($fakeResponse);

        $this->getInstance()->getRequest($this->endpoint);
    }

    public function testApiInstanceForVersionAbove4()
    {
        $resetZendParams = $this->zendClient->expects($this->once());
        $resetZendParams->method('resetParameters');

        //SetUri
        $uri = 'https://api.fakelocation.com/v4/json/';
        $apiConfigUri = $this->apiConfiguration->expects($this->once());
        $apiConfigUri->method('getBaseUri')->willReturn($uri);

        $endpointGetEndpoint = $this->endpoint->expects($this->once());
        $endpointGetEndpoint->method('getEndpoint')->willReturn('getAddress/');

        $zendClientUri = $this->zendClient->expects($this->once());
        $zendClientUri->method('setUri')->with($uri.'getAddress/');

        $requestData = ['postcode' => '1014BA', 'huisnummer' => '37'];
        $requestDataWithHeader = ['postcode' => '1014BA', 'huisnummer' => '37'];

        $this->endpoint->expects($this->any())
            ->method('getRequestData')->willReturnOnConsecutiveCalls($requestData, $requestDataWithHeader);

        $clientConfigId = $this->clientConfiguration->expects($this->once());
        $clientConfigId->method('getClientId')->willReturn('11111');

        $clientConfigApi = $this->clientConfiguration->expects($this->once());
        $clientConfigApi->method('getApiKey')->willReturn('APIKEY');

        //SetParameters
        $endpointMethod = $this->endpoint->expects($this->exactly(3));
        $endpointMethod->method('getMethod')->willReturn(ZendClient::GET);

        $zendClientSetMethod = $this->zendClient->expects($this->once());
        $zendClientSetMethod->method('setMethod')->with(ZendClient::GET);

        $clientConfigDomain = $this->clientConfiguration->expects($this->once());
        $clientConfigDomain->method('getDomainUrl')->willReturn('www.fakedomain.com');

        $serverIp = $this->serverAddress->expects($this->once());
        $serverIp->method('getServerAddress')->willReturn('127.0.0.1');

        $completedRequestParams = ['postcode' => '1014BA', 'huisnummer' => '37',
                                   'domain' => 'www.fakedomain.com', 'remote_ip' => '127.0.0.1'
        ];

        $zendClientParams = $this->zendClient->expects($this->once());
        $zendClientParams->method('setParameterGet')->with($completedRequestParams);

        $fakeResponse = new \Zend_Http_Response('200', array(), '{}');

        $zendRequest = $this->zendClient->expects($this->once());
        $zendRequest->method('request')->willReturn($fakeResponse);

        $this->getInstance()->getRequest($this->endpoint);
    }

    public function testApiInstanceForVersionAbove4AsPost()
    {
        $resetZendParams = $this->zendClient->expects($this->once());
        $resetZendParams->method('resetParameters');

        //SetUri
        $uri = 'https://api.fakelocation.com/v4/json/';
        $apiConfigUri = $this->apiConfiguration->expects($this->once());
        $apiConfigUri->method('getBaseUri')->willReturn($uri);

        $endpointGetEndpoint = $this->endpoint->expects($this->once());
        $endpointGetEndpoint->method('getEndpoint')->willReturn('getAddress/');

        $zendClientUri = $this->zendClient->expects($this->once());
        $zendClientUri->method('setUri')->with($uri.'getAddress/');

        $requestData = ['postcode' => '1014BA', 'huisnummer' => '37'];
        $requestDataWithHeader = ['postcode' => '1014BA', 'huisnummer' => '37'];

        $this->endpoint->expects($this->any())
            ->method('getRequestData')->willReturnOnConsecutiveCalls($requestData, $requestDataWithHeader);

        $clientConfigId = $this->clientConfiguration->expects($this->once());
        $clientConfigId->method('getClientId')->willReturn('11111');

        $clientConfigApi = $this->clientConfiguration->expects($this->once());
        $clientConfigApi->method('getApiKey')->willReturn('APIKEY');

        //SetParameters
        $endpointMethod = $this->endpoint->expects($this->exactly(3));
        $endpointMethod->method('getMethod')->willReturn(ZendClient::POST);

        $zendClientSetMethod = $this->zendClient->expects($this->once());
        $zendClientSetMethod->method('setMethod')->with(ZendClient::POST);

        $clientConfigDomain = $this->clientConfiguration->expects($this->once());
        $clientConfigDomain->method('getDomainUrl')->willReturn('www.fakedomain.com');

        $serverIp = $this->serverAddress->expects($this->once());
        $serverIp->method('getServerAddress')->willReturn('127.0.0.1');

        $completedRequestParams = ['postcode' => '1014BA', 'huisnummer' => '37',
                                   'domain' => 'www.fakedomain.com', 'remote_ip' => '127.0.0.1'
        ];

        $zendClientParams = $this->zendClient->expects($this->once());
        $zendClientParams->method('setParameterPost')->with($completedRequestParams);

        $fakeResponse = new \Zend_Http_Response('200', array(), '{}');

        $zendRequest = $this->zendClient->expects($this->once());
        $zendRequest->method('request')->willReturn($fakeResponse);

        $this->getInstance()->getRequest($this->endpoint);
    }

    public function testApiInstanceForBePostcode()
    {
        $resetZendParams = $this->zendClient->expects($this->once());
        $resetZendParams->method('resetParameters');

        //SetUri
        $uri = 'https://api.fakelocation.com/be/v4/';
        $apiConfigUri = $this->apiConfiguration->expects($this->once());
        $apiConfigUri->method('getBeBaseUri')->willReturn($uri);

        $endpointGetEndpoint = $this->endpoint->expects($this->exactly(3));
        $endpointGetEndpoint->method('getEndpoint')->willReturn('postcode-find/');

        $endpointGetEndpointCountry = $this->endpoint->expects($this->exactly(2));
        $endpointGetEndpointCountry->method('getCountry')->willReturn('BE');

        $zendClientUri = $this->zendClient->expects($this->once());
        $zendClientUri->method('setUri')->with($uri.'postcode-find/');

        $requestData = ['zipcodezone' => '1000'];
        $requestDataWithHeader = ['zipcodezone' => '1000'];

        $this->endpoint->expects($this->any())
            ->method('getRequestData')->willReturnOnConsecutiveCalls($requestData, $requestDataWithHeader);

        $clientConfigId = $this->clientConfiguration->expects($this->once());
        $clientConfigId->method('getClientId')->willReturn('11111');

        $clientConfigApi = $this->clientConfiguration->expects($this->once());
        $clientConfigApi->method('getApiKey')->willReturn('APIKEY');

        //SetParameters
        $endpointMethod = $this->endpoint->expects($this->exactly(3));
        $endpointMethod->method('getMethod')->willReturn(ZendClient::GET);

        $zendClientSetMethod = $this->zendClient->expects($this->once());
        $zendClientSetMethod->method('setMethod')->with(ZendClient::GET);

        $clientConfigDomain = $this->clientConfiguration->expects($this->once());
        $clientConfigDomain->method('getDomainUrl')->willReturn('www.fakedomain.com');

        $serverIp = $this->serverAddress->expects($this->once());
        $serverIp->method('getServerAddress')->willReturn('127.0.0.1');

        $completedRequestParams = ['zipcodezone' => '1000',
                                   'domain' => 'www.fakedomain.com', 'remote_ip' => '127.0.0.1'
        ];

        $zendClientParams = $this->zendClient->expects($this->once());
        $zendClientParams->method('setParameterGet')->with($completedRequestParams);

        $fakeResponse = new \Zend_Http_Response('200', array(), '{}');

        $zendRequest = $this->zendClient->expects($this->once());
        $zendRequest->method('request')->willReturn($fakeResponse);

        $this->getInstance()->getRequest($this->endpoint);
    }

    public function testApiWithHttpClientException()
    {
        $zendRequest = $this->zendClient->expects($this->once());
        $zendRequest->method('request')->willThrowException(new \Zend_Http_Client_Exception());

        $response = $this->getInstance()->getRequest($this->endpoint);

        $this->assertEquals(false, $response['success']);
    }
}

