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
                isBePostcodeCheckOn : ko.observable(DataProvider.isPostcodeBeOn())
            },

            observeCountry : function (value) {
                if (!this.isBePostcodeCheckOn()) {
                    return;
                }

                window.customSelf = this;

                if (value) {
                    window.customSelf.switchToBe(value === 'BE');
                }
            },

            /**
             * When going through the autocompleteZipcodezone function, var self gets defined. When going back and forth
             * through shipping/billing steps the 'this' scope will change, but stays the same within
             * autocompleteZipcodezone. This observer makes the customScope/Parentname for the current scope available.
             **/
            observePostcode : function (value) {
                if (!this.isBePostcodeCheckOn()) {
                    return;
                }

                window.customSelf = this;

                if (!value) {
                    /** Empty out the zipcode field when value is removed. **/
                    var menuElement = $('.' + window.customSelf.customScope + '\\.tigAutocomplete');
                    menuElement.hide();
                }

                if (value !== window.currentZipcode) {
                    var zipcodeElement = $('div[name="' + window.customSelf.customScope + '.postcode"]');
                    zipcodeElement.find('.tig-autocomplete-result-city').text('');
                }
                window.currentZipcode = value;
            },

            observeStreet : function () {
                if (!this.isBePostcodeCheckOn()) {
                    return;
                }

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
                    self.parentName + '.postcode-field-group.field-group.postcode',
                    self.parentName + '.street.0'
                ];

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

                // Force auto complete off
                $('.tig_zipcodezone_autocomplete .input-text').attr('autocomplete', 'no');
                $('.tig_street_autocomplete .input-text').attr('autocomplete', 'no');
            },

            /**
             * set the auto complete for the zipcode field.
             */
            autocompleteZipcodezone : function () {
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
                            /**
                             * Prevent tabbing while zipcode is still loading.
                             */
                            this.element.on('keydown', function (objEvent) {
                                if (objEvent.keyCode == 9) {
                                    objEvent.preventDefault();
                                }
                            });
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

                                // Force remove the loader & re-enable tabbing out of the field.
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
                                setTimeout(function (zipcodeElement) {
                                    zipcodeElement.element.off('keydown');
                                }, 250, this.zipcodeElement);
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

                            Registry.get(fields, function (
                                cityElement,
                                streetElement
                            ) {
                                cityElement.set('value', ui.item.value.substring(7, ui.item.value.length));
                                $('.tig_street_autocomplete .input-text').attr('placeholder', '');
                                streetElement.placeholder = '';
                                streetElement.enable();
                            });
                            event.target.parentElement.getElementsByClassName('tig-autocomplete-result-city')[0]
                                .textContent = ui.item.value.substring(4, ui.item.value.length);
                            ui.item.value = ui.item.value.substring(0, 4);
                            window.currentZipcode = ui.item.value;
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
             */
            autocompleteStreet : function () {
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
                                window.customSelf.parentName + '.postcode-field-group.field-group.postcode',
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
                            if (ui.item.value == $.mage.__('Busy with loading streets...') ||
                                ui.item.value == $.mage.__('No results found. Please fill in manually.')) {
                                ui.item.value = '';
                                return false;
                            }
                        },
                        close : function (event, ui) {
                            $("input[name*='street']").trigger('change');
                        }
                    });
                });
            },

            isCountryBe : function () {
                var currentFormData = this.source.get(window.customSelf.customScope);

                return currentFormData && currentFormData.country_id === 'BE';
            },
        });
    };
});
