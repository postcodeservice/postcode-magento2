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
/* jshint ignore:start */
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function (
    $,
    wrapper,
    quote
) {
    'use strict';

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

            // < M2.3.0
            if (shippingAddress.customAttributes !== undefined && shippingAddress.customAttributes.tig_housenumber !== undefined) {
                shippingAddress['extension_attributes']['tig_housenumber']          = shippingAddress.customAttributes.tig_housenumber;
                shippingAddress['extension_attributes']['tig_housenumber_addition'] = shippingAddress.customAttributes.tig_housenumber_addition;
                return originalAction();
            }
            // >= M2.3.0
            if (shippingAddress.customAttributes.length > 0) {
                shippingAddress.customAttributes.forEach(function(customAttribute) {
                    if(customAttribute.attribute_code === 'tig_housenumber') {
                        shippingAddress['extension_attributes']['tig_housenumber']       = customAttribute.value;
                    } else if (customAttribute.attribute_code === 'tig_housenumber_addition') {
                        shippingAddress['extension_attributes']['tig_housenumber_addition'] = customAttribute.value;
                    }
                });
            }

            return originalAction();
        });
    };
});
/* jshint ignore:end */