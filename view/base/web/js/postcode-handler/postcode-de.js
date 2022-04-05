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
define(
    [
        'underscore',
        './postcode-handler',
        '../helper/field-types'
    ],
    function (
        underscore,
        PostcodeHandler,
        FieldTypes
    ) {
        'use strict';

        const postcodeDeRegex = /^[0-9]{4}\s?[A-Z]{2}$/i;

        function PostcodeHandlerDE(config,fields) {
            this.states = Object.seal({
                INIT: 'init',
            });

            PostcodeHandler.call(
                this,
                config,
                fields
            );

            return (this);
        }

        PostcodeHandlerDE.prototype = Object.create(PostcodeHandler.prototype);

        PostcodeHandlerDE.prototype.getISOCode = function(){ return "DE";}

        PostcodeHandlerDE.prototype.handle = function (field_type, field_value) {
            this.log('Handler @ ' + this.getCurrentState() + ': ' + field_type + ' => ' + field_value);
            switch(this.getCurrentState()) {
                case this.states.INIT:

                    break;
            }
        }
        return PostcodeHandlerDE;
    }
);
