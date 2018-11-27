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
                    observeCountry : '${ $.parentName }.country_id:value',
                    observePostcode    : '${ $.parentName }.postcode-field-group.field-group.postcode:value',
                    observeStreet    : '${ $.parentName }.street.0:value'
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
             * When going through the autocompleteZipcodezone function, var self gets defined. When going back and forth
             * through shipping/billing steps the 'this' scope will change, but stays the same within
             * autocompleteZipcodezone. This observer makes the customScope/Parentname for the current scope available.
             **/
            observePostcode : function () {
                window.customSelf = this;
            },

            observeStreet : function () {
                window.customSelf = this;
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
                        $('.tig_street_autocomplete .input-text').attr('placeholder', placeholder);
                    }
                    $('.tig_zipcodezone_autocomplete').addClass('tig-postcode-be');
                    $("div[name*='tig_housenumber']").addClass('tig-housenumber-be');
                    $("div[name*='tig_housenumber_addition']").addClass('tig-housenumber-addition-be');
                });

                $('.tig_zipcodezone_autocomplete .input-text').attr('autocomplete', 'no');
                $('.tig_street_autocomplete .input-text').attr('autocomplete', 'no');
            },

            /** Back to the Magento default. **/
            enableStreetField : function () {
                var self = this;
                Registry.get(self.parentName + '.street.0', function (streetElement) {
                    streetElement.placeholder = '';
                    streetElement.enable();
                    $('.tig_zipcodezone_autocomplete').removeClass('tig-postcode-be');
                    $("div[name*='tig_housenumber']").removeClass('tig-housenumber-be');
                    $("div[name*='tig_housenumber_addition']").removeClass('tig-housenumber-addition-be');
                });
            },

            addAutocomplete : function () {
                var self = this;

                var tigClass = "." + Object.keys(this.additionalClasses)[0];
                if (tigClass === '.tig_zipcodezone_autocomplete') {
                    self.autocompleteZipcodezone();
                }

                if (tigClass === '.tig_street_autocomplete') {
                    self.autocompleteStreet();
                }

                self.switchToBe(self.isCountryBe());
            },

            /**
             * set the auto complete for the zipcode field.
             * @param tigClass
             */
            autocompleteZipcodezone : function (tigClass) {
                var self = this;
                $(".tig_zipcodezone_autocomplete .input-text").each(function () {
                    $(this).parent().append('<span class="tig-autocomplete-result-city"></span>');
                    $(this).autocomplete({
                        source : function (zipcodezone, response) {
                            this.menu.element.addClass(self.customScope + ".tigAutocomplete");
                            if (!self.isCountryBe()) {
                                /**
                                 * Somehow the loader occasionally pops up on different countries.
                                 * Here we force remove the loader.
                                 */
                                this.element.removeClass('ui-autocomplete-loading');
                                response([]);
                                return;
                            }
                            response([$.mage.__('Busy with loading zipcodes...')]);
                            $.ajax({
                                method         : 'GET',
                                url            : window.checkoutConfig.postcode.action_url.postcode_be_getpostcode,
                                data           : {
                                    zipcodezone : zipcodezone.term
                                },
                                zipcodeElement : this
                            }).done(function (data) {
                                /**
                                 * This part will refresh the data inside the array
                                 */
                                this.zipcodeElement.element.removeClass('ui-autocomplete-loading');

                                if (data.success == false) {
                                    response([$.mage.__('No results found.')]);
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
                            /** Prevent weird values being inserted into the postcode / city fields **/
                            if (ui.item.value == $.mage.__('Busy with loading zipcodes...') ||
                                ui.item.value == $.mage.__('No results found.')) {
                                ui.item.value = '';
                                return false;
                            }
                            var fields = [
                                window.customSelf.parentName + '.city',
                                window.customSelf.parentName + '.street.0'
                            ];

                            Registry.get(fields, function (cityElement,
                                                           streetElement) {
                                cityElement.set('value', ui.item.value.substring(7, ui.item.value.length));
                                $('.tig_street_autocomplete .input-text').attr('placeholder', '');
                                streetElement.placeholder = '';
                                streetElement.enable();
                            });
                            event.target.parentElement.getElementsByClassName('tig-autocomplete-result-city')[0]
                                .textContent = ui.item.value.substring(4, ui.item.value.length);
                            ui.item.value = ui.item.value.substring(0, 4);
                        },
                        close : function (event) {
                            var menuElement = $('.' + window.customSelf.customScope + '\\.tigAutocomplete');
                            if (event.originalEvent !== undefined &&
                                event.originalEvent.type !== 'menuselect' &&
                                !menuElement.is(":visible")
                            ) {
                                menuElement.show();

                                return false;
                            }
                            $("input[name*='postcode']").trigger('change');
                            $("input[name*='city']").trigger('change');
                        },
                        delay : 0
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

                $(".tig_street_autocomplete .input-text").each(function () {
                    $(this).autocomplete({
                        source : function (request, response) {
                            if (!self.isCountryBe()) {
                                /**
                                 * Somehow the loader occasionally pops up on different countries.
                                 * Here we force remove the loader.
                                  */
                                this.element.removeClass('ui-autocomplete-loading');
                                response([]);
                                return;
                            }
                            response([$.mage.__('Busy with loading streets...')]);
                            Registry.get([
                                window.customSelf.parentName + '.postcode',
                                window.customSelf.parentName + '.city',
                                window.customSelf.parentName + '.street.0'
                            ], function (postcodeElement, cityElement, streetElement) {
                                postcode = postcodeElement.value();
                                city = cityElement.value();
                                street = streetElement.value();
                            });

                            $.ajax({
                                method : 'GET',
                                url    : window.checkoutConfig.postcode.action_url.postcode_be_getstreet,
                                data   : {
                                    zipcode : postcode,
                                    city    : city,
                                    street  : street
                                },
                                streetElement : this
                            }).done(function (data) {
                                /**
                                 * This part will refresh the data inside the array
                                 */
                                this.streetElement.element.removeClass('ui-autocomplete-loading');

                                if (data.success == false) {
                                    response([$.mage.__('No results found. Please fill in manually.')]);
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
                        select : function (event, ui) {
                            /** Prevent weird values being inserted into the postcode / city fields **/
                            if (ui.item.value == $.mage.__('Busy with loading zipcodes...') ||
                                ui.item.value == $.mage.__('No results found. Please fill in manually.')) {
                                ui.item.value = '';
                                return false;
                            }

                            $("input[name*='street']").trigger('change');
                        },
                        delay : 0
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
