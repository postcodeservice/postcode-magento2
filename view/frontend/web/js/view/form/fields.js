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
    'Magento_Ui/js/form/components/group',
    'ko',
    'TIG_Postcode/js/Helper/Logger',
    'TIG_Postcode/js/Address/State',
    'uiRegistry'
], function (
    $,
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
            timer    : undefined
        },

        initialize : function () {
            this._super()
                ._setClasses();

            var self = this;
            Registry.async(this.provider)(function () {
                self.initModules();
                self.updateFieldData();
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

            var address = this.controllRegistry(State.address());
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

            $('.tig_hidden').show(motion);
            if (type !== 'failed') {
                return;
            }

            var message = $('.tig-postcode-validation-message');
            message.html($.mage.__('Can not find the address, please fill in manually')).show();

            var timer;
            if (typeof timer !== 'undefined') {
                clearTimeout(timer);
            }

            timer = setTimeout(function () {
                message.hide(motion);
            }, 4000);
        },

        controllRegistry : function (address) {
            var currentFormData = this.source.get(this.customScope);

            // Wait until the data is filled in.
            if (!currentFormData) {
                return null;
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
