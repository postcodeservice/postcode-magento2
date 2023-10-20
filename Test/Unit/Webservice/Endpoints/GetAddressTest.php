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
namespace TIG\Postcode\Test\Unit\Webservice\Endpoints;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Webservices\Endpoints\GetAddress;

class GetAddressTest extends TestCase
{
    /** @var GetAddress */
    protected $instanceClass = GetAddress::class;

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetEndpoint()
    {
        $instance = $this->getInstance();
        $result = $instance->getEndpoint();

        $this->assertEquals('getAddress/', $result);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetMethod()
    {
        $instance = $this->getInstance();
        $result = $instance->getMethod();

        $this->assertEquals('GET', $result);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testTheRequestDataIsSetCorrectly()
    {
        $requestData = ['postcode' => '1014BA', 'huisnummer' => '37'];
        $instance = $this->getInstance();
        $instance->setRequestData($requestData);

        $result = $instance->getRequestData();
        $this->assertEquals($requestData, $result);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetCountry()
    {
        $instance = $this->getInstance();
        $result = $instance->getCountry();
        $this->assertEquals('NL', $result);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetResponseKeys()
    {
        $instance = $this->getInstance();
        $result = $instance->getResponseKeys();
        $this->assertEquals(['street', 'city'], $result);
    }
}
