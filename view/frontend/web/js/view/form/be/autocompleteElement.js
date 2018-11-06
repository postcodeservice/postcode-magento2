/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiRegistry',
    'ko',
    'TIG_Postcode/js/Helper/DataProvider'
], function (
    $,
    Registry,
    ko,
    DataProvider
) {
    'use strict';

    return function (originalElement) {
        return originalElement.extend({
            defaults : {
                imports : {
                    observeCountry : '${ $.parentName }.country_id:value'
                },
                isNLPostcodeCheckOn : ko.observable(DataProvider.isPostcodeNLOn())
            },

            observeCountry : function (value) {
                var self = this;

                if (value) {
                    self.switchToBe(value === 'BE');
                }
            },

            /** if switchToBe is true, hide the city and postcode fields and show the zipcodezone field
             *  otherwise switch back to defaults of Magento
             **/
            switchToBe : function (switchToBe) {
                var self = this;
                var fields = [
                    self.parentName + '.zipcodezone',
                    self.parentName + '.city',
                    self.parentName + '.street.0'
                ];

                if (self.isNLPostcodeCheckOn()) {
                    fields.push(self.parentName + '.postcode-field-group.field-group.postcode');
                } else {
                    fields.push(self.parentName + '.postcode');
                }

                if (switchToBe) {
                    self.hideAddressFields(fields);

                    return;
                }
                self.showAddressFields(fields);
            },

            /** Hide the postcode and city field, show the zipcodezone field and disable the street field **/
            hideAddressFields : function (fields) {
                /** city field conflicts with the NL service, so we temp remove the tig_hidden class **/
                $('.tig_hidden').show();
                $('.tig_be_hidden').removeClass('tig_hidden');

                Registry.get(fields, function (
                    zipcodezoneElement,
                    cityElement,
                    streetElement,
                    postcodeElement
                ) {
                    zipcodezoneElement.show();
                    streetElement.show();
                    cityElement.hide();
                    postcodeElement.validation.required = false;
                    postcodeElement.hide();
                });

                if (!$('.tig_zipcodezone_autocomplete .input-text').val()) {
                    $('.tig_street_autocomplete .input-text').attr('disabled', true);
                }
            },

            /** Back to the Magento default **/
            showAddressFields : function (fields) {
                $('.tig_be_hidden').addClass('tig_hidden');
                Registry.get(fields, function (
                    zipcodezoneElement,
                    cityElement,
                    streetElement,
                    postcodeElement
                ) {
                    zipcodezoneElement.hide();
                    cityElement.show();
                    postcodeElement.validation.required = true;
                    postcodeElement.show();
                });
                $('.tig_street_autocomplete .input-text').attr('disabled', false);
            },

            addAutocomplete : function () {
                var self = this;

                var tigClass = "." + Object.keys(this.additionalClasses)[0];
                if (tigClass === '.tig_zipcodezone_autocomplete') {
                    self.autocompleteZipcodezone(tigClass);
                }

                if (tigClass === '.tig_street_autocomplete') {
                    self.autocompleteStreet(tigClass);
                }
                if (!self.isCountryNl()) {
                    self.switchToBe(this.source.get(this.customScope).country_id === 'BE');
                }
                if (self.isCountryBe()) {
                    self.initDisableStreetField();
                }
            },

            initDisableStreetField : function () {
                var self = this;
                if (self.containers.length > 0) {
                    self.disableStreetField();

                    self.switchToBe(self.isCountryBe());
                }
            },

            /**
             * We only reach this at initialisation because
             * that's the only moment when this.parentName equals street field
             **/
            disableStreetField : function () {
                var self = this;
                var fields = [self.containers[0].parentName + '.zipcodezone', self.parentName + '.0'];

                Registry.get(fields, function (zipcodezoneElement, streetElement) {
                    if (!zipcodezoneElement.value()) {
                        streetElement.disable();
                    }
                });
            },

            /**
             * set the auto complete for the zipcodezone field
             * @param tigClass
             */
            autocompleteZipcodezone : function (tigClass) {
                var self = this;
                $(tigClass + " .input-text").each(function () {
                    $(this).autocomplete({
                        source : function (zipcodezone, response) {
                            if (!self.isCountryBe()) {
                                return;
                            }
                            $.ajax({
                                method : 'GET',
                                url    : window.checkoutConfig.postcode.action_url.postcode_be_getpostcode,
                                data   : {
                                    zipcodezone : zipcodezone.term
                                }
                            }).done(function (data) {
                                /**
                                 * This part will refresh the data inside the array
                                 */
                                if (data.success == false) {
                                    console.log('Zero results found');
                                    return;
                                }
                                var selectBoxArr = [];
                                $.each(data, function (key, value) {
                                    selectBoxArr.push(data[key].postcode + ' - ' + data[key].plaats);
                                });

                                response(selectBoxArr);
                            }).fail(function (data) {
                                console.log(data);
                            });
                        },
                        select : function (event, ui) {
                            Registry.get([
                                self.parentName + '.postcode',
                                self.parentName + '.city'
                            ], function (postcodeElement, cityElement) {
                                postcodeElement.set('value', ui.item.value.substring(0, 4));
                                cityElement.set('value', ui.item.value.substring(7, ui.item.value.length));
                                $('.tig_street_autocomplete .input-text').attr('disabled', false);
                                $("input[name*='postcode']").trigger('change');
                                $("input[name*='city']").trigger('change');
                            });
                        }
                    });
                });
            },

            /**
             * set the auto complete for the street field after zipcodezone is filled
             * @param tigClass
             */
            autocompleteStreet : function (tigClass) {
                var self = this;

                var postcode = null;
                var city = null;
                var street = null;

                $(tigClass + " .input-text").each(function () {
                    $(this).autocomplete({
                        source : function (request, response) {
                            if (!self.isCountryBe()) {
                                return;
                            }
                            Registry.get([
                                self.containers[0].parentName + '.postcode',
                                self.containers[0].parentName + '.city',
                                self.parentName + '.0'
                            ], function (postcodeElement, cityElement, streetElement) {
                                postcode = postcodeElement.value;
                                city = cityElement.value;
                                street = streetElement.value;
                            });

                            $.ajax({
                                method : 'GET',
                                url    : window.checkoutConfig.postcode.action_url.postcode_be_getstreet,
                                data   : {
                                    zipcode : postcode,
                                    city    : city,
                                    street  : street
                                }
                            }).done(function (data) {
                                /**
                                 * This part will refresh the data inside the array
                                 */
                                if (data.success == false) {
                                    console.log('Zero results found');
                                    return;
                                }
                                var selectBoxArr = [];
                                $.each(data, function (key, value) {
                                    selectBoxArr.push(value.straat);
                                });

                                response(selectBoxArr);
                            }).fail(function (data) {
                                console.log(data);
                            });
                        },
                        select : function () {
                            $("input[name*='street']").trigger('change');
                        }
                    });
                });
            },

            isCountryBe : function () {
                var currentFormData = this.source.get(this.dataScope.split('.')[0]);

                if (currentFormData && currentFormData.country_id === 'BE') {
                    return true;
                }

                return false;
            },

            isCountryNl : function () {
                var currentFormData = this.source.get(this.dataScope.split('.')[0]);

                if (currentFormData && currentFormData.country_id === 'NL') {
                    return true;
                }

                return false;
            }
        });
    };
});
