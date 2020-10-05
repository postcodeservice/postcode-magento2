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
namespace TIG\Postcode\Webservices;

use Magento\Framework\HTTP\ZendClient as ZendClient;
use TIG\Postcode\Config\Provider\ApiConfiguration;
use TIG\Postcode\Config\Provider\ClientConfiguration;
use TIG\Postcode\Webservices\Endpoints\EndpointInterface;
use TIG\Postcode\Services\Converter\Factory;
use Magento\Framework\HTTP\PhpEnvironment\ServerAddress;

class Api
{
    /**
     * @var ZendClient
     */
    private $zendClient;

    /**
     * @var ApiConfiguration
     */
    private $apiConfiguration;

    /**
     * @var ClientConfiguration
     */
    private $clientConfiguration;

    /**
     * @var Factory
     */
    private $converter;

    /**
     * @var ServerAddress
     */
    private $serverAddress;

    /**
     * Api constructor.
     *
     * @param ZendClient          $client
     * @param ApiConfiguration    $apiConfiguration
     * @param ClientConfiguration $clientConfiguration
     * @param Factory             $converter
     * @param ServerAddress       $serverAddress
     */
    public function __construct(
        ZendClient $client,
        ApiConfiguration $apiConfiguration,
        ClientConfiguration $clientConfiguration,
        Factory $converter,
        ServerAddress $serverAddress
    ) {
        $this->zendClient          = $client;
        $this->apiConfiguration    = $apiConfiguration;
        $this->clientConfiguration = $clientConfiguration;
        $this->converter           = $converter;
        $this->serverAddress       = $serverAddress;
    }

    /**
     * @param EndpointInterface $endpoint
     *
     * @return array|\Zend_Http_Response
     */
    public function getRequest(EndpointInterface $endpoint)
    {
        $this->zendClient->resetParameters();

        $this->setUri($endpoint);
        $this->setHeaders($endpoint);
        $this->setParameter($endpoint);

        try {
            $response = $this->zendClient->request();
            $response = $this->converter->convert('response', $response->getBody(), $endpoint->getResponseKeys());
        } catch (\Zend_Http_Client_Exception $exception) {
            $response = [
                'success' => false,
                'error'   => __('%1 : Zend Http Client exception', $exception->getCode())
            ];
        }

        return $response;
    }

    /**
     * Set the headers, but only if the api version is 4 or higher. Before version 4 user data is parsed within the
     * requestData array as ['client_id' => 'xxxx', 'secure_code' => 'xxxx']
     *
     * @param $endpoint;
     */
    private function setHeaders(EndpointInterface $endpoint)
    {
        $version = str_replace('v', '', $this->apiConfiguration->getVersion());

        if ((int)$version >= 4 || $endpoint->getCountry() === 'BE') {
            $this->zendClient->setConfig(['strict' => false]);
            $this->zendClient->setHeaders([
                'X-Client_Id'   => $this->clientConfiguration->getClientId(),
                'X-Secure_Code' => $this->clientConfiguration->getApiKey()
            ]);

            return;
        }

        $params = $endpoint->getRequestData();
        $params['client_id']   = $this->clientConfiguration->getClientId();
        $params['secure_code'] = $this->clientConfiguration->getApiKey();

        $endpoint->setRequestData($params);
    }

    /**
     * @param EndpointInterface $endpoint
     */
    private function setParameter(EndpointInterface $endpoint)
    {
        $this->zendClient->setMethod($endpoint->getMethod());

        $params = $endpoint->getRequestData();
        $params['domain']    = $this->clientConfiguration->getDomainUrl();
        $params['remote_ip'] = $this->serverAddress->getServerAddress();

        if ($endpoint->getMethod() == ZendClient::GET) {
            $this->zendClient->setParameterGet($params);
        }

        if ($endpoint->getMethod() == ZendClient::POST) {
            $this->zendClient->setParameterPost($params);
        }
    }

    /**
     * @param EndpointInterface $endpoint
     *
     * @throws \Zend_Http_Client_Exception
     */
    private function setUri(EndpointInterface $endpoint)
    {
        $uri = $this->apiConfiguration->getBaseUri() . $endpoint->getEndpoint();
        if ($endpoint->getCountry() == 'BE') {
            $uri = $this->apiConfiguration->getBeBaseUri($endpoint->getEndpoint()) . $endpoint->getEndpoint();
        }

        $this->zendClient->setUri($uri);
    }
}
