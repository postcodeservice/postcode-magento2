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

namespace TIG\Postcode\Controller\Address;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use TIG\Postcode\Webservices\Endpoints\GetAddress;
use TIG\Postcode\Webservices\Endpoints\GetBePostcode;
use TIG\Postcode\Webservices\Endpoints\GetBeStreet;
use TIG\Postcode\Webservices\Endpoints\GetDePostcode;
use TIG\Postcode\Webservices\Endpoints\GetDeStreet;
use TIG\Postcode\Services\Converter\Factory;

class Service extends Action
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var Factory
     */
    private $converter;

    /**
     * @var GetAddress // NL
     */
    private $getAddress;

    /**
     * @var GetBePostcode // BE
     */
    private $getBePostcode;

    /**
     * @var GetBeStreet // BE
     */
    private $getBeStreet;

    /**
     * @var GetDePostcode // DE
     */
    private $getDePostcode;

    /**
     * @var GetDeStreet // DE
     */
    private $getDeStreet;

    /**
     * Service constructor.
     *
     * @param Context       $context
     * @param JsonFactory   $jsonFactory
     * @param Factory       $converterFactory
     * @param GetAddress    $getAddress
     * @param GetBePostcode $getBePostcode
     * @param GetBeStreet   $getBeStreet
     * @param GetDePostcode $getDePostcode
     * @param GetDeStreet   $getDeStreet
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Factory $converterFactory,
        GetAddress $getAddress,
        GetBePostcode $getBePostcode,
        GetBeStreet $getBeStreet,
        GetDePostcode $getDePostcode,
        GetDeStreet $getDeStreet
    ) {
        parent::__construct($context);

        $this->jsonFactory   = $jsonFactory;
        $this->converter     = $converterFactory;
        $this->getAddress    = $getAddress;
        $this->getBePostcode = $getBePostcode;
        $this->getBeStreet   = $getBeStreet;
        $this->getDePostcode = $getDePostcode;
        $this->getDeStreet   = $getDeStreet;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $country = $this->getCountry($params);
        $method  = $this->getMethod($params, $country);

        $endpoint = $this->getEndpoint($country, $method);
        $params   = $this->converter->convert('request', $params, $endpoint->getRequestKeys());
        if (!$params) {
            return $this->returnFailure(__('Request validation failed'));
        }

        $endpoint->setRequestData($params);
        $result = $endpoint->call();
        if (!$result) {
            return $this->returnFailure(__('Response validation failed'));
        }

        return $this->returnJson($result);
    }

    /**
     * Get the method
     *
     * @param array|string $params
     * @param array|string $country
     *
     * @return string
     */
    private function getMethod($params, $country)
    {
        if (isset($params[$country])) {
            return $params[$country];
        }

        return 'postcodecheck';
    }

    /**
     * Get country code
     *
     * @param array|string $params
     *
     * @return string
     */
    private function getCountry($params): string
    {
        if (key($params) == 'be') { // BE
            return key($params);
        }

        if (key($params) == 'de') { // DE
            return key($params);
        }

        return 'nl';
    }

    /**
     * Return failure
     *
     * @param string|int $error
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function returnFailure($error)
    {
        return $this->returnJson([
            'success' => false,
            'error'   => $error
        ]);
    }

    /**
     * Return Json
     *
     * @param array|string $data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function returnJson($data)
    {
        $response = $this->jsonFactory->create();

        return $response->setData($data);
    }

    /**
     * Get endpoint via country and param
     *
     * @param string $country
     * @param string $method
     *
     * @return GetAddress|GetBePostcode|GetBeStreet|GetDePostcode|GetDeStreet
     */
    private function getEndpoint($country, $method)
    {
        if ($country == 'be' && $method == 'getpostcode') { // BE
            return $this->getBePostcode;
        }

        if ($country == 'be' && $method == 'getstreet') { // BE
            return $this->getBeStreet;
        }

        if ($country == 'de' && $method == 'getpostcode') { // DE
            return $this->getDePostcode;
        }

        if ($country == 'de' && $method == 'getstreet') { // DE
            return $this->getDeStreet;
        }

        return $this->getAddress; // NL
    }
}
