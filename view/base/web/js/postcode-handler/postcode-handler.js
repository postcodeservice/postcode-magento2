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
    'uiRegistry',
    'knockout',
    '../helper/field-types',
    '../helper/reorder-fields'
], function (registry, ko, FieldTypes, reorderFields) {
    'use strict';

    const STATE_DISABLED = 'disabled';
    const STATE_INIT = 'init';

    var postcodeInstanceId = 0;

    var postcodeInstances = [];

    /**
     * Postcode Handler Constructor
     *
     * @param config
     * @param postcodeFieldset
     * @returns {PostcodeHandler}
     * @constructor
     */
    function PostcodeHandler (config, postcodeFieldset)
    {
        this.id = postcodeInstanceId++;
        this.currentState = STATE_DISABLED;

        this.postcodeService = postcodeFieldset;
        this.config = config;

        this.setIdByContainer(postcodeFieldset);

        this.log('Init');

        postcodeInstances.push(this);

        return (this);
    }

    /**
     * Init State
     *
     * We need this for extending this abstract class
     * @type {string}
     */
    PostcodeHandler.INIT = STATE_INIT;

    /**
     * Logger
     *
     * @param message
     */
    PostcodeHandler.prototype.log = function(message){
        console.log('PostcodeService[' + this.id + '][' + this.getISOCode() + ']: ' + message);
    }

    /**
     * Concatenate field value
     *
     * This function also supports street fieldsets
     *
     * @param field
     */
    PostcodeHandler.prototype.concatenateFieldsToStreet = function(field){
        if([FieldTypes.street, FieldTypes.house_number, FieldTypes.house_number_addition].includes((field))) {
            var street = this.getPostcodeService().getFieldValue(FieldTypes.street);
            var house_number = this.getPostcodeService().getFieldValue(FieldTypes.house_number);
            var house_number_addition = this.getPostcodeService().getFieldValue(FieldTypes.house_number_addition);
            this.log("Updating magento street" , [street, house_number, house_number_addition])
            this.getPostcodeService().setFieldValue(FieldTypes.magento_street, street +
                (house_number ? ' ' + house_number : '') +
                (house_number_addition ? ' ' + house_number_addition : '')
            );
        }
    }
    /**
     * Handle field changes
     *
     * @return boolean
     * @param fieldType
     * @param value
     */
    PostcodeHandler.prototype.handle = function(fieldType, value) {
        this.concatenateFieldsToStreet(fieldType);
    }

    /**
     * Destroy PostcodeHandler
     *
     * Cleans up instance and returns fields back to default state
     *
     */
    PostcodeHandler.prototype.destroy = function(){
        this.log('Destroy');
        postcodeInstances.filter(e => e !== this);
        this.getPostcodeService().resetFieldsConfig();
    }

    /**
     * Reset PostcodeHandler
     *
     * Called t
     */
    PostcodeHandler.prototype.reset = function() {
        if (!('tig_postcode' in this.config) || this.config.tig_postcode['enabled'] === false) {
            this.setCurrentState(STATE_DISABLED);
            return;
        }

        this.setCurrentState(STATE_INIT);
        this.postcodeService.setFieldsConfig(this.config.tig_postcode);
    }

    PostcodeHandler.prototype.setIdByContainer = function(fieldSet){
        if (fieldSet && fieldSet.name) {
            this.id = fieldSet.name;
        }
    }

    PostcodeHandler.prototype.getISOCode = function() {
        return null;
    }

    PostcodeHandler.prototype.getCurrentState = function() {
        return this.currentState;
    }

    PostcodeHandler.prototype.setCurrentState = function(state) {
        this.log("State change to " + state);
        this.currentState = state;
    }

    PostcodeHandler.prototype.getPostcodeService = function() {
        return this.postcodeService;
    }

    return PostcodeHandler;
});
