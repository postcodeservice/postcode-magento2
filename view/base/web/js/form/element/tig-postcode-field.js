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
    'Magento_Ui/js/form/element/post-code',
    'jquery',
    'knockout',
    'mageUtils',

    './helpers/tigStreetField',
    './helpers/cityHelper',
    './helpers/countryHelper',
    './helpers/tigFieldsHelper',
    './helpers/magentoStreetFieldset',

    '../../helper/field-types',
    '../../helper/reorder-fields'
], function (MagentoUiPostcode, $, ko, utils,
    TigStreetHelper,
    CityHelper,
    CountryHelper,
    TigFieldsHelper,
    MagentoStreetHelper,
    FieldTypes, reorderFields
) {
    'use strict';

    return MagentoUiPostcode.extend(utils.extend({
        uiInitialized: false,
        elemCount: 0,

        currentPostcodeHandler: null,

        defaults: {
            template: 'TIG_Postcode/form/collection',
            imports: {
                updatePostcode: '${ $.name }:value'
            },

            modules: {
                postcode: '${ $.parentName }.postcode',
                fieldSet: '${ $.parentName }'
            }
        },

        initialize: function(){
            this._super();
        },

        /**
         * After Render at this moment all elements are in the DOM so it is safe
         * to restart the handler to initialize fields.
         */
        afterRender: function(){
            if (!this.currentPostcodeHandler || this.uiInitialized) {
                return
            }
            this.uiInitialized = true;
            this.currentPostcodeHandler.reset();
            this.currentPostcodeHandler.handle();
        },

        /**
         * Called when Postcode is updated
         *
         * @param value
         */
        updatePostcode: function(value){
            if (this.currentPostcodeHandler && value) {
                this.currentPostcodeHandler.handle(FieldTypes.postcode, value);
            }
        },

        /***********************************************************************
         *  Accessors fields and configuration, called by postcodeHandler      *
         ***********************************************************************/
        /**
         * Get Related field by FieldType
         *
         * @param fieldType
         * @returns {null|*}
         */
        getElement: function(fieldType){
            switch(fieldType){
                case FieldTypes.street:
                    return this.street();
                case FieldTypes.city:
                    return this.city();
                case FieldTypes.postcode:
                    return this.postcode();
                case FieldTypes.house_number:
                    return this.housenumber();
                case FieldTypes.house_number_addition:
                    return this.housenumber_addition();
                case FieldTypes.magento_street:
                    return this.magento_street();
            }
            return null;
        },

        /**
         * Reset field config to default
         *
         * Used to restore related fields back to default
         */
        resetFieldsConfig: function() {
            for(const key of Object.keys(FieldTypes)){
                const fieldType = FieldTypes[key];
                const elem = this.getElement(fieldType);
                if (elem && 'tig_defaults' in elem) {
                    this.setFieldConfig(elem, elem.tig_defaults, true);
                    delete elem.tig_defaults;
                }
            }
            reorderFields(this.fieldSet());
        },

        /**
         * Store default value so we can restore the fields back to its default
         *
         * @param field
         * @param key
         * @param value
         */
        storeDefault: function(field, key, value) {
            if (!('tig_defaults' in field)){
                field.tig_defaults = {};
            }

            if(key in field.tig_defaults) {
                console.warn("Trying to overwrite default value ", field.index, key, value, field.tig_defaults[key]);
                return;
            }

            if(typeof value === 'function') {
                value = value();
            }
            if(typeof value === 'object') {
                value = _.assign({}, value);
            }
            field.tig_defaults[key] = value;
        },

        /**
         * Set Field Config
         *
         * We obtain a config array through the CountryOptions containing the
         * initial settings for each related field.
         *
         * See PHP Class \TIG\Postcode\Model\ResourceModel\Country\CollectionPlugin
         * @param field
         * @param config
         * @param isReset
         */
        setFieldConfig: function (field, config, isReset = false){
            if ('sortOrder' in config) {
                if(!isReset && 'sortOrder' in field) {
                    this.storeDefault(
                        field,
                        'sortOrder',
                        field.sortOrder
                    );
                }
                field.sortOrder = config.sortOrder;
            }

            if ('visible' in config) {
                if (!isReset && 'visible' in field) {
                    this.storeDefault(
                        field,
                        'visible',
                        field.visible
                    );
                }

                this.showHideField(field, config.visible);
            }

            if ('rows' in config) {
                if (!isReset) {
                    this.storeDefault(field, 'rows', field.elems().length);
                }
                if ('elems' in field) {
                    for(var i = 0; i < field.elems().length; i++) {
                        this.showHideField(field.elems()[i], i < config.rows);
                    }
                }
            }

            if (!isReset && 'classes' in config){
                if('additionalClasses' in field) {
                    this.storeDefault(
                        field,
                        'classes',
                        field.additionalClasses
                    );
                }

                this.addClassesToField(
                    field,
                    config.classes
                );
            }

            if(isReset) {
              this.disableTigClasses(field);
            }
        },

        /***
         * Set config for all known fields
         *
         * @param config
         */
        setFieldsConfig: function(config) {
            for(const key in FieldTypes){
                var fieldType = FieldTypes[key];
                const elem = this.getElement(fieldType);
                if(fieldType in config && elem){
                    this.setFieldConfig(elem, config[fieldType]);
                }
            }

            if (this.containers && this.containers.length > 0) {
                reorderFields(this.fieldSet());
            }
        },

        /***********************************************************************
         *  Accessors for field changes (classes, visibility, rowCount)        *
         ***********************************************************************/

        /**
         * Add CSS Classes to field
         *
         * @param field fieldType or Field
         * @param classes
         */
        addClassesToField: function(field, classes){
            if (typeof field !== 'object') {
                field = this.getElement(field);
            }
            if (typeof field.additionalClasses == 'function') {
                field.additionalClasses(
                    _.extend(
                        field.additionalClasses(),
                        classes
                    )
                );
                return;
            }
            field.additionalClasses = _.extend(field.additionalClasses, classes);
        },

        /**
         * Disable TIG Classes
         *
         * Sets all `tig_%` classes to disabled since
         * removing classes from additionalClasses does not work
         *
         * @param field
         */
        disableTigClasses: function(field) {
            if (typeof field !== 'object') {
                field = this.getElement(field);
            }

            var classes = this.getClassesFromField(field);
            for(var index in classes){
                if(index.startsWith('tig_')){
                    classes[index] = false;
                }
            }
            this.addClassesToField(field, classes);
        },

        /**
         * Get CSS Classes from field
         *
         * @param field
         * @returns {number|string|*}
         */
        getClassesFromField: function(field){
            if (typeof field !== 'object') {
                field = this.getElement(field);
            }

            if(typeof field.additionalClasses == 'function') {
                return field.additionalClasses();
            }
            return field.additionalClasses;
        },

        /**
         * Show or hide field
         *
         * @param field
         * @param show
         */
        showHideField: function(field, show) {
            if (typeof field !== 'object') {
                field = this.getElement(field);
            }

            if('visible' in field) {
                field.visible(show);
            }

            this.addClassesToField(field, {'tig_hidden': !show});
        },

        /**
         * Set field value
         *
         * This function also supports street fieldsets
         *
         * @param field
         * @param value
         */
        setFieldValue: function(field, value) {
            if (typeof field !== 'object') {
                field = this.getElement(field);
            }

            if (!field){
                return;
            }

            if ('elems' in field && field.elems().length > 0){
                this.setFieldValue(field.elems()[0], value);
                return;
            }

            if ('value' in field && typeof field.value === 'function') {
                field.value(value);
                return;
            }

            if ('value' in field) {
                field.value = value;
            }
        },

        /**
         * Set field value
         *
         * This function also supports street fieldsets
         *
         * @param field
         */
        getFieldValue: function(field) {
            if (typeof field !== 'object') {
                field = this.getElement(field);
            }

            if (!field){
                return null;
            }

            if ('elems' in field && field.elems().length > 0){
                return this.getFieldValue(field.elems()[0]);
            }

            if ('value' in field && typeof field.value === 'function') {
                return field.value();
            }

            if ('value' in field) {
                return field.value;
            }
            return null;
        },

        /**
         * We expand this object with functions from below helpers, these help
         * too keep this file smaller and more readable.
         */
    }, TigStreetHelper, CityHelper, CountryHelper, TigFieldsHelper, MagentoStreetHelper));
});
