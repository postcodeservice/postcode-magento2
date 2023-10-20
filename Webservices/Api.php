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

namespace TIG\Postcode\Webservices;

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Client\Exception\RuntimeException;
use Laminas\Http\Request;
use TIG\Postcode\Config\Provider\ApiConfiguration;
use TIG\Postcode\Config\Provider\ClientConfiguration;
use TIG\Postcode\Webservices\Endpoints\EndpointInterface;
use TIG\Postcode\Services\Converter\Factory;
use Magento\Framework\HTTP\PhpEnvironment\ServerAddress;

class Api
{
    /**
     * @var HttpClient
     */
    private $httpClient;

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
     * @param HttpClient          $client
     * @param ApiConfiguration    $apiConfiguration
     * @param ClientConfiguration $clientConfiguration
     * @param Factory             $converter
     * @param ServerAddress       $serverAddress
     */
    public function __construct(
        HttpClient $client,
        ApiConfiguration $apiConfiguration,
        ClientConfiguration $clientConfiguration,
        Factory $converter,
        ServerAddress $serverAddress
    ) {
        $this->httpClient          = $client;
        $this->apiConfiguration    = $apiConfiguration;
        $this->clientConfiguration = $clientConfiguration;
        $this->converter           = $converter;
        $this->serverAddress       = $serverAddress;
    }

    /**
     * Execute getRequest
     *
     * @param EndpointInterface $endpoint
     *
     * @return array|string
     */
    public function getRequest(EndpointInterface $endpoint)
    {
        $this->httpClient->resetParameters();

        $this->setUri($endpoint);
        $this->setHeaders($endpoint);
        $this->setParameter($endpoint);

        try {
            $response = $this->httpClient->send();
            $response = $this->converter->convert('response', $response->getBody(), $endpoint->getResponseKeys());
        } catch (RuntimeException $exception) {
            $response = [
                'success' => false,
                'error'   => __('%1 : Laminas Http Client exception', $exception->getCode())
            ];
        } catch (\TIG\Postcode\Exception $exception) {
            $response = [
                'success' => false,
                'error'   => __('%1 : Postcode exception', $exception->getCode())
            ];
        }

        return $response;
    }

    /**
     * Add headers to request
     *
     * Set the headers, but only if the api version is 4 or higher. Before version 4 user data is parsed within the
     * requestData array as ['ClientId' => 'xxxx', 'SecureCode' => 'xxxx']
     *
     * @param EndpointInterface $endpoint
     *
     * @throws \Zend_Http_Client_Exception
     */
    private function setHeaders(EndpointInterface $endpoint)
    {
        $version = str_replace('v', '', $this->apiConfiguration->getVersion());

        if ((int) $version >= 4 || $endpoint->getCountry() === 'BE' || $endpoint->getCountry() === 'DE') {
            $this->httpClient->setOptions(['strict' => false]);
            $this->httpClient->setHeaders([
                'X-ClientId'   => $this->clientConfiguration->getClientId(),
                'X-SecureCode' => $this->clientConfiguration->getSecureCode()
            ]);

            return;
        }

        $params                = $endpoint->getRequestData();
        $params['client_id']   = $this->clientConfiguration->getClientId();
        $params['secure_code'] = $this->clientConfiguration->getSecureCode();

        $endpoint->setRequestData($params);
    }

    /**
     * Add parameters to request
     *
     * @param EndpointInterface $endpoint
     */
    private function setParameter(EndpointInterface $endpoint)
    {
        $this->httpClient->setMethod($endpoint->getMethod());

        $params              = $endpoint->getRequestData();
        $params['domain']    = $this->clientConfiguration->getDomainUrl();
        $params['remote_ip'] = $this->serverAddress->getServerAddress();

        if ($endpoint->getMethod() == Request::METHOD_GET) {
            $this->httpClient->setParameterGet($params);
        }

        if ($endpoint->getMethod() == Request::METHOD_POST) {
            $this->httpClient->setParameterPost($params);
        }
    }

    /**
     * Set URi for request
     *
     * @param EndpointInterface $endpoint
     *
     * @throws RuntimeException
     */
    private function setUri(EndpointInterface $endpoint)
    {
        $uri = $this->apiConfiguration->getBaseUri() . $endpoint->getEndpoint(); // NL

        if ($endpoint->getCountry() == 'BE') {
            $uri = $this->apiConfiguration->getBEBaseUri($endpoint->getEndpoint()) . $endpoint->getEndpoint(); // BE
        }

        if ($endpoint->getCountry() == 'DE') {
            $uri = $this->apiConfiguration->getDEBaseUri($endpoint->getEndpoint()) . $endpoint->getEndpoint(); // DE
        }

        $this->httpClient->setUri($uri);
    }
}
