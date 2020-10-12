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

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'TIG_Postcode/js/action/set-shipping-information-mixin': true
            },
            'Amasty_Checkout/js/action/set-shipping-information' : {
                'TIG_Postcode/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/set-billing-address' : {
                'TIG_Postcode/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'TIG_Postcode/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/create-billing-address': {
                'TIG_Postcode/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'TIG_Postcode/js/action/set-billing-address-mixin': true
            },
            'Magento_Ui/js/form/element/abstract': {
                'TIG_Postcode/js/view/form/be/autocompleteElement': true
            },
            'mage/menu': {
                'TIG_Postcode/js/lib/mage/mage-mixin': true
            },
            'MyParcelNL_Magento/js/view/delivery-options': {
                'TIG_Postcode/js/view/myparcel/delivery-options-mixin': true
            }
        }
    },
    map : {
        '*': {
            'Magento_Checkout/template/shipping-address/address-renderer/default.html' : 'TIG_Postcode/template/shipping-address/address-renderer/default.html',
            'Magento_Checkout/template/shipping-information/address-renderer/default.html' : 'TIG_Postcode/template/shipping-information/address-renderer/default.html',
            'Magento_Checkout/template/billing-address/details.html' : 'TIG_Postcode/template/billing-address/details.html',
            'Amasty_CustomerAttributes/js/action/set-shipping-information-mixin' : 'TIG_Postcode/js/action/set-shipping-information-mixin'
        }
    }
};
