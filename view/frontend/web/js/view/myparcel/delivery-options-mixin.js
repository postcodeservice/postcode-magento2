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
define([
    'mage/utils/wrapper',
    'uiRegistry'
], function (
    wrapper,
    Registry
) {
    'use strict';

    return function (deliveryOptions) {
        deliveryOptions.getHouseNumber = wrapper.wrapSuper(deliveryOptions.getHouseNumber, function (address) {
            var housenumberElement = Registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode-field-group.field-group.housenumber');

            if (housenumberElement === undefined) {
                return this._super(address);
            }

            var housenumber = housenumberElement.value();
            if (!housenumber) {
                return this._super(address);
            }

            return housenumber;
        });

        return deliveryOptions;
    };
});
