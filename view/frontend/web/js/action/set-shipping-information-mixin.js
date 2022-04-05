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
 * to support@postcodeservice.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.com for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',

], function ($, wrapper, quote) {
    'use strict';

    function findAttribute(attributeName, address) {
        return address.customAttributes.find(
            function (element) {
                return element.attribute_code === attributeName;
            }
        );
    }
    return function (setShippingInformationAction) {

        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();

            if (shippingAddress === undefined || !shippingAddress) {
                return originalAction();
            }

            if (shippingAddress.customAttributes === undefined) {
                return originalAction();
            }

            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }

            var housenumber = findAttribute('tig_housenumber', shippingAddress);
            var housenumberAddition = findAttribute('tig_housenumber_addition', shippingAddress);
            var street = findAttribute('tig_street', shippingAddress);

            if (housenumber) {
                shippingAddress['extension_attributes']['tig_housenumber'] = housenumber.value;
            }

            if (housenumberAddition) {
                shippingAddress['extension_attributes']['tig_housenumber_addition'] = housenumberAddition.value;
            }

            if (street) {
                shippingAddress['extension_attributes']['tig_street'] = street.value;
            }

            return originalAction();
        });
    };
});
