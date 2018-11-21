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

            /**
             *  if switchToBe is true, disable the street field when zipcode is empty
             *  otherwise switch back to defaults of Magento.
             **/
            switchToBe : function (switchToBe) {
                var self = this;
                if (switchToBe) {
                    self.disableStreetField();

                    return;
                }
                self.enableStreetField();
            },

            /** Disable the street field (but only if zipcode is empty). **/
            disableStreetField : function () {
                var self = this;

                var fields = [
                    self.parentName + '.postcode',
                    self.parentName + '.street.0'
                ];
                if (self.isNLPostcodeCheckOn()) {
                    fields = [
                        self.parentName + '.postcode-field-group.field-group.postcode',
                        self.parentName + '.street.0'
                    ];
                }
                var placeholder = $.mage.__('Please select a postcode before filling the street field.');
                Registry.get(fields, function (postcodeElement, streetElement) {
                    if (!postcodeElement.value()) {
                        // This is for setting the init placeholder
                        streetElement.placeholder = placeholder;
                        streetElement.disable();
                    }
                    $('.tig_street_autocomplete .input-text').attr('placeholder', placeholder);
                });
            },

            /** Back to the Magento default. **/
            enableStreetField : function () {
                var self = this;
                Registry.get(self.parentName + '.street.0', function (streetElement) {
                    streetElement.enable();
                });
                $('.tig_street_autocomplete .input-text').attr('placeholder', '');
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
            },

            /**
             * set the auto complete for the zipcode field.
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
                                },
                                menu : this.menu
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
                                this.menu.element.addClass(self.customScope + ".tigAutocomplete");
                            }).fail(function (data) {
                                console.log(data);
                            });
                        },
                        select : function (event, ui) {
                            var fields = [
                                self.parentName + '.city',
                                self.parentName + '.street.0'
                            ];
                            if (self.customScope === 'shippingAddress' && self.isNLPostcodeCheckOn()) {
                                fields = [
                                    self.containers[0].containers[0].parentName + '.city',
                                    self.containers[0].containers[0].parentName + '.street.0'
                                ];
                            }

                            Registry.get(fields, function (
                                cityElement,
                                streetElement
                            ) {
                                cityElement.set('value', ui.item.value.substring(7, ui.item.value.length));
                                streetElement.enable();
                            });
                            ui.item.value = ui.item.value.substring(0, 4);
                            $("input[name*='postcode']").trigger('change');
                            $("input[name*='city']").trigger('change');
                        },
                        close : function (event) {
                            var menuElement = $('.' + self.customScope + '\\.tigAutocomplete')
                            if (event.originalEvent.type !== 'menuselect' && !menuElement.is(":visible")) {
                                menuElement.show();

                                return false;
                            }
                        }
                    });
                });
            },

            /**
             * set the auto complete for the street field after zipcodezone is filled.
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

                return currentFormData && currentFormData.country_id === 'BE';
            },
        });
    };
});
