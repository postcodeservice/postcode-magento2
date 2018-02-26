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

use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\Data\AddressInterface;
use TIG\Postcode\Services\Address\StreetFields;

class Payment
{
    /**
     * @var StreetFields
     */
    private $streetParser;

    public function __construct(
        StreetFields $streetFields
    ) {
        $this->streetParser = $streetFields;
    }

    // @codingStandardsIgnoreLine
    public function beforeSavePaymentInformation(
        PaymentInformationManagement $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $attributes = $billingAddress->getExtensionAttributes();
        if (empty($attributes)) {
            return [$cartId, $paymentMethod, $billingAddress];
        }

        if (!$attributes->getTigHousenumber()) {
            return [$cartId, $paymentMethod, $billingAddress];
        }

        $street = $this->streetParser->parse($billingAddress->getStreet(), $attributes);
        $billingAddress->setStreet($street);

        return [$cartId, $paymentMethod, $billingAddress];
    }
}