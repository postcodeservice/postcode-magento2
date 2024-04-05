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
define(
    [
        // Importing required modules and dependencies
        'jquery',
        'underscore',
        './postcode-handler',
        '../helper/field-types',
        '../helper/postcode-api',
        'mage/translate'
    ],
    function ($, _, PostcodeHandler, FieldTypes, PostcodeApi, $t) {
        'use strict';
        
        // Define error messages for various error states
        const errorMessages = {
            ADDRESS_NOT_FOUND: 'Address not found with provided zipcode and house number. If correct, please enter address details manually.',
            VALIDATION_UNAVAILABLE: 'Address validation temporarily unavailable.',
            VALIDATION_FAILED: 'Could not perform address validation.'
        };
        
        // Define UI constants
        const UI = {
            SEARCH_DELAY_IN_MS: 0
        };
        
        // Define the possible states of the postcode handler
        const states = Object.seal({
            INIT: PostcodeHandler.INIT,
            IDLE: 'postcode_idle',
            POSTCODE_CALL_MADE: 'postcode_call_made',
            POSTCODE_CALL_FAILED: 'postcode_call_failed',
            POSTCODE_SHOW_FIELDS_SUGGESTION: 'postcode_show_fields_suggestion',
            POSTCODE_SHOW_FIELDS_EDIT: 'postcode_show_fields_edit'
        });
        
        // Define application configuration
        const appConfig = {
            COUNTRY: 'NL'
        };
        
        // Regular expression for validating Dutch zipcodes
        const postcodeNLRegex = /^[0-9]{4}\s?[A-Z]{2}$/i;
        
        // Function to get the appropriate error message based on the error_code in the data object
        function getErrorMessage(data)
        {
            if (data.error_code) {
                console.error('Postcodeservice.com extension: ' + JSON.stringify(data));
                switch(data.error_code) {
                    case 429:
                        return errorMessages.VALIDATION_UNAVAILABLE;
                    default:
                        return data.error_code > 400
                            ? errorMessages.VALIDATION_FAILED
                            : errorMessages.ADDRESS_NOT_FOUND;
                }
            }
            return errorMessages.ADDRESS_NOT_FOUND;
        }
        
        // Constructor function for the Dutch postcode handler
        function PostcodeHandlerNL(config, postcodeService)
        {
            this.debounceBeforeCall = null;
            this.data = {};
            PostcodeHandler.call(this, config, postcodeService);
            
            return (this);
        }
        
        // Inherit from the generic PostcodeHandler
        PostcodeHandlerNL.prototype = Object.create(PostcodeHandler.prototype);
        
        // Method to get the ISO code for Netherlands
        PostcodeHandlerNL.prototype.getISOCode = function () { return appConfig.COUNTRY;};
        
        // Method to call the API and handle the response
        PostcodeHandlerNL.prototype.callApi = _.debounce(function (postcode, house_number) {
            const self = this;
            this.setCurrentState(states.POSTCODE_CALL_MADE);
            
            // Call the API and handle the response
            PostcodeApi.getPostCodeNL(postcode, house_number).done(function (data) {
                self.getPostcodeService().getElement(FieldTypes.postcode).error('');
                
                if (data.results.length === 0) {
                    const errorMessage = getErrorMessage(data);
                    self.setCurrentState(states.POSTCODE_CALL_FAILED);
                    self.getPostcodeService()
                    .getElement(FieldTypes.postcode)
                    .error($t(errorMessage));
                    return;
                }
                
                // Set the field values based on the API response
                self.getPostcodeService().setFieldValue(FieldTypes.street, data.results[0].street);
                self.getPostcodeService().setFieldValue(FieldTypes.city, data.results[0].city);
                self.concatenateFieldsToStreet(FieldTypes.street);
                
                self.setCurrentState(states.POSTCODE_SHOW_FIELDS_SUGGESTION);
            }).fail(function () {
                self.setCurrentState(states.POSTCODE_CALL_FAILED);
            }).always(function () {
                self.getPostcodeService().showHideField(FieldTypes.street, true);
                self.getPostcodeService().showHideField(FieldTypes.city, true);
            });
        }, UI.SEARCH_DELAY_IN_MS); // The delay in milliseconds for the
                                   // _.debounce function from Underscore.js.
        
        // Method to handle changes to the postcode or house number fields
        PostcodeHandlerNL.prototype.handle = function (field_type, field_value) {
            if (this.getCurrentState() !== states.INIT) {
                if (field_type === FieldTypes.postcode) {
                    this.data.postcode = field_value;
                }
                
                if (field_type === FieldTypes.house_number) {
                    this.data.house_number = field_value;
                }
                
                // Validate the input and call the API if valid, state change is handled in callApi
                // due to debouncing
                if ((field_type === FieldTypes.postcode || field_type ===
                        FieldTypes.house_number) &&
                    // Ensure that this.data.postcode & this.data.house_number exists, and they are
                    // strings so that the regex match doesn't fail
                    typeof this.data.postcode === 'string' &&
                    typeof this.data.house_number === 'string' &&
                    this.data.postcode.match(postcodeNLRegex) &&
                    (this.data.house_number.match(/^\d+/)[0]) > 0) {
                    // If 1A is entered in the house_number and default validation is turned off,
                    // strip the characters after the number, check if it is a positive number
                    // and perform the validation nevertheless
                    this.callApi(this.data.postcode, this.data.house_number.match(/^\d+/)[0]);
                }
            }
            
            // Handle changes to the state based on the current state and input
            switch(this.getCurrentState()) {
                case states.INIT:
                    this.setCurrentState(states.IDLE);
                    const postcodeField = this.getPostcodeService().getElement(FieldTypes.postcode);
                    const housenumberField = this.getPostcodeService()
                    .getElement(FieldTypes.house_number);
                    
                    if (postcodeField) {
                        this.handle(FieldTypes.postcode, postcodeField.value());
                    }
                    if (housenumberField) {
                        this.handle(FieldTypes.house_number, housenumberField.value());
                    }
                    break;
                case states.POSTCODE_SHOW_FIELDS_EDIT:
                case states.POSTCODE_SHOW_FIELDS_SUGGESTION:
                    if ([FieldTypes.street, FieldTypes.city].includes(field_type)) {
                        this.setCurrentState(states.POSTCODE_SHOW_FIELDS_EDIT);
                    }
                    break;
            }
            
            // Concatenate all values to Magento street value
            PostcodeHandler.prototype.handle.call(this, field_type, field_value);
        };
        
        return PostcodeHandlerNL;
    }
);
