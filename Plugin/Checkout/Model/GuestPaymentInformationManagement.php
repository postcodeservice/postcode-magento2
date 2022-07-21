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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Postcode\Plugin\Checkout\Model;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use TIG\Postcode\Helper\TigFieldsHelper;

class GuestPaymentInformationManagement
{
    /**
     * @var TigFieldsHelper
     */
    private $fieldsHelper;

    /**
     * @param TigFieldsHelper $fieldsHelper
     */
    public function __construct(
        TigFieldsHelper $fieldsHelper
    ) {
        $this->fieldsHelper = $fieldsHelper;
    }

    /**
     * Before plugin on SavePaymentInformation
     *
     * @see \Magento\Checkout\Model\GuestPaymentInformationManagement::savePaymentInformation()
     *
     * @param \Magento\Checkout\Model\GuestPaymentInformationManagement $subject
     * @param int                                                       $cartId
     * @param string                                                    $email
     * @param PaymentInterface                                          $paymentMethod
     * @param AddressInterface                                          $address
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSavePaymentInformation(
        \Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
        $cartId,
        $email,
        PaymentInterface $paymentMethod,
        AddressInterface $address
    ) {
        $extAttributes = $address->getExtensionAttributes();
        $this->fieldsHelper->copyFieldsFromExtensionAttributesToObject($extAttributes, $address);
    }
}
