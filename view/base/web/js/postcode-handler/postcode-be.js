define(
    [
        'jquery',
        'knockout',
        'underscore',
        './postcode-handler',
        '../helper/field-types',
        '../helper/postcode-api',
        'jquery-ui-modules/autocomplete'
    ],
    function (
        $,
        ko,
        _,
        PostcodeHandler,
        FieldTypes,
        PostcodeApi
    ) {
        'use strict';

        const postcodeBeRegex = /^[1-9][0-9]{3}$/i;

        const states = Object.seal({
            INIT: PostcodeHandler.INIT,
            IDLE: 'postcode_idle',
            POSTCODE_CALL_MADE: 'postcode_call_made',
            POSTCODE_CALL_FAILED: 'postcode_call_failed',
            POSTCODE_SHOW_FIELDS_SUGGESTION: 'postcode_show_fields_suggestion',
            POSTCODE_SHOW_FIELDS_EDIT: 'postcode_show_fields_edit'
        });

        function PostcodeHandlerBE(
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

        PostcodeHandlerBE.prototype = Object.create(PostcodeHandler.prototype);

        PostcodeHandlerBE.prototype.getISOCode = function(){ return "BE";}

        PostcodeHandlerBE.prototype.destroy = function(){
            this.deleteAutoComplete();
            PostcodeHandler.prototype.destroy.call(this);
        }

        PostcodeHandlerBE.prototype.getAutoCompleteResultCity = function() {
            var currentPostcodeService = this.getPostcodeService();
            var postcodeField = currentPostcodeService.getElement(FieldTypes.postcode);
            var domPostcodeField = $('#' + postcodeField.uid);

            if ($('.tig-autocomplete-result-city', domPostcodeField.parent()).length === 0) {
                domPostcodeField.parent().append('<span class="tig-autocomplete-result-city"></span>');
            }

            return $('.tig-autocomplete-result-city', domPostcodeField.parent());
        }

        PostcodeHandlerBE.prototype.deleteAutoComplete = function() {
            var currentPostcodeService = this.getPostcodeService();

            var postcodeField = currentPostcodeService.getElement(FieldTypes.postcode);
            var domPostcodeField = $('#' + postcodeField.uid);
            if (domPostcodeField.length > 0 && domPostcodeField.data('uiAutocomplete')) {
                domPostcodeField.autocomplete("destroy");
            }

            var streetField = currentPostcodeService.getElement(FieldTypes.street);
            var domStreetField = $('#' + streetField.uid);
            if (domStreetField.length > 0 && domStreetField.data('uiAutocomplete')) {
                domStreetField.autocomplete("destroy");
            }

            $('.tig-autocomplete-result-city', domPostcodeField.parent()).remove();
        }

        PostcodeHandlerBE.prototype.addAutoCompleteToPostcode = function () {
            var self = this;

            var currentPostcodeService = this.getPostcodeService();
            var postcodeField = currentPostcodeService.getElement(FieldTypes.postcode);
            var domPostcodeField = $('#' + postcodeField.uid);

            if (domPostcodeField.length === 0) {
                return;
            }

            domPostcodeField.attr('autocomplete', 'off');

            self.getAutoCompleteResultCity();
            domPostcodeField.autocomplete({
                delay: 500,
                source: function (zipcodezone, response) {
                    this.menu.element.addClass(this.customScope + ".tigAutocomplete");
                    this.menu.element.addClass('tigJqueryUiClass');

                    domPostcodeField.addClass('auto-complete-running');

                    self.getAutoCompleteResultCity().text('');

                    response([{
                        label: $.mage.__('Busy with loading zipcodes...'),
                        data: false
                    }]);

                    PostcodeApi.getPostCodeBE(zipcodezone.term).done(function(data){
                        // If no results are found, a success = false is returned
                        if (data.success === false) {
                            response({label: $.mage.__('No results found.')});
                            return;
                        }

                        var selectBoxArr = [];
                        $.each(data, function (key) {
                            selectBoxArr.push({
                                label: data[key].postcode + ' - ' + data[key].plaats,
                                value: data[key].postcode,
                                data: data[key]
                            });
                        });

                        response(selectBoxArr);
                    });
                },
                select: function (event, ui) {
                    if (ui.item.value == $.mage.__('Busy with loading zipcodes...')) {
                        ui.item.value = '';
                        return false;
                    }

                    if (typeof ui.item.data === 'undefined') {
                        return false;
                    }

                    var data = ui.item.data;
                    self.getPostcodeService().setFieldValue(FieldTypes.city, data.plaats);
                    self.getPostcodeService().setFieldValue(FieldTypes.postcode, data.postcode);

                    self.getAutoCompleteResultCity().text(' - ' + data.plaats);
                },
                close: function() {
                    domPostcodeField.removeClass('auto-complete-running');
                }
            });
        }

        PostcodeHandlerBE.prototype.addAutoCompleteToStreet = function () {
            var self = this;

            var currentPostcodeService = this.getPostcodeService();
            var streetFieldZero = currentPostcodeService.getElement(FieldTypes.street);
            var streetField = $('#' + streetFieldZero.uid);

            if (streetField.length === 0) {
                return;
            }

            streetField.attr('autocomplete', 'yes');

            streetField.autocomplete({
                delay: 500,
                source : function (street, response) {
                    this.menu.element.addClass('tigJqueryUiClass');
                    this.menu.element.appendTo(this.element.closest('.tig_street_autocomplete'));

                    response([{
                        label: $.mage.__('Busy with loading streets...'),
                        data: false
                    }]);

                    var postcode = currentPostcodeService.getElement(FieldTypes.postcode).value();
                    var city = currentPostcodeService.getElement(FieldTypes.city).value();

                    PostcodeApi.getStreetBe(postcode, street.term, city).done(function(data){
                        // If no results are found, a success = false is returned
                        if (data.success == false) {
                            response([{
                                label: $.mage.__('No results found.'),
                                data: false
                            }]);
                            return;
                        }

                        var selectBoxArr = [];
                        $.each(data, function (key, value) {
                            selectBoxArr.push({
                                label: data[key].straat,
                                value: data[key].straat
                            });
                        });

                        response(selectBoxArr);
                    });
                },
                select : function (event, ui) {
                    if (ui.item.value == $.mage.__('Busy with loading streets...')) {
                        ui.item.value = '';
                        return false;
                    }

                    if (typeof ui.item.data === false) {
                        return false;
                    }

                    var data = ui.item.value;
                    self.getPostcodeService().setFieldValue(FieldTypes.street, data);
                }
            });
        }

        PostcodeHandlerBE.prototype.handle = function (field_type, field_value) {
            if (field_type === FieldTypes.postcode) {
                this.data.postcode = field_value;
            }

            switch(this.getCurrentState()) {
                case states.INIT:
                    this.setCurrentState(states.IDLE);
                    this.getPostcodeService().addClassesToField(FieldTypes.street,{'tig_street_autocomplete': true});
                    this.getPostcodeService().addClassesToField(FieldTypes.postcode,{'tig_zipcodezone_autocomplete': true});
                    this.addAutoCompleteToPostcode(this.getPostcodeService().getElement(FieldTypes.postcode));
                    this.addAutoCompleteToStreet(this.getPostcodeService().getElement(FieldTypes.street));
                    break;
            }
            PostcodeHandler.prototype.handle.call(this, field_type, field_value);
        }
        return PostcodeHandlerBE;
    }
);
