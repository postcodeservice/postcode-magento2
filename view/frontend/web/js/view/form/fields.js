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
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
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
    'uiRegistry'
], function (
    $,
    quote,
    AddressConverter,
    uiComponent,
    ko,
    Logger,
    State,
    Registry
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

        initialize : function () {
            this._super()
                ._setClasses();

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

            if (value !== 'NL') {
                message.hide();
                return;
            }

            if (value) {
                this.updateFieldData();
            }
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
            addressData = AddressConverter.formAddressDataToQuoteAddress(
                this.source.get('shippingAddress')
            );

            //Copy form data to quote shipping address object (Credit: Magaplaza)
            for (var field in addressData) {
                if (addressData.hasOwnProperty(field) &&
                    shippingAddress.hasOwnProperty(field) &&
                    typeof addressData[field] != 'function' && //eslint-disable-line eqeqeq
                    _.isEqual(shippingAddress[field], addressData[field])
                ) {
                    shippingAddress[field] = addressData[field];
                } else if (typeof addressData[field] != 'function' && //eslint-disable-line eqeqeq
                    !_.isEqual(shippingAddress[field], addressData[field])
                ) {
                    shippingAddress = addressData;
                    break;
                }
            }

            quote.shippingAddress(shippingAddress);
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
        }
    });
});
