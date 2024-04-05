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

    /** @var string */
    private $base = 'https://api.postcodeservice.com/nl/';
    /** @var string */
    private $beBase = 'https://api.postcodeservice.com/be/';
    /** @var string */
    private $deBase = 'https://api.postcodeservice.com/de/';
    private $frBase = 'https://api.postcodeservice.com/fr/';
    /** @var string */
    private $version = 'v6';
    /** @var string */
    private $postcodeVersion = 'v3';
    /** @var string */
    private $streetVersion = 'v3';

    /** @var string */
    private $postcodeEndpoint = 'zipcode-find/';
    /** @var string */
    private $streetEndpoint = 'street-find/';

    /** @var string */
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
        $this->setXpath(ApiConfiguration::XPATH_API_NL_BASE, $this->base);
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
    public function testDeGetBase()
    {
        $this->setXpath(ApiConfiguration::XPATH_API_DE_BASE, $this->deBase);
        $this->assertEquals($this->deBase, $this->instance->getBase('DE'));
    }

    /**
     * @return void
     */
    public function testFrGetBase()
    {
        $this->setXpath(ApiConfiguration::XPATH_API_FR_BASE, $this->deBase);
        $this->assertEquals($this->deBase, $this->instance->getBase('FR'));
    }

    /**
     * @return void
     */
    public function testGetVersion()
    {
        $this->setXpath(ApiConfiguration::XPATH_API_NL_POSTCODE_VERSION, $this->version);
        $this->assertEquals($this->version, $this->instance->getVersion());
    }

    /**
     * @return void
     */
    public function testGetType()
    {
        $this->setXpath(ApiConfiguration::XPATH_API_NL_TYPE, $this->type);
        $this->assertEquals($this->type, $this->instance->getType());
    }

    /**
     * @return void
     */
    public function testGetBaseUri()
    {
        $this->setXpathConsecutive(
            [
                ApiConfiguration::XPATH_API_NL_BASE,
                ApiConfiguration::XPATH_API_NL_POSTCODE_VERSION,
                ApiConfiguration::XPATH_API_NL_TYPE
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
        $this->assertEquals($expected, $this->instance->getBEBaseUri($this->postcodeEndpoint));
    }

    /**
     * @return void
     */
    public function testGetDeBasePostcodeUri()
    {
        $this->setXpathConsecutive(
            [
                ApiConfiguration::XPATH_API_DE_BASE,
                ApiConfiguration::XPATH_API_DE_POSTCODE_VERSION,
                ApiConfiguration::XPATH_API_DE_STREET_VERSION,
            ],
            [
                $this->deBase,
                $this->postcodeVersion,
                $this->streetVersion
            ]
        );

        $expected = $this->deBase . '/' . $this->postcodeVersion . '/';
        $this->assertEquals($expected, $this->instance->getDEBaseUri($this->postcodeEndpoint));
    }

    /**
     * @return void
     */
    public function testGetFrBasePostcodeUri()
    {
        $this->setXpathConsecutive(
            [
                ApiConfiguration::XPATH_API_FR_BASE,
                ApiConfiguration::XPATH_API_FR_POSTCODE_VERSION,
                ApiConfiguration::XPATH_API_FR_STREET_VERSION,
            ],
            [
                $this->frBase,
                $this->postcodeVersion,
                $this->streetVersion
            ]
        );

        $expected = $this->frBase . '/' . $this->postcodeVersion . '/';
        $this->assertEquals($expected, $this->instance->getFRBaseUri($this->postcodeEndpoint));
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
        $this->assertEquals($expected, $this->instance->getBEBaseUri($this->streetEndpoint));
    }

    /**
     * @return void
     */
    public function testGetDeBaseStreetUri()
    {
        $this->setXpathConsecutive(
            [
                ApiConfiguration::XPATH_API_DE_BASE,
                ApiConfiguration::XPATH_API_DE_POSTCODE_VERSION,
                ApiConfiguration::XPATH_API_DE_STREET_VERSION,
            ],
            [
                $this->deBase,
                $this->postcodeVersion,
                $this->streetVersion
            ]
        );

        $expected = $this->deBase . '/' . $this->streetVersion . '/';
        $this->assertEquals($expected, $this->instance->getDEBaseUri($this->streetEndpoint));
    }

    /**
     * @return void
     */
    public function testGetFrBaseStreetUri()
    {
        $this->setXpathConsecutive(
            [
                ApiConfiguration::XPATH_API_FR_BASE,
                ApiConfiguration::XPATH_API_FR_POSTCODE_VERSION,
                ApiConfiguration::XPATH_API_FR_STREET_VERSION,
            ],
            [
                $this->frBase,
                $this->postcodeVersion,
                $this->streetVersion
            ]
        );

        $expected = $this->frBase . '/' . $this->streetVersion . '/';
        $this->assertEquals($expected, $this->instance->getFRBaseUri($this->streetEndpoint));
    }
}
