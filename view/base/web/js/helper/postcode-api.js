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
define(['jquery', './postcode-config'], function($, postcodeConfig){

    return {
        getPostCodeNL: function (postcode, house_number) {
            return $.ajax(
                {
                    method: 'GET',
                    url: postcodeConfig.getWebserviceURL_NL(),
                    showLoader: true,
                    data: {
                        huisnummer: house_number,
                        postcode: postcode
                    }
                });
        },

        getPostCodeBE: function (postcode) {
            return $.ajax({
                method         : 'GET',
                url            : postcodeConfig.getWebserviceURL_BE_Postcode(),
                data           : {
                    zipcodezone : postcode
                }
            });
        },

        getStreetBe: function (postcode, street, city) {
            return $.ajax({
                method         : 'GET',
                url            : postcodeConfig.getWebserviceURL_BE_Street(),
                data   : {
                    zipcode : postcode,
                    city    : city,
                    street  : street
                },
            });
        }
    }
});
