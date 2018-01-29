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
    'ko',
    'jquery'
], function (
    ko,
    $
) {
    'use strict';

    var addressData = {
        postcode    : null,
        housenumber : null,
        addition    : null
    },
    countryCode, timer, valueUpdateNotifier = ko.observable(null);

    var fields = [
        "input[name*='postcode']",
        "input[name*='tig_housenumber']",
        "select[name*='country_id']"
    ];

    /**
     * Without cookie data Magento is not observing the fields so the AddressFinder is never triggered.
     * The Timeout is needed so it gives the Notifier the chance to retrieve the correct country code,
     * and not the default value.
     */
    $(document).on('change', fields.join(','), function () {
        // Clear timeout if exists.
        if (typeof timer !== 'undefined') {
            clearTimeout(timer);
        }

        timer = setTimeout(function () {
            countryCode = $("select[name*='country_id']").val();
            valueUpdateNotifier.notifySubscribers();
        }, 500);
    });

    return ko.computed(function () {
        valueUpdateNotifier();

        if (countryCode !== 'NL') {
            return null;
        }

        addressData.postcode    = $("input[name*='postcode']").val();
        addressData.housenumber = $("input[name*='tig_housenumber']").val();
        addressData.addition    = $("input[name*='tig_housenumber_addition']").val();


        if (!addressData.postcode || !addressData.housenumber) {
            return null;
        }

        return addressData;

    }.bind(this));
});
