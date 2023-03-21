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
namespace TIG\Postcode\Webservices\Endpoints;

interface EndpointInterface
{
    /**
     * Call endpoint
     *
     * @return mixed
     */
    public function call();

    /**
     * Get endpoint
     *
     * @return string
     */
    public function getEndpoint();

    /**
     * Get request data
     *
     * @return array
     */
    public function getRequestData();

    /**
     * Set request data
     *
     * @param array $data
     *
     * @return mixed
     */
    public function setRequestData(array $data);

    /**
     * Get the method
     *
     * @return string
     */
    public function getMethod();

    /**
     * Get Country
     *
     * @return string
     */
    public function getCountry();

    /**
     * Get request keys
     *
     * @return array
     */
    public function getRequestKeys();

    /**
     * Get response keys
     *
     * @return array
     */
    public function getResponseKeys();
}
