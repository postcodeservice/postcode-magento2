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
namespace TIG\Postcode\Test\Unit\Services\Converter;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Services\Converter;

class FactoryTest extends TestCase
{
    protected $instanceClass = Converter\Factory::class;

    /**
     * @var Converter\Factory
     */
    public $instance;

    public function setUp()
    {
        parent::setUp();

        $this->instance = $this->getInstance([
            'converters' => [
                'request'  => $this->getObject(Converter\Request::class),
                'response' => $this->getObject(Converter\Response::class),
            ]
        ]);
    }

    public function testIncorrectClassImplementation()
    {
        $instance = $this->getInstance([
            'converters' => [
                'incorrect' => static::class
            ]
        ]);

        try {
            $instance->convert('type', 'data');
        } catch (\TIG\Postcode\Exception $exception) {
            $souldReceive = 'Class is not an implementation of '. Converter\ConverterInterface::class;
            $this->assertEquals($souldReceive, $exception->getMessage());
            return;
        }

        $this->fail('Should trow an exception, but got none');
    }

    public function testIncorrectConverterTypeGiven()
    {
        try {
            $this->instance->convert('incorrect-typ', 'some-data');
        } catch (\TIG\Postcode\Exception $exception) {
            $souldReceive = 'Could not find type incorrect-typ as converter';
            $this->assertEquals($souldReceive, $exception->getMessage());
            return;
        }

        $this->fail('Should trow an exception, but got none');
    }
}
