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
 * to support@postcodeservice.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Postcode\Test\Unit\Config\Source;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Config\Source\Modus;

class ModusTest extends TestCase
{
    protected $instanceClass = Modus::class;

    public function testToOptionsArray()
    {
        $instance = $this->getInstance();
        $result   = $instance->toOptionArray();

        $this->assertCount(3, $result);

        foreach ($result as $mode) {
            $this->assertArrayHasKey('label', $mode);
            $this->assertArrayHasKey('value', $mode);

            $inArray = in_array($mode['value'], ['0', '1', '2']);
            $this->assertTrue($inArray);
        }
    }
}
