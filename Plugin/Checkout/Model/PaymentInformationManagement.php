<?php
namespace TIG\Postcode\Plugin\Checkout\Model;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use TIG\Postcode\Helper\TigFieldsHelper;

class PaymentInformationManagement
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
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param                                                      $cartId
     * @param PaymentInterface                                     $paymentMethod
     * @param AddressInterface                                     $address
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSavePaymentInformation(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $address
    ) {
        $extAttributes = $address->getExtensionAttributes();
        $this->fieldsHelper->copyFieldsFromExtensionAttributesToObject($extAttributes, $address);
    }
}
