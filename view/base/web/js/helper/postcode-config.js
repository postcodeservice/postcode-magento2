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
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
define(['underscore'], function(_) {
    var postcodeConfig = {
        'action_url': {
            'postcode_service': '/postcode/address/service',
            'postcode_be_getstreet': '/postcode/address/service/be/getstreet',
            'postcode_be_getpostcode': '/postcode/address/service/be/getpostcode'
        }
    };

    if ('checkoutConfig' in window && 'postcode' in window.checkoutConfig) {
        postcodeConfig = _.extend(postcodeConfig, window.checkoutConfig.postcode);
    }

    return {
        getWebserviceURL_NL : function() {
            return postcodeConfig.action_url.postcode_service;
        },

        getWebserviceURL_BE_Street: function() {
            return postcodeConfig.action_url.postcode_be_getstreet;
        },

        getWebserviceURL_BE_Postcode: function() {
            return postcodeConfig.action_url.postcode_be_getpostcode;
        }
    }
});
