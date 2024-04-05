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
namespace TIG\Postcode\Test\Unit\Services\Validation;

use TIG\Postcode\Services\Validation;

class RequestTest extends TestInterface
{
    /** @var Validation\Request */
    protected $instanceClass = Validation\Request::class;

    /**
     * @return void
     * @throws \Exception
     */
    public function testWhenRequestHasNoData()
    {
        $this->assertFalse($this->getInstance()->validate('request'));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetAndGetKeys()
    {
        $instance = $this->getInstance();
        $instance->setKeys(['zipcode', 'house_number']);
        $this->assertEquals($instance->getKeys(), ['zipcode', 'house_number']);
    }
}
