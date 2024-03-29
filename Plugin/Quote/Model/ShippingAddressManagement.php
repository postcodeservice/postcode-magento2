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

namespace TIG\Postcode\Plugin\Quote\Model;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\ShippingAddressManagement as MagentoShippingAddressManagement;
use Psr\Log\LoggerInterface;
use TIG\Postcode\Helper\TigFieldsHelper;

class ShippingAddressManagement
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
     * Before Plugin on Assign function
     *
     * @see MagentoShippingAddressManagement::assign()
     *
     * @param MagentoShippingAddressManagement               $subject
     * @param int                                            $cartId
     * @param AddressInterface                               $address
     */
    public function beforeAssign(
        MagentoShippingAddressManagement $subject,
        $cartId,
        AddressInterface $address
    ) {
        $extAttributes = $address->getExtensionAttributes();
        $this->fieldsHelper->copyFieldsFromExtensionAttributesToObject($extAttributes, $address);
    }
}
