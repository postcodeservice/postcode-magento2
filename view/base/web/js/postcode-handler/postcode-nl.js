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
        'jquery',
        'underscore',
        './postcode-handler',
        '../helper/field-types',
        '../helper/postcode-api'
    ], function (
        $,
        _,
        PostcodeHandler,
        FieldTypes,
        PostcodeApi
    ) {
        'use strict';

        const postcodeNlRegex = /^[0-9]{4}\s?[A-Z]{2}$/i;

        const states = Object.seal({
            INIT: PostcodeHandler.INIT,
            IDLE: 'postcode_idle',
            POSTCODE_CALL_MADE: 'postcode_call_made',
            POSTCODE_CALL_FAILED: 'postcode_call_failed',
            POSTCODE_SHOW_FIELDS_SUGGESTION: 'postcode_show_fields_suggestion',
            POSTCODE_SHOW_FIELDS_EDIT: 'postcode_show_fields_edit'
        });

        function PostcodeHandlerNL(
            config,
            postcodeService
        ) {
            this.debounceBeforeCall = null;
            this.data = {

            };
            PostcodeHandler.call(
                this,
                config,
                postcodeService
            );

            return (this);
        }

        PostcodeHandlerNL.prototype = Object.create(PostcodeHandler.prototype);

        PostcodeHandlerNL.prototype.getISOCode = function(){ return "NL";}

        PostcodeHandlerNL.prototype.callApi = _.debounce(function(postcode, house_number){
            const self = this;
            this.setCurrentState(states.POSTCODE_CALL_MADE);

            PostcodeApi.getPostCodeNL(postcode, house_number).done(function(data){
                self.getPostcodeService().getElement(FieldTypes.postcode).error('');

                if (data.success !== true) {
                    self.setCurrentState(states.POSTCODE_CALL_FAILED);
                    self.getPostcodeService().getElement(FieldTypes.postcode).error('Sorry, wij konden geen adresgegevens vinden met de opgegeven postcode en huisnummer combinatie. Indien u er zeker van bent dat de opgegeven postcode en huisnummer correct zijn, vul dan adresinformatie handmatig aan.');
                    return;
                }

                self.getPostcodeService().setFieldValue(FieldTypes.street, data.straatnaam);
                self.getPostcodeService().setFieldValue(FieldTypes.city, data.woonplaats);
                self.concatenateFieldsToStreet(FieldTypes.street);

                self.setCurrentState(states.POSTCODE_SHOW_FIELDS_SUGGESTION);
            }).fail(function(){
                self.setCurrentState(states.POSTCODE_CALL_FAILED);
            }).always(function(){
                self.getPostcodeService().showHideField(FieldTypes.street, true);
                self.getPostcodeService().showHideField(FieldTypes.city, true);
            });
        },500);

        PostcodeHandlerNL.prototype.handle = function (field_type, field_value) {
            if (this.getCurrentState() !== states.INIT) {
                if (field_type === FieldTypes.postcode) {
                    this.data.postcode = field_value;
                }

                if (field_type === FieldTypes.house_number) {
                    this.data.house_number = field_value;
                }

                // Do validation and call, state change is handled in callApi due to debouncing
                if ((field_type === FieldTypes.postcode || field_type === FieldTypes.house_number) &&
                    typeof this.data.postcode !== 'undefined' &&
                    typeof this.data.house_number !== 'undefined' &&
                    this.data.postcode.match(postcodeNlRegex) &&
                    this.data.house_number.match(/[0-9]+/)) {
                    this.callApi(this.data.postcode, this.data.house_number);
                }
            }

            switch(this.getCurrentState()) {
                case states.INIT:
                    this.setCurrentState(states.IDLE);
                    var postcodeField = this.getPostcodeService().getElement(FieldTypes.postcode);
                    var housenumberField = this.getPostcodeService().getElement(FieldTypes.house_number);

                    if (postcodeField) {
                        this.handle(FieldTypes.postcode, postcodeField.value());
                    }
                    if (housenumberField) {
                        this.handle(FieldTypes.house_number, housenumberField.value());
                    }
                    break;
                case states.POSTCODE_SHOW_FIELDS_EDIT:
                case states.POSTCODE_SHOW_FIELDS_SUGGESTION:
                    if ([FieldTypes.street, FieldTypes.city].includes(field_type)) {
                        this.setCurrentState(states.POSTCODE_SHOW_FIELDS_EDIT);
                    }
                    break;
            }

            // Important, concatenates all values to Magento street value
            PostcodeHandler.prototype.handle.call(
                this,
                field_type,
                field_value
            );
        }

        return PostcodeHandlerNL;
    }
);
