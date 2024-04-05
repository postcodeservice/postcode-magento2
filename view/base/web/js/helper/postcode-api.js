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

define(['jquery', './postcode-config'], function ($, postcodeConfig) {
    
    return {
        getPostCodeNL: function (zipcode, house_number) {
            return $.ajax(
                {
                    method: 'GET',
                    url: postcodeConfig.getWebserviceURL_NL(),
                    showLoader: false, // If you prefer a loader, set this value to true
                    data: {
                        house_number: house_number,
                        zipcode: zipcode
                    }
                });
        },
        
        getPostCodeBE: function (postcode) {
            // multi_results = 1 enables support for bilingual cities such as Brussel, see
            // https://developers.postcodeservice.com/#belgium-api-GETbe-v4-zipcode-find
            return $.ajax({
                method: 'GET',
                url: postcodeConfig.getWebserviceURL_BE_Postcode(),
                data: {
                    zipcodezone: postcode,
                    multi_results: 1
                }
            });
        },
        
        getStreetBE: function (postcode, street, city) {
            return $.ajax({
                method: 'GET',
                url: postcodeConfig.getWebserviceURL_BE_Street(),
                data: {
                    zipcode: postcode,
                    city: city,
                    street: street
                }
            });
        },
        
        getPostCodeDE: function (postcode) {
            return $.ajax({
                method: 'GET',
                url: postcodeConfig.getWebserviceURL_DE_Postcode(),
                data: {
                    zipcodezone: postcode
                }
            });
        },
        
        getStreetDE: function (postcode, street, city) {
            return $.ajax({
                method: 'GET',
                url: postcodeConfig.getWebserviceURL_DE_Street(),
                data: {
                    zipcode: postcode,
                    city: city,
                    street: street
                }
            });
        },
        
        getPostCodeFR: function (postcode) {
            return $.ajax({
                method: 'GET',
                url: postcodeConfig.getWebserviceURL_FR_Postcode(),
                data: {
                    zipcodezone: postcode
                }
            });
        },
        
        getStreetFR: function (postcode, street, city) {
            return $.ajax({
                method: 'GET',
                url: postcodeConfig.getWebserviceURL_FR_Street(),
                data: {
                    zipcode: postcode,
                    city: city,
                    street: street
                }
            });
        }
    };
});
