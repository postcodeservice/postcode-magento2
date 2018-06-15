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
namespace TIG\Postcode\Test\Unit\Plugin\Address\Management;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Services\Address\StreetFields;
use \Magento\Quote\Model\Quote\Address;
use TIG\Postcode\Plugin\Address\Management\Billing;
use Magento\Quote\Api\Data\AddressExtensionInterface;

class BillingTest extends TestCase
{
    protected $instanceClass = Billing::class;

    public function testBeforeAssignWithCorrectData()
    {
        $extensionAttributeMock = $this->getFakeMock(AddressExtensionInterface::class)
            ->setMethods(
                ['getTigHousenumber',
                 'getTigHousenumberAddition',
                 'setTigHousenumber',
                 'setTigHousenumberAddition',
                 'getCheckoutFields',
                 'setCheckoutFields'
                ]
            )->getMock();

        $attributeHousenumber = $extensionAttributeMock->expects($this->any())->method('getTigHousenumber');
        $attributeHousenumber->willReturn('37');

        $address = $this->getObject(Address::class);
        $address->setStreet('kabelweg');
        $address->setExtensionAttributes($extensionAttributeMock);

        $parseMock = $this->getFakeMock(StreetFields::class)->getMock();
        $parseExpects = $parseMock->expects($this->once());
        $parseExpects->method('parse');
        $parseExpects->willReturn('kabelweg 37');

        $newAddress = $address;
        $newAddress->setStreet('kabelweg 37');

        $instance = $this->getInstance([
            'streetFields' => $parseMock
        ]);

        $result = $instance->beforeAssign(null, 1, $address);
        $this->assertEquals([1, $newAddress, false], $result);
    }

    public function testBeforeAssignWithoutAttributes()
    {
        $address  = $this->getObject(Address::class);
        $instance = $this->getInstance();

        $expected = [1, $address, false];
        $this->assertEquals($expected, $instance->beforeAssign(null, 1, $address));
    }

    public function testBeforeAssignWithIncorrectAttributes()
    {
        $extensionAttributeMock = $this->getFakeMock(AddressExtensionInterface::class)
            ->setMethods(
                [
                 'getTigHousenumber',
                 'getCheckoutFields',
                 'setCheckoutFields'
                ]
            )->getMock();

        $address  = $this->getObject(Address::class);
        $address->setExtensionAttributes($extensionAttributeMock);
        $instance = $this->getInstance();

        $expected = [1, $address, false];
        $this->assertEquals($expected, $instance->beforeAssign(null, 1, $address));
    }
}
