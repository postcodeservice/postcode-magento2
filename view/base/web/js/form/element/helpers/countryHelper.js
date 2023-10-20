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

define([
    'underscore',
    'knockout',
    '../../../helper/field-types',
    
    '../../../postcode-handler/postcode-nl',
    '../../../postcode-handler/postcode-be',
    '../../../postcode-handler/postcode-de'
], function (_, ko, FieldTypes, postcodeNL, postcodeBE, postcodeDE) {
    'use strict';
    
    const KnownHandlers = {
        'NL': postcodeNL,
        'BE': postcodeBE,
        'DE': postcodeDE
    };
    
    return {
        defaults: {
            imports: {
                updateCountry: '${ $.parentName }.country_id:value',
                updateCountryOptions: '${ $.parentName }.country_id:indexedOptions'
            },
            modules: {
                city: '${ $.parentName }.city'
            }
        },
        
        /**
         * Called when Country is updated (ISO Value)
         *
         * @param value
         */
        updateCountry: function (value) {
            if (!value) {
                return;
            }
            this.countryISO = value;
            this.changeHandlerAfterUpdate();
        },
        
        /**
         * Called when Country Options are updated (contains Postcode Config)
         *
         * @param value
         */
        updateCountryOptions: function (value) {
            if (!value) {
                return;
            }
            this.countryOptions = value;
            this.changeHandlerAfterUpdate();
        },
        
        /**
         * Change Postcode Handler after update
         */
        changeHandlerAfterUpdate: function () {
            if (!this.countryOptions || !this.countryISO) {
                return;
            }
            
            if (
                this.currentPostcodeHandler &&
                this.currentPostcodeHandler.getISOCode() === this.countryISO
            ) {
                return;
            }
            
            if (this.currentPostcodeHandler) {
                this.currentPostcodeHandler.destroy();
                this.currentPostcodeHandler = null;
            }
            
            if (!(this.countryISO in KnownHandlers)) {
                return;
            }
            
            var isoOptions = {};
            if (this.countryOptions && this.countryISO in this.countryOptions) {
                isoOptions = this.countryOptions[this.countryISO];
            }
            
            this.currentPostcodeHandler = new KnownHandlers[this.countryISO](isoOptions, this);
            
            if (this.uiInitialized) {
                this.currentPostcodeHandler.reset();
                this.currentPostcodeHandler.handle();
            }
        }
    };
});
