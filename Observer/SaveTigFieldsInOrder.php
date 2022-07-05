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

namespace TIG\Postcode\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SaveTigFieldsInOrder implements ObserverInterface
{
    /**
     * @param Observer $observer
     *
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        if ($quote->getBillingAddress()) {
            $order->getBillingAddress()->setTigStreet($quote->getBillingAddress()->getExtensionAttributes()->getTigStreet());
            $order->getBillingAddress()->setTigHousenumber($quote->getBillingAddress()->getExtensionAttributes()->getTigHousenumber());
            $order->getBillingAddress()->setTigHousenumberAddition($quote->getBillingAddress()->getExtensionAttributes()->getTigHousenumberAddition());
        }

        if (!$quote->isVirtual()) {
            $order->getShippingAddress()->setTigStreet($quote->getShippingAddress()->getExtensionAttributes()->getTigStreet());
            $order->getShippingAddress()->setTigHousenumber($quote->getShippingAddress()->getExtensionAttributes()->getTigHousenumber());
            $order->getShippingAddress()->setTigHousenumberAddition($quote->getShippingAddress()->getExtensionAttributes()->getTigHousenumberAddition());
        }
        return $this;
    }
}
