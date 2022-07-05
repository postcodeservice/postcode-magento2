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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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

namespace TIG\Postcode\Helper;

use Magento\Customer\Api\Data\AddressInterface as CustomerAddressInterface;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Psr\Log\LoggerInterface;

class TigFieldsHelper
{
    const TIG_FIELDS = [
        'tig_street',
        'tig_housenumber',
        'tig_housenumber_addition',
    ];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ){
        $this->logger = $logger;
    }

    /**
     * @param $extensionAttributes
     * @param $object
     */
    public function copyFieldsFromExtensionAttributesToObject($extensionAttributes, $object) {
        if (empty($extensionAttributes)) {
            return;
        }
        try {
            $object->setTigHousenumber($extensionAttributes->getTigHousenumber());
            $object->setTigHousenumberAddition($extensionAttributes->getTigHousenumberAddition());
            $object->setTigStreet($extensionAttributes->getTigStreet());
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param QuoteAddressInterface    $quoteAddress
     * @param CustomerAddressInterface $customerAddress
     */
    public function copyFieldsFromQuoteAddressToCustomerAddress(
        QuoteAddressInterface $quoteAddress,
        CustomerAddressInterface $customerAddress
    ) {
        try {
            foreach(self::TIG_FIELDS as $fieldName){
                $value = $quoteAddress->getData($fieldName);
                $customerAddress->setCustomAttribute($fieldName, $value);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
