'use strict';
define([
    'TIG_Postcode/js/helper/field-types'
],function (FieldTypes) {
    'use strict';

    var mixin = {

        getCustomAttributeLabel: function(element) {
            // Hide TIG Elements
            if (element && 'attribute_code' in element &&
                [
                    FieldTypes.house_number,
                    FieldTypes.house_number_addition,
                    FieldTypes.street
                ].includes(element.attribute_code)) {
                return;
            }
            return this._super(element);
        }
    };

    return function (magentoBlock) {
        return magentoBlock.extend(mixin);
    };
});
