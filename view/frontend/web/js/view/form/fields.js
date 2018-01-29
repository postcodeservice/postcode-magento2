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
    'TIG_Postcode/js/Address/Finder',
    'TIG_Postcode/js/Helper/Logger',
    'TIG_Postcode/js/Address/State',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry'
], function (
    $,
    uiComponent,
    ko,
    Finder,
    Logger,
    State,
    CheckoutData,
    Registry
) {
    'use strict';

    var failed = true;

    return uiComponent.extend({
        defaults : {
            template : 'TIG_Postcode/checkout/field-group'
        },

        initialize : function () {
            this._super()
                ._setClasses();

            this.initModules();

            return this;
        },

        initObservable : function () {
            this._super().observe([]);

            if (!window.checkoutConfig.postcode.postcode_active) {
                return this;
            }

            State.parent(this);

            // Check for changes and value of the postcode service fields.
            Finder.subscribe(function (address) {
                if (!address) {
                    this.renderFieldsAndMessage(200, '');
                    return;
                }

                if (State.address()) {
                    address = this.controllRegistry(address);
                }

                if (JSON.stringify(State.address()) === JSON.stringify(address)) {
                    return;
                }

                State.address(address);

                if (!address.postcode || !address.housenumber) {
                    this.renderFieldsAndMessage(200, '');
                    return;
                }

                Logger.info('Start postcode check');
                this.getAddressData(address.postcode, address.housenumber);

            }.bind(this));

            return this;
        },

        getAddressData : function (postcode, housenumber) {
            var self = this;
            $.ajax({
                method:'GET',
                url : window.checkoutConfig.postcode.action_url.postcode_service,
                data : {
                    huisnummer : housenumber,
                    postcode   : postcode
                }
            }).done(function (data) {
                self.handelResponse(data);
            }).fail(function (data) {
                Logger.error(data);
            }).always(function (data) {
                Logger.info(data);
            });
        },

        handelResponse : function (data) {
            var type = 'failed';
            if (data === null || !data.success) {
                Logger.error('Postcode check : No success');
            }

            if (data.straatnaam && data.woonplaats) {
                Logger.info('Postcode check : Success, going to parse data');
                Registry.get(State.parent().parentName + '.street.0').set('value', data.straatnaam);
                Registry.get(State.parent().parentName + '.city').set('value', data.woonplaats);
                type = 'success';
            }

            this.renderFieldsAndMessage(200, type);
        },

        renderFieldsAndMessage : function (motion, type) {

            $('.tig_hidden').slideDown(motion);
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
                message.slideUp(motion);
            }, 4000);
        },

        controllRegistry : function (address) {

            debugger;
            var selector = this.parentName + '.postcode-field-group.field-group';
            var addressData = {
                postcode    : Registry.get(selector + '.postcode').value(),
                housenumber : Registry.get(selector + '.housenumber').value(),
                addition    : Registry.get(selector + '.housenumber_addition').value()
            };

            if (JSON.stringify(addressData) !== JSON.stringify(address)) {
                address = addressData;
            }

            return address;
        }
    });
});
