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
namespace TIG\Postcode\Plugin\Mageplaza;

use Mageplaza\Osc\Api\CheckoutManagementInterface;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use TIG\Postcode\Services\Address\StreetFields;
use TIG\Postcode\Services\Address\AttributeParser;

class CheckoutManagement
{
    /**
     * @var StreetFields
     */
    private $streetParser;

    /**
     * @var AttributeParser
     */
    private $attributeParser;

    public function __construct(
        StreetFields $streetFields,
        AttributeParser $attributeParser
    ) {
        $this->streetParser = $streetFields;
        $this->attributeParser = $attributeParser;
    }

    // @codingStandardsIgnoreLine
    public function beforeSaveCheckoutInformation(
        CheckoutManagementInterface $subject,
        $cartId,
        ShippingInformationInterface $addressInformation,
        $customerAttributes = [],
        $additionInformation = []
    ) {
        if (empty($customerAttributes)) {
            return [$cartId, $addressInformation, $customerAttributes, $additionInformation];
        }

        if (!isset($customerAttributes['tig_housenumber'])) {
            return [$cartId, $addressInformation, $customerAttributes, $additionInformation];
        }

        $shippignAddress = $addressInformation->getShippingAddress();
        $attributes = $this->attributeParser->set($customerAttributes);
        $street = $this->streetParser->parse($shippignAddress->getStreet(), $attributes);
        $shippignAddress->setStreet($street);

        $addressInformation->setShippingAddress($shippignAddress);

        return [$cartId, $addressInformation, $customerAttributes, $additionInformation];
    }
}
