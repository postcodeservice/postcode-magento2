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
namespace TIG\Postcode\Plugin\Address\Management;

use Magento\Quote\Model\BillingAddressManagement as MagentoManagement;
use Magento\Quote\Api\Data\AddressInterface;

class Billing
{
    public function beforeAssign(
        MagentoManagement $subject,
        $cartId,
        AddressInterface $address,
        $useForShipping = false
    ) {
        $attributes = $address->getExtensionAttributes();
        if (!$attributes) {
            return [$cartId, $address];
        }

        if (!$attributes->getTigHousenumber()) {
            return [$cartId, $address];
        }

        $streetData = $address->getStreet();

        // @todo : Add field configuration for parsing the housenumber into first or second street field.
        $street =  $streetData[0] . ' ' . $attributes->getTigHousenumber();

        $address->setStreet($street);

        return [$cartId, $address, $useForShipping];
    }
}
