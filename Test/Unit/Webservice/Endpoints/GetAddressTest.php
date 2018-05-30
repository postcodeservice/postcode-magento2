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
namespace TIG\Postcode\Test\Unit\Webservice\Endpoints;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Webservices\Endpoints\GetAddress;

class GetAddressTest extends TestCase
{
    protected $instanceClass = GetAddress::class;

    public function testGetEndpoint()
    {
        $instance = $this->getInstance();
        $result = $instance->getEndpoint();

        $this->assertEquals('getAddress/', $result);
    }

    public function testGetMethod()
    {
        $instance = $this->getInstance();
        $result = $instance->getMethod();

        $this->assertEquals('GET', $result);
    }

    public function testTheRequestDataIsSetCorrectly()
    {
        $requestData = ['postcode' => '1014BA', 'huisnummer' => '37'];
        $instance = $this->getInstance();
        $instance->setRequestData($requestData);

        $result = $instance->getRequestData();
        $this->assertEquals($requestData, $result);
    }
}