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
        'jquery',
        'knockout',
        'underscore',
        './postcode-handler',
        '../helper/field-types',
        '../helper/postcode-api',
        'jquery-ui-modules/autocomplete',
        'mage/translate'
    ],
    function ($, ko, _, PostcodeHandler, FieldTypes, PostcodeApi) {
        'use strict';
        
        // Define display messages for various states
        const displayMessages = {
            LOADING_ZIPCODES: 'Loading zipcodes ...',
            ZIPCODE_NOT_FOUND: 'Zipcode not found.',
            LOADING_STREET: 'Loading streets ...',
            STREET_NOT_FOUND: 'Cannot find street, is it correct?'
        };
        
        // Define error messages for various error states
        const errorMessages = {
            VALIDATION_UNAVAILABLE: 'Address validation temporarily unavailable.',
            VALIDATION_FAILED: 'Could not perform address validation.'
        };
        
        // Define UI constants
        const UI = {
            DROPDOWN_DELAY_IN_MS: 30,
            MIN_AUTOCOMPLETE_LENGTH: 2,
            MAX_DROPDOWN_RESULTS: 7
        };
        
        // Define states for the postcode handler
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
            COUNTRY: 'BE'
        };
        
        // Function to get error message based on data and default error message
        function getErrorMessage(data, defaultErrorMessage)
        {
            if (data.error_code) {
                console.error('Postcodeservice.com extension: ' + JSON.stringify(data));
                switch(data.error_code) {
                    case 429:
                        return errorMessages.VALIDATION_UNAVAILABLE;
                    default:
                        return data.error_code > 400
                            ? errorMessages.VALIDATION_FAILED
                            : defaultErrorMessage;
                }
            }
            return defaultErrorMessage;
        }
        
        // Constructor for PostcodeHandlerBE
        function PostcodeHandlerBE(config, postcodeService)
        {
            this.debounceBeforeCall = null;
            this.data = {};
            PostcodeHandler.call(this, config, postcodeService);
            
            return (this);
        }
        
        // Inherit from PostcodeHandler
        PostcodeHandlerBE.prototype = Object.create(PostcodeHandler.prototype);
        
        // Method to get ISO code
        PostcodeHandlerBE.prototype.getISOCode = function () { return appConfig.COUNTRY; };
        
        // Method to destroy the handler
        PostcodeHandlerBE.prototype.destroy = function () {
            this.deleteAutoComplete();
            PostcodeHandler.prototype.destroy.call(this);
        };
        
        // Method to delete autocomplete from fields
        PostcodeHandlerBE.prototype.deleteAutoComplete = function () {
            const currentPostcodeService = this.getPostcodeService();
            const postcodeField = currentPostcodeService.getElement(FieldTypes.postcode);
            const domPostcodeField = $('#' + postcodeField.uid);
            
            if (domPostcodeField.length > 0 && domPostcodeField.data('uiAutocomplete')) {
                domPostcodeField.autocomplete('destroy');
            }
            
            const streetField = currentPostcodeService.getElement(FieldTypes.street);
            const domStreetField = $('#' + streetField.uid);
            
            if (domStreetField.length > 0 && domStreetField.data('uiAutocomplete')) {
                domStreetField.autocomplete('destroy');
            }
        };
        
        // Method to add autocomplete to postcode field
        PostcodeHandlerBE.prototype.addAutoCompleteToPostcode = async function () {
            const self = this;
            const currentPostcodeService = this.getPostcodeService();
            const postcodeField = currentPostcodeService.getElement(FieldTypes.postcode);
            const domPostcodeField = $('#' + postcodeField.uid);
            
            // If the DOM element for the postcode field does not exist, exit the function
            if (domPostcodeField.length === 0) {
                return;
            }
            
            // Turn off browser's autocomplete feature for the postcode field
            domPostcodeField.attr('autocomplete', 'off');
            
            // Add jQuery UI Autocomplete to the postcode field
            domPostcodeField.autocomplete({
                delay: UI.DROPDOWN_DELAY_IN_MS, // Delay in milliseconds after a keystroke is
                                                // activated
                minLength: UI.MIN_AUTOCOMPLETE_LENGTH, // Minimum number of characters required to
                                                       // start an autocomplete search
                source: function (zipcodezone, response) {
                    // Add classes to the menu element
                    this.menu.element.addClass(this.customScope + '.tigAutocomplete');
                    this.menu.element.addClass('tigJqueryUiClass');
                    
                    // Add class to indicate that autocomplete is running
                    domPostcodeField.addClass('auto-complete-running');
                    
                    // Display loading message
                    response([
                        {
                            label: $.mage.__(displayMessages.LOADING_ZIPCODES),
                            data: false
                        }]);
                    
                    // Call the API to get postcode data
                    PostcodeApi.getPostCodeBE(zipcodezone.term).done(function (data) {
                        
                        // If no results are returned, display an error message
                        if (data.results.length === 0) {
                            const errorMessage = getErrorMessage(
                                data, displayMessages.ZIPCODE_NOT_FOUND);
                            
                            response(
                                {
                                    label: $.mage.__(errorMessage),
                                    data: false
                                });
                            return;
                        }
                        
                        // Prepare data for the selection box
                        const selectBoxArr = [];
                        $.each(data.results, function (key) {
                            if (selectBoxArr.length >= UI.MAX_DROPDOWN_RESULTS) {
                                return false; // Break the loop if more than x results are in the
                                              // list
                            }
                            selectBoxArr.push({
                                label: data.results[key].zipcode + ' - ' + data.results[key].city,
                                value: data.results[key].zipcode,
                                data: data.results[key]
                            });
                        });
                        
                        // Update the autocomplete suggestions
                        response(selectBoxArr);
                    });
                },
                select: function (event, ui) {
                    // If a loading or error message is selected, do not update the field value
                    if ((ui.item.value === $.mage.__(displayMessages.LOADING_ZIPCODES)) ||
                        (ui.item.value === $.mage.__(displayMessages.ZIPCODE_NOT_FOUND))) {
                        ui.item.value = '';
                        return false;
                    }
                    
                    if (typeof ui.item.data === 'undefined') {
                        return false;
                    }
                    
                    // Update the city and postcode fields with the selected data
                    let data = ui.item.data;
                    self.getPostcodeService().setFieldValue(FieldTypes.city, data.city);
                    self.getPostcodeService().setFieldValue(FieldTypes.postcode, data.zipcode);
                    
                },
                close: function () {
                    // Remove the class indicating that autocomplete is running
                    domPostcodeField.removeClass('auto-complete-running');
                }
            });
        };
        
        // Method to add autocomplete to street field
        PostcodeHandlerBE.prototype.addAutoCompleteToStreet = async function () {
            const self = this;
            const currentPostcodeService = this.getPostcodeService();
            const streetFieldZero = currentPostcodeService.getElement(FieldTypes.street);
            const streetField = $('#' + streetFieldZero.uid);
            
            // If the DOM element for the street field does not exist, exit the function
            if (streetField.length === 0) {
                return;
            }
            
            // Turn on browser's autocomplete feature for the street field
            streetField.attr('autocomplete', 'yes');
            
            // Add jQuery UI Autocomplete to the street field
            streetField.autocomplete({
                delay: UI.DROPDOWN_DELAY_IN_MS, // Delay in milliseconds after a keystroke is
                                                // activated
                minLength: UI.MIN_AUTOCOMPLETE_LENGTH, // Minimum number of characters required to
                // start an autocomplete search
                source: function (street, response) {
                    const postcode = currentPostcodeService.getElement(FieldTypes.postcode).value();
                    const city = currentPostcodeService.getElement(FieldTypes.city).value();
                    
                    // If the required fields are empty, do not make an API request
                    if (!city?.trim() || !postcode?.trim()) {
                        // If the required fields are empty, do not make an API request
                        this.menu.element.removeClass('tigJqueryUiClass').css('display', 'none');
                        return;
                    }
                    
                    // Add class to the menu element
                    this.menu.element.addClass('tigJqueryUiClass');
                    this.menu.element.appendTo(this.element.closest('.tig_street_autocomplete'));
                    
                    // Display loading message
                    response([
                        {
                            label: $.mage.__(displayMessages.LOADING_STREET),
                            data: false
                        }]);
                    
                    // Call the API to get street data
                    PostcodeApi.getStreetBE(postcode, street.term, city).done(function (data) {
                        if (data.results.length === 0) {
                            const errorMessage = getErrorMessage(
                                data, displayMessages.STREET_NOT_FOUND);
                            
                            response(
                                {
                                    label: $.mage.__(errorMessage),
                                    data: false
                                });
                            return;
                        }
                        
                        // Prepare unique list of streets for the selection box
                        if (Array.isArray(data.results)) {
                            var uniqueStreets = data.results.reduce((unique, item) => {
                                return unique.includes(item.street) ? unique : [
                                    ...unique,
                                    item.street];
                            }, []);
                        } else {
                            // If data is not an array, it means no data was found. Possibly the
                            // city field was not filled in hence the validation could not occur.
                            response([
                                {
                                    data: false
                                }]);
                        }
                        
                        const selectBoxArr = [];
                        $.each(uniqueStreets, function (index, value) {
                            if (selectBoxArr.length >= UI.MAX_DROPDOWN_RESULTS) {
                                return false; // Stop adding to the list after reaching the maximum
                                              // number of results
                            }
                            selectBoxArr.push({
                                label: value,
                                value: value
                            });
                        });
                        
                        // Update the autocomplete suggestions
                        response(selectBoxArr);
                    });
                },
                select: function (event, ui) {
                    // If a loading or error message is selected, do not update the field value
                    if ((ui.item.value === $.mage.__(displayMessages.LOADING_STREET)) ||
                        (ui.item.value === $.mage.__(displayMessages.STREET_NOT_FOUND))) {
                        ui.item.value = '';
                        return false;
                    }
                    
                    // Update the street field with the selected data
                    if (typeof ui.item.data === false) {
                        return false;
                    }
                    
                    // User selected street, update the street fieldValue
                    var data = ui.item.value;
                    self.getPostcodeService().setFieldValue(FieldTypes.street, data);
                }
            });
        };
        
        // Method to handle field type and value
        PostcodeHandlerBE.prototype.handle = async function (field_type, field_value) {
            if (field_type === FieldTypes.postcode) {
                this.data.postcode = field_value;
            }
            
            switch(this.getCurrentState()) {
                case states.INIT:
                    // If the current state is INIT, change it to IDLE and add autocomplete to the
                    // fields
                    this.setCurrentState(states.IDLE);
                    this.getPostcodeService()
                    .addClassesToField(FieldTypes.street, {'tig_street_autocomplete': true});
                    this.getPostcodeService()
                    .addClassesToField(FieldTypes.postcode, {'tig_zipcodezone_autocomplete': true});
                    await this.addAutoCompleteToPostcode(
                        this.getPostcodeService().getElement(FieldTypes.postcode));
                    await this.addAutoCompleteToStreet(
                        this.getPostcodeService().getElement(FieldTypes.street));
                    break;
            }
            
            // Call the handle method of the parent class
            PostcodeHandler.prototype.handle.call(this, field_type, field_value);
            return true;
        };
        
        // Return the constructor function
        return PostcodeHandlerBE;
    }
);
