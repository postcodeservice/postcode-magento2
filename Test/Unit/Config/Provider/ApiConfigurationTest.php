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
namespace TIG\Postcode\Test\Unit\Config\Provider;

use TIG\Postcode\Config\Provider\ApiConfiguration;

class ApiConfigurationTest extends AbstractConfigurationTest
{
    /** @var ApiConfiguration */
    protected $instanceClass = ApiConfiguration::class;

    /** @var string  */
    private $base = 'https://api.postcodeservice.com/nl/';
    /** @var string  */
    private $beBase = 'https://api.postcodeservice.com/be/';

    /** @var string  */
    private $version         = 'v3';
    /** @var string  */
    private $postcodeVersion = 'v2';
    /** @var string  */
    private $streetVersion   = 'v2';

    /** @var string  */
    private $postcodeEndpoint = 'postcode-find/';
    /** @var string  */
    private $streetEndpoint   = 'street-find/';

    /** @var string  */
    private $type = 'json';

    /**
     * @var ApiConfiguration
     */
    private $instance;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->instance = $this->getInstance();
    }

    /**
     * @return void
     */
    public function testGetBase()
    {
        $this->setXpath(ApiConfiguration::XPATH_API_BASE, $this->base);
        $this->assertEquals($this->base, $this->instance->getBase());
    }

    /**
     * @return void
     */
    public function testBeGetBase()
    {
        $this->setXpath(ApiConfiguration::XPATH_API_BE_BASE, $this->beBase);
        $this->assertEquals($this->beBase, $this->instance->getBase('BE'));
    }

    /**
     * @return void
     */
    public function testGetVersion()
    {
        $this->setXpath(ApiConfiguration::XPATH_API_VERSION, $this->version);
        $this->assertEquals($this->version, $this->instance->getVersion());
    }

    /**
     * @return void
     */
    public function testGetType()
    {
        $this->setXpath(ApiConfiguration::XPATH_API_TYPE, $this->type);
        $this->assertEquals($this->type, $this->instance->getType());
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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
