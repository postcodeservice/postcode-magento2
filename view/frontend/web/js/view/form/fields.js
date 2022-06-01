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
 * to support@postcodeservice.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.com for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Ui/js/form/components/group',
    'ko',
    'TIG_Postcode/js/Helper/Logger',
    'TIG_Postcode/js/Address/State',
    'uiRegistry',
    'underscore'
], function (
    $,
    quote,
    AddressConverter,
    uiComponent,
    ko,
    Logger,
    State,
    Registry,
    _
) {
    'use strict';

    return uiComponent.extend({
        defaults : {
            template  : 'TIG_Postcode/checkout/field-group',
            isLoading : false,
            message   : ko.observable(null),
            imports   : {
                observePostcode    : '${ $.parentName }.postcode-field-group.field-group.postcode:value',
                observeHousenumber : '${ $.parentName }.postcode-field-group.field-group.housenumber:value',
                observeAddition    : '${ $.parentName }.postcode-field-group.field-group.housenumber_addition:value',
                observeCountry     : '${ $.parentName }.country_id:value'
            },
            sameCall : false,
            timer    : undefined,
            beAutocomplete : false
        },

        addToAddress: function (address, customAttributes) {
            // Subtract one, as the index is 0
            var housenumberIndex = window.checkoutConfig.postcode.streetparser.housenumberParsing - 1;
            var housenumberAdditionIndex = window.checkoutConfig.postcode.streetparser.housenumberAdditionParsing - 1;

            _.each(address[customAttributes], function (attr) {
                if (_.isUndefined(attr.attribute_code || attr.value === '')) {
                    return;
                }

                var value;
                // Simply concating provides the housenumber "undefined 37", because street[1] can be undefined.
                if (attr.attribute_code === 'tig_housenumber') {
                    value = [address.street[housenumberIndex], attr.value].join(' ');
                    address.street[housenumberIndex] = value.trim();
                }

                if (attr.attribute_code === 'tig_housenumber_addition') {
                    value = [address.street[housenumberAdditionIndex], attr.value].join(' ');
                    address.street[housenumberAdditionIndex] = value.trim();
                }
            }.bind(this));

            return address;
        },

        updateAddresses: function (options) {
            if (typeof options.data === "string") {
                var data = $.parseJSON(options.data);

                if (data != null) {
                    // Handle Magento inconsistencies
                    var customAttributes = 'custom_attributes';

                    if (!_.isUndefined(data.address) && !_.isUndefined(data.address[customAttributes])) {
                        data.address = this.addToAddress(data.address, customAttributes);
                    }

                    customAttributes = 'customAttributes';

                    if (!_.isUndefined(data.addressInformation) && data.addressInformation && !_.isUndefined(data.addressInformation.shipping_address) && !_.isUndefined(data.addressInformation.shipping_address[customAttributes])) {
                        data.addressInformation.shipping_address = this.addToAddress(data.addressInformation.shipping_address, customAttributes);
                    }

                    if (!_.isUndefined(data.addressInformation) && data.addressInformation && !_.isUndefined(data.addressInformation.billing_address) && !_.isUndefined(data.addressInformation.billing_address[customAttributes])) {
                        data.addressInformation.billing_address = this.addToAddress(data.addressInformation.billing_address, customAttributes);
                    }

                    if (!_.isUndefined(data.billingAddress) && data.billingAddress && !_.isUndefined(data.billingAddress[customAttributes])) {
                        data.billingAddress = this.addToAddress(data.billingAddress, customAttributes);
                    }

                    return JSON.stringify(data);
                }
            }
        },

        initialize : function () {
            this._super()
                ._setClasses();

            // Only allow this prefilter once, to prevent updating addresses multiple times
            if (window.checkoutConfig.postcode.is_initialized === undefined) {
                // Credits to OneStepCheckout.com and @speedupmate (https://gist.github.com/speedupmate) for this logic and solution
                $.ajaxPrefilter(
                    function ( options, originalOptions, jqXHR ) {
                        var allowedMethods = ["POST","DELETE","PUT"];
                        var allowedUrls = _.filter(['checkout/onepage/update', 'rest/'], function (url) {
                            return options.url.indexOf(url) !== -1;
                        });

                        if ($.inArray(options.type.toUpperCase(), allowedMethods) === -1 ||
                            allowedUrls.length < 1) {
                            return false;
                        }

                        options.data = this.updateAddresses(options);
                    }.bind(this)
                );
                window.checkoutConfig.postcode.is_initialized = true;
            }

            // PSM2-116 - If customAttributes exist, the address already contains a tig_housenumber.
            // Sometimes extension attributes get lost, fill them every time the address changes.
            quote.shippingAddress.subscribe(function (address) {
                if (!address) {
                    return;
                }
                
                if (address.extension_attributes === undefined) {
                    address.extension_attributes = {};
                }

                if (address.customAttributes !== undefined && address.customAttributes[0] !== undefined && address.customAttributes[0].attribute_code === 'tig_housenumber') {
                    address.extension_attributes.tig_housenumber          = address.customAttributes[0].value;
                    address.extension_attributes.tig_housenumber_addition = address.customAttributes[1].value;
                }
            });

            quote.billingAddress.subscribe(function (address) {
                if (!address) {
                    return;
                }
                
                if (address.extension_attributes === undefined) {
                    address.extension_attributes = {};
                }

                if (address.customAttributes !== undefined && address.customAttributes[0] !== undefined && address.customAttributes[0].attribute_code === 'tig_housenumber') {
                    address.extension_attributes.tig_housenumber          = address.customAttributes[0].value;
                    address.extension_attributes.tig_housenumber_addition = address.customAttributes[1].value;
                }
            });

            var self = this;
            Registry.async(this.provider)(function () {
                self.initModules();
                self.updateFieldData();
            });

            /** If zipcodezone is available, we can assume the be check is on **/
            Registry.get(self.parentName + '.zipcodezone', function () {
                self.beAutocomplete = true;
            });

            return this;
        },

        initObservable : function () {
            this._super().observe(['isLoading']);

            if (!window.checkoutConfig.postcode.postcode_active) {
                return this;
            }

            return this;
        },

        observeHousenumber : function (value) {
            if (value) {
                this.updateFieldData();
            }
        },

        observePostcode : function (value) {
            if (value) {
                this.updateFieldData();
            }
        },

        observeCountry : function (value) {
            var message = $('.tig-postcode-validation-message');

            this.toggleAddressFields(value);

            if (value !== 'NL') {
                message.hide();
                return;
            }

            if (value && value === 'NL') {
                this.updateFieldData();
            }
        },

        /**
         * Hide or show every address line that's not the first address line when NL or BE is
         * selected. Restore the original checkout fields when NL or BE is NOT selected.
         */
        toggleAddressFields : function (country) {
            var fields = [
                this.parentName + '.street',
                this.parentName + '.city',
                this.parentName + '.postcode-field-group.field-group.postcode',
                this.parentName + '.postcode-field-group.field-group.housenumber',
                this.parentName + '.postcode-field-group.field-group.housenumber_addition'
            ];

            Registry.get(fields, function (
                streetElement,
                cityElement,
                postcodeElement,
                housenumberElement,
                housenumberAdditionalElement
            ) {
                streetElement.visible(!(country === 'BE' || country === 'NL'));
                housenumberElement.visible(country === 'BE' || country === 'NL');
                housenumberAdditionalElement.visible(country === 'BE' || country === 'NL');

                // In some countries housenumber is not required
                housenumberElement.required(country === 'BE' || country === 'NL');
                housenumberElement.validation['required-entry'] = (country === 'BE' || country === 'NL');

                // Next three lines are for initial load. Fields are available in uiRegistry, but not yet in jQuery.
                postcodeElement.additionalClasses['tig-postcode-full-width'] = !(country === 'NL' || country === 'BE');
                streetElement.additionalClasses.tig_hidden = true;
                cityElement.additionalClasses.tig_hidden = true;

                var postcodeField = $('.tig-postcode-field-group div[name$=postcode]');
                /* jshint ignore:start */
                country === 'NL' || country === 'BE' ? postcodeField.removeClass('tig-postcode-full-width') : postcodeField.addClass('tig-postcode-full-width');
                /* jshint ignore:end */

                if (country === 'NL') {
                    $('.tig_hidden').hide(200);

                    return;
                }

                $('.tig_hidden').show(200);
            });

            // Handle street fields separately in a for loop. they could be disabled in the configs.
            var streetFields = [
                this.parentName + '.street.1',
                this.parentName + '.street.2',
                this.parentName + '.street.3'
            ];

            /* jshint ignore:start */
            for (var i=0; i < streetFields.length; i++) {
                Registry.get(streetFields[i], function (streetElement) {
                    streetElement.visible(!(country === 'BE' || country === 'NL'));
                });
            }
            /* jshint ignore:end */
        },

        updateFieldData : function () {
            var self = this;

            if (typeof this.timer !== 'undefined') {
                clearTimeout(this.timer);
            }

            this.timer = setTimeout(function () {
                self.setFieldData();
            }, 1000);
        },

        setFieldData : function () {
            if (!this.source) {
                return;
            }

            var address = this.controlRegistry(State.address());
            if (!address) {
                return;
            }

            if (!address.postcode || !address.housenumber) {
                this.renderFieldsAndMessage(200, '');
                return;
            }

            if (!State.validateLastCall(address.postcode+address.housenumber)) {
                this.handelResponse(State.getLastCall(true));
            }

            if (JSON.stringify(State.address()) === JSON.stringify(address) && State.isSameCall()) {
                this.renderFieldsAndMessage(200, '');
                return;
            }

            Logger.info('Start postcode check');
            this.getAddressData(address.postcode, address.housenumber);

        },

        getAddressData : function (postcode, housenumber) {
            var self = this;

            if (self.request !== undefined) {
                self.request.abort();
            }

            self.isLoading(true);
            self.request = $.ajax({
                method:'GET',
                url : window.checkoutConfig.postcode.action_url.postcode_service,
                data : {
                    huisnummer : housenumber,
                    postcode   : postcode
                }
            }).done(function (data) {
                self.handelResponse(data, postcode+housenumber);
            }).fail(function (data) {
                Logger.error(data);
            }).always(function (data) {
                self.isLoading(false);
                Logger.info(data);
            });
        },

        handelResponse : function (data, key) {
            var self = this;
            var type = 'failed';
            if (data === null || !data.success) {
                Logger.error('Postcode check : No success');
            }

            if (data.straatnaam && data.woonplaats) {
                State.setLastCall([key, data]);
                Registry.get(self.parentName + '.street.0').set('value', data.straatnaam);
                Registry.get(self.parentName + '.city').set('value', data.woonplaats);

                var streetNr = Registry.get(self.parentName + '.street.1');
                var streetAddition = Registry.get(self.parentName + '.street.2');

                if (streetNr !== undefined) {
                    streetNr.set('value', '');
                }

                if (streetAddition !== undefined) {
                    streetAddition.set('value', '');
                }

                // Trigger change for subscripe methods.
                $("input[name*='street[0]']").trigger('change');
                $("input[name*='city']").trigger('change');

                type = 'success';
            }

            this.renderFieldsAndMessage(100, type);
        },

        renderFieldsAndMessage : function (motion, type) {
            var message = $('.tig-postcode-validation-message');

            $('.tig_hidden').show(motion);
            if (type !== 'failed') {
                this.renderAddressData();
                message.hide(motion);
                return;
            }

            message.html($.mage.__('Sorry, we could not find the address on the given zip code and house number combination. If you are sure that the zip code and house number are correct, please fill in the address details manually.')).show();
        },

        // Magaplaza and other oneStepcheckouts render the billing fields on the same page.
        renderAddressData : function () {
            if (window.checkoutConfig.postcode.checkout === 'default' ||
                window.checkoutConfig.postcode.checkout === 'blank'
            ) {
                return;
            }

            var shippingAddress = quote.shippingAddress(),
            shippingData = AddressConverter.formAddressDataToQuoteAddress(
                this.source.get('shippingAddress')
            );

            //Copy form data to quote shipping address object (Credit: Magaplaza)
            for (var shippingField in shippingData) {
                if (shippingData.hasOwnProperty(shippingField) &&
                    shippingAddress.hasOwnProperty(shippingField) &&
                    typeof shippingData[shippingField] != 'function' && //eslint-disable-line eqeqeq
                    _.isEqual(shippingAddress[shippingField], shippingData[shippingField])
                ) {
                    shippingAddress[shippingField] = shippingData[shippingField];
                } else if (typeof shippingData[shippingField] != 'function' && //eslint-disable-line eqeqeq
                    !_.isEqual(shippingAddress[shippingField], shippingData[shippingField])
                ) {
                    shippingAddress = shippingData;
                    break;
                }
            }

            quote.shippingAddress(shippingAddress);

            var billingAddress = quote.billingAddress(),
            billingData = AddressConverter.formAddressDataToQuoteAddress(
                this.source.get('billingAddress')
            );

            //Copy form data to quote shipping address object (Credit: Magaplaza)
            for (var billingField in billingData) {
                if (billingData.hasOwnProperty(billingField) &&
                    billingAddress.hasOwnProperty(billingField) &&
                    typeof billingData[billingField] != 'function' && //eslint-disable-line eqeqeq
                    _.isEqual(billingAddress[billingField], billingData[billingField])
                ) {
                    billingAddress[billingField] = billingData[billingField];
                } else if (typeof billingData[billingField] != 'function' && //eslint-disable-line eqeqeq
                    !_.isEqual(billingAddress[billingField], billingData[billingField])
                ) {
                    billingAddress = billingData;
                    break;
                }
            }

            quote.billingAddress(billingAddress);
        },

        controlRegistry : function (address) {
            var self = this;
            /**
             * Country ID is not available yet and will default to NL, causing unexpected behaviour when a customer
             * has a quote with another country in it.
             */
            if ($("[name*='" + self.customScope + ".country_id']").length < 1) {
                $('.tig_hidden').show();
                return;
            }
            var currentFormData = this.source.get(this.customScope);

            // Wait until the data is filled in.
            if (!currentFormData) {
                return null;
            }

            // MagePlaza compatibility.
            if (currentFormData.shippingAddress) {
                var tempData = currentFormData.shippingAddress;
                currentFormData.postcode = tempData.postcode;
                currentFormData.custom_attributes = tempData.custom_attributes;
            }

            $('.tig_hidden').hide();
            if (currentFormData.country_id !== "NL") {
                $('.tig_hidden').show();
                return null;
            }

            if (!currentFormData.postcode) {
                currentFormData = this.source.shippingAddress;
            }

            if (!currentFormData.postcode || !currentFormData.custom_attributes.tig_housenumber) {
                return null;
            }

            var addressData = {
                postcode    : currentFormData.postcode,
                housenumber : currentFormData.custom_attributes.tig_housenumber,
                addition    : currentFormData.custom_attributes.tig_housenumber_addition
            };

            if (JSON.stringify(addressData) !== JSON.stringify(address)) {
                address = addressData;
            }

            State.address(address);
            return address;
        },

        // Compatibility with Mageplaza - #POSTCODENL-235
        value: function () {
            return null;
        }
    });
});
