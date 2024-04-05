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
use TIG\Postcode\Exception;
use TIG\Postcode\Webservices\Endpoints\GetNLAddressValidation;
use TIG\Postcode\Webservices\Endpoints\GetBEZipcodeFind;
use TIG\Postcode\Webservices\Endpoints\GetBEStreetFind;
use TIG\Postcode\Webservices\Endpoints\GetDEZipcodeFind;
use TIG\Postcode\Webservices\Endpoints\GetDEStreetFind;
use TIG\Postcode\Webservices\Endpoints\GetFRZipcodeFind;
use TIG\Postcode\Webservices\Endpoints\GetFRStreetFind;
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
     * @var GetNLAddressValidation // NL
     */
    private $getNLAddressValidation;

    /**
     * @var GetBEZipcodeFind // BE
     */
    private $getBePostcode;

    /**
     * @var GetBEStreetFind // BE
     */
    private $getBeStreet;

    /**
     * @var GetDEZipcodeFind // DE
     */
    private $getDePostcode;

    /**
     * @var GetDEStreetFind // DE
     */
    private $getDeStreet;
    /**
     * @var GetFRZipcodeFind // FR
     */
    private $getFrPostcode;

    /**
     * @var GetFRStreetFind // FR
     */
    private $getFrStreet;

    /**
     * Service constructor.
     *
     * @param Context                $context
     * @param JsonFactory            $jsonFactory
     * @param Factory                $converterFactory
     * @param GetNLAddressValidation $getNLAddressValidation
     * @param GetBEZipcodeFind       $getBePostcode
     * @param GetBEStreetFind        $getBeStreet
     * @param GetDEZipcodeFind       $getDePostcode
     * @param GetDEStreetFind        $getDeStreet
     * @param GetFRZipcodeFind       $getFrPostcode
     * @param GetFRStreetFind        $getFrStreet
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Factory $converterFactory,
        GetNLAddressValidation $getNLAddressValidation,
        GetBEZipcodeFind $getBePostcode,
        GetBEStreetFind $getBeStreet,
        GetDEZipcodeFind $getDePostcode,
        GetDEStreetFind $getDeStreet,
        GetFRZipcodeFind $getFrPostcode,
        GetFRStreetFind $getFrStreet,
    ) {
        parent::__construct($context);

        $this->jsonFactory            = $jsonFactory;
        $this->converter              = $converterFactory;
        $this->getNLAddressValidation = $getNLAddressValidation;
        $this->getBePostcode          = $getBePostcode;
        $this->getBeStreet            = $getBeStreet;
        $this->getDePostcode          = $getDePostcode;
        $this->getDeStreet            = $getDeStreet;
        $this->getFrPostcode          = $getFrPostcode;
        $this->getFrStreet            = $getFrStreet;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\Result\Json
     * @throws Exception
     */
    public function execute(): \Magento\Framework\Controller\Result\Json
    {
        // Get request parameters
        $params = $this->getRequest()->getParams();

        // Determine the country and method based on the parameters
        $country = $this->getCountry($params);
        $method  = $this->getMethod($params, $country);

        // Get the endpoint based on the country and method
        $endpoint = $this->getEndpoint($country, $method);

        // Convert the parameters for the request
        $params = $this->converter->convert('request', $params, $endpoint->getRequestKeys());

        // If the parameters are not valid, throw an exception as a json object
        if (!$params) {
            return $this->returnFailure(__('Request keys validation failed'));
        }

        // Set the request data and make the call
        $endpoint->setRequestData($params);
        $result = $endpoint->call();

        // If the result is not valid, throw an exception as a json object
        if (!$result) {
            return $this->returnFailure(__('Response keys validation failed'));
        }

        // Return the result as JSON
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
        $validCountries = ['be', 'de', 'fr'];
        $country        = key($params);

        return in_array($country, $validCountries) ? $country : 'nl';
    }

    /**
     * Return failure
     *
     * @param int|string $error
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function returnFailure(int|string $error): \Magento\Framework\Controller\Result\Json
    {
        return $this->returnJson(
            [
                'success' => false,
                'error'   => $error
            ]
        );
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
     * @return GetDEZipcodeFind|GetNLAddressValidation|GetBEStreetFind|GetBEZipcodeFind|GetDEStreetFind|GetFRZipcodeFind|GetFRStreetFind
     */
    private function getEndpoint(string $country, string $method): GetDEZipcodeFind|GetNLAddressValidation|GetBEStreetFind|GetBEZipcodeFind|GetDEStreetFind|GetFRZipcodeFind|GetFRStreetFind
    {
        return match ($country) {
            'be' => match ($method) {
                'getpostcode' => $this->getBePostcode,
                'getstreet' => $this->getBeStreet,
            },
            'de' => match ($method) {
                'getpostcode' => $this->getDePostcode,
                'getstreet' => $this->getDeStreet,
            },
            'fr' => match ($method) {
                'getpostcode' => $this->getFrPostcode,
                'getstreet' => $this->getFrStreet,
            },
            default => $this->getNLAddressValidation,
        };
    }
}
