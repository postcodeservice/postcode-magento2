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
 * to support@postcodeservice.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
/*browser:true*/
/*global define*/
define([
    'ko'
], function (
    ko
) {
    'use strict';

    return {
        address: ko.observable(null),
        lastCall: ko.observable(null),
        sameCall: ko.observable(null),

        setLastCall: function (data) {
            this.lastCall(data);
        },

        setSameCall: function (bool) {
            this.sameCall(bool);
        },

        isSameCall: function () {
            return this.sameCall();
        },

        getLastCall: function (dataOnly) {
            if (dataOnly) {
                return this.lastCall()[1];
            }
            return this.lastCall();
        },

        validateLastCall: function (keyToMatch) {
            if (!this.lastCall) {
                return false;
            }

            if (this.lastCall[0] === keyToMatch) {
                this.setSameCall(true);
                return false;
            }

            this.setSameCall(false);

            return true;
        }
    };
});
