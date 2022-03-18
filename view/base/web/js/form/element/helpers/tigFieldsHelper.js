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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
define([
    'underscore',
    'knockout',
    '../../../helper/field-types',
], function (_,ko, FieldTypes) {
    'use strict';

    return {
        defaults: {
            imports: {
                updateHousenumber: '${ $.parentName }.tig_housenumber:value',
                updateHouseNumberAddition: '${ $.parentName }.tig_housenumber_addition:value',
            },
            modules: {
                housenumber: '${ $.parentName }.tig_housenumber',
                housenumber_addition: '${ $.parentName }.tig_housenumber_addition',
            }
        },

        /**
         * Called when Housenumber is updated
         *
         * @param value
         */
        updateHousenumber: function(value){
            if (this.currentPostcodeHandler && value) {
                this.currentPostcodeHandler.handle(FieldTypes.house_number, value);
            }
        },

        /**
         * Called when Housenumber addition is updated
         *
         * @param value
         */
        updateHouseNumberAddition: function(value){
            if (this.currentPostcodeHandler && value) {
                this.currentPostcodeHandler.handle(FieldTypes.house_number_addition, value);
            }
        },
    }
});
