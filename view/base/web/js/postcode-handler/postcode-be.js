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
        
        // Defining states for the postcode handler
        const states = Object.seal({
            INIT: PostcodeHandler.INIT,
            IDLE: 'postcode_idle',
            POSTCODE_CALL_MADE: 'postcode_call_made',
            POSTCODE_CALL_FAILED: 'postcode_call_failed',
            POSTCODE_SHOW_FIELDS_SUGGESTION: 'postcode_show_fields_suggestion',
            POSTCODE_SHOW_FIELDS_EDIT: 'postcode_show_fields_edit'
        });
        
        // Constructor for PostcodeHandlerBE
        function PostcodeHandlerBE(config, postcodeService)
        {
            this.debounceBeforeCall = null;
            this.data = {};
            PostcodeHandler.call(this, config, postcodeService);
            
            return (this);
        }
        
        // Setting up prototype chain
        PostcodeHandlerBE.prototype = Object.create(PostcodeHandler.prototype);
        
        // Method to get ISO code
        PostcodeHandlerBE.prototype.getISOCode = function () { return 'BE';};
        
        // Method to destroy autocomplete
        PostcodeHandlerBE.prototype.destroy = function () {
            this.deleteAutoComplete();
            PostcodeHandler.prototype.destroy.call(this);
        };
        
        // Method to delete autocomplete
        PostcodeHandlerBE.prototype.deleteAutoComplete = function () {
            var currentPostcodeService = this.getPostcodeService();
            
            var postcodeField = currentPostcodeService.getElement(FieldTypes.postcode);
            var domPostcodeField = $('#' + postcodeField.uid);
            if (domPostcodeField.length > 0 && domPostcodeField.data('uiAutocomplete')) {
                domPostcodeField.autocomplete('destroy');
            }
            
            var streetField = currentPostcodeService.getElement(FieldTypes.street);
            var domStreetField = $('#' + streetField.uid);
            if (domStreetField.length > 0 && domStreetField.data('uiAutocomplete')) {
                domStreetField.autocomplete('destroy');
            }
        };
        
        // Method to add autocomplete to postcode field
        PostcodeHandlerBE.prototype.addAutoCompleteToPostcode = function () {
            var self = this;
            
            var currentPostcodeService = this.getPostcodeService();
            var postcodeField = currentPostcodeService.getElement(FieldTypes.postcode);
            var domPostcodeField = $('#' + postcodeField.uid);
            
            if (domPostcodeField.length === 0) {
                return;
            }
            
            domPostcodeField.attr('autocomplete', 'off'); // When autocomplete is off, the browser
                                                          // does not automatically complete
                                                          // entries based on earlier typed values.
            
            domPostcodeField.autocomplete({
                delay: 30, // Parameter for the results delay in milliseconds
                source: function (zipcodezone, response) {
                    
                    if (zipcodezone.term.length <= 1) {
                        // Only make requests when more than 2 numbers are keyed in:
                        // It reduces the amount of API calls and keeps the result list more
                        // compact
                        this.menu.element.removeClass('tigJqueryUiClass');
                        return;
                    }
                    
                    this.menu.element.addClass(this.customScope + '.tigAutocomplete');
                    this.menu.element.addClass('tigJqueryUiClass');
                    
                    domPostcodeField.addClass('auto-complete-running');
                    
                    response([
                        {
                            label: $.mage.__('Loading zipcodes ...'),
                            data: false
                        }]);
                    
                    PostcodeApi.getPostCodeBE(zipcodezone.term).done(function (data) {
                        
                        if (data.success === false) {
                            // If no results are found, a success === false is returned
                            let errorMessage = 'Zipcode not found.';
                            
                            if (data.error_code) {
                                // show error in console
                                console.error(
                                    'Postcodeservice.com extension: ' + JSON.stringify(data));
                                
                                if (data.error_code > 400) {
                                    errorMessage = 'Could not perform address validation.';
                                }
                                if (data.error_code === 429) {
                                    errorMessage = 'Address validation temporarily unavailable.';
                                }
                            }
                            
                            response(
                                {
                                    label: $.mage.__(errorMessage),
                                    data: false
                                });
                            return;
                        }
                        
                        // Add elements to selection box
                        var selectBoxArr = [];
                        $.each(data, function (key) {
                            if (selectBoxArr.length >= 10) {
                                return false; // Break the loop if more than 10 results are in the
                                              // list
                            }
                            selectBoxArr.push({
                                label: data[key].zipcode + ' - ' + data[key].city,
                                value: data[key].zipcode,
                                data: data[key]
                            });
                        });
                        
                        response(selectBoxArr);
                    });
                },
                select: function (event, ui) {
                    // If the loader / info messages are selected, do not copy them to the street
                    // field
                    if ((ui.item.value === $.mage.__('Loading zipcodes ...')) ||
                        (ui.item.value === $.mage.__('Zipcode not found.'))) {
                        ui.item.value = '';
                        return false;
                    }
                    
                    if (typeof ui.item.data === 'undefined') {
                        return false;
                    }
                    
                    var data = ui.item.data;
                    self.getPostcodeService().setFieldValue(FieldTypes.city, data.city);
                    self.getPostcodeService().setFieldValue(FieldTypes.postcode, data.zipcode);
                    
                },
                close: function () {
                    domPostcodeField.removeClass('auto-complete-running');
                }
            });
        };
        
        // Method to add autocomplete to street field
        PostcodeHandlerBE.prototype.addAutoCompleteToStreet = function () {
            var self = this;
            
            var currentPostcodeService = this.getPostcodeService();
            var streetFieldZero = currentPostcodeService.getElement(FieldTypes.street);
            var streetField = $('#' + streetFieldZero.uid);
            
            if (streetField.length === 0) {
                return;
            }
            
            streetField.attr('autocomplete', 'yes');
            
            streetField.autocomplete({
                delay: 50, // Parameter for the results delay in milliseconds
                source: function (street, response) {
                    var postcode = currentPostcodeService.getElement(FieldTypes.postcode).value();
                    var city = currentPostcodeService.getElement(FieldTypes.city).value();
                    
                    if (city === '' || postcode === '' || postcode === null) {
                        // If the required fields are empty, do not make an API request
                        this.menu.element.removeClass('tigJqueryUiClass');
                        this.menu.element.css('display', 'none');
                        return;
                    }
                    
                    this.menu.element.addClass('tigJqueryUiClass');
                    this.menu.element.appendTo(this.element.closest('.tig_street_autocomplete'));
                    
                    response([
                        {
                            label: $.mage.__('Loading streets ...'),
                            data: false
                        }]);
                    
                    PostcodeApi.getStreetBE(postcode, street.term, city).done(function (data) {
                        if (data.success === false) {
                            // If no results are found, a success === false is returned
                            let errorMessage = 'Cannot find street, is it correct?';
                            
                            if (data.error_code) {
                                // show error in console
                                console.error(
                                    'Postcodeservice.com extension: ' + JSON.stringify(data));
                                
                                if (data.error_code > 400) {
                                    errorMessage = 'Could not perform address validation.';
                                }
                                if (data.error_code === 429) {
                                    errorMessage = 'Address validation temporarily unavailable.';
                                }
                            }
                            
                            response([
                                {
                                    label: $.mage.__(errorMessage),
                                    data: false
                                }]);
                            return;
                        }
                        
                        // Belgium sometimes has a street that lays within two
                        // postkantons, e.g. Rue de l'Aur in Bruxelles/Ixelles/Brussel. Filter
                        // double street names because for this usage we do not the separate
                        // postkantons items.
                        if (Array.isArray(data)) {
                            var uniqueStreets = data.reduce((unique, item) => {
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
                        
                        var selectBoxArr = [];
                        $.each(uniqueStreets, function (index, value) {
                            if (selectBoxArr.length >= 6) {
                                return false; // Break the loop if more than 6 results are in the
                                              // list
                            }
                            selectBoxArr.push({
                                label: value,
                                value: value
                            });
                        });
                        
                        response(selectBoxArr);
                    });
                },
                select: function (event, ui) {
                    // If the loader / info messages are selected by the end user, do not copy them
                    // to the street field
                    if ((ui.item.value === $.mage.__('Loading streets ...')) ||
                        (ui.item.value === $.mage.__('Cannot find street, is it correct?'))) {
                        ui.item.value = '';
                        return false;
                    }
                    
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
        PostcodeHandlerBE.prototype.handle = function (field_type, field_value) {
            if (field_type === FieldTypes.postcode) {
                this.data.postcode = field_value;
            }
            
            switch(this.getCurrentState()) {
                case states.INIT:
                    this.setCurrentState(states.IDLE);
                    this.getPostcodeService()
                    .addClassesToField(FieldTypes.street, {'tig_street_autocomplete': true});
                    this.getPostcodeService()
                    .addClassesToField(FieldTypes.postcode, {'tig_zipcodezone_autocomplete': true});
                    this.addAutoCompleteToPostcode(
                        this.getPostcodeService().getElement(FieldTypes.postcode));
                    this.addAutoCompleteToStreet(
                        this.getPostcodeService().getElement(FieldTypes.street));
                    break;
            }
            PostcodeHandler.prototype.handle.call(this, field_type, field_value);
            return true;
        };
        return PostcodeHandlerBE;
    }
);
