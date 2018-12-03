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
namespace TIG\Postcode\Test\Unit\Config\Provider;

use TIG\Postcode\Config\Provider\ApiConfiguration;

class ApiConfigurationTest extends AbstractConfigurationTest
{
    protected $instanceClass = ApiConfiguration::class;

    private $base = 'https://postcode.tig.nl/api';
    private $beBase = 'https://postcode-be.tig.nl/api/be';

    private $version         = 'v3';
    private $postcodeVersion = 'v2';
    private $streetVersion   = 'v2';

    private $postcodeEndpoint = 'postcode-find/';
    private $streetEndpoint   = 'street-find/';

    private $type = 'json';

    /**
     * @var ApiConfiguration
     */
    private $instance;

    public function setUp()
    {
        parent::setUp();
        $this->instance = $this->getInstance();
    }

    public function testGetBase()
    {
        $this->setXpath(ApiConfiguration::XPATH_API_BASE, $this->base);
        $this->assertEquals($this->base, $this->instance->getBase());
    }

    public function testBeGetBase()
    {
        $this->setXpath(ApiConfiguration::XPATH_API_BE_BASE, $this->beBase);
        $this->assertEquals($this->beBase, $this->instance->getBase('BE'));
    }

    public function testGetVersion()
    {
        $this->setXpath(ApiConfiguration::XPATH_API_VERSION, $this->version);
        $this->assertEquals($this->version, $this->instance->getVersion());
    }

    public function testGetType()
    {
        $this->setXpath(ApiConfiguration::XPATH_API_TYPE, $this->type);
        $this->assertEquals($this->type, $this->instance->getType());
    }

    public function testGetBaseUri()
    {
        $this->setXpathConsecutive(
            [
                ApiConfiguration::XPATH_API_BASE,
                ApiConfiguration::XPATH_API_VERSION,
                ApiConfiguration::XPATH_API_TYPE
            ],
            [
                $this->base,
                $this->version,
                $this->type
            ]
        );

        $expected = $this->base . '/' . $this->version . '/' . $this->type . '/';
        $this->assertEquals($expected, $this->instance->getBaseUri());
    }

    public function testGetBeBasePostcodeUri()
    {
        $this->setXpathConsecutive(
            [
                ApiConfiguration::XPATH_API_BE_BASE,
                ApiConfiguration::XPATH_API_BE_POSTCODE_VERSION,
                ApiConfiguration::XPATH_API_BE_STREET_VERSION,
            ],
            [
                $this->beBase,
                $this->postcodeVersion,
                $this->streetVersion
            ]
        );

        $expected = $this->beBase . '/' . $this->postcodeVersion . '/';
        $this->assertEquals($expected, $this->instance->getBeBaseUri($this->postcodeEndpoint));
    }

    public function testGetBeBaseStreetUri()
    {
        $this->setXpathConsecutive(
            [
                ApiConfiguration::XPATH_API_BE_BASE,
                ApiConfiguration::XPATH_API_BE_POSTCODE_VERSION,
                ApiConfiguration::XPATH_API_BE_STREET_VERSION,
            ],
            [
                $this->beBase,
                $this->postcodeVersion,
                $this->streetVersion
            ]
        );

        $expected = $this->beBase . '/' . $this->streetVersion . '/';
        $this->assertEquals($expected, $this->instance->getBeBaseUri($this->streetEndpoint));
    }
}
