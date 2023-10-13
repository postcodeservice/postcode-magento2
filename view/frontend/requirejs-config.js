var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'TIG_Postcode/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/create-shipping-address': {
                'TIG_Postcode/js/action/create-shipping-address-mixin': true
            },
            'Magento_Checkout/js/action/set-billing-address': {
                'TIG_Postcode/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'TIG_Postcode/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/create-billing-address': {
                'TIG_Postcode/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'TIG_Postcode/js/action/set-billing-address-mixin': true
            },
            
            // Mixin for hiding Postcode Service TIG Attributes
            'Magento_Checkout/js/view/billing-address': {
                'TIG_Postcode/js/view/hide-tig-attributes-mixin': true
            },
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': {
                'TIG_Postcode/js/view/hide-tig-attributes-mixin': true
            },
            'Magento_Checkout/js/view/shipping-information/address-renderer/default': {
                'TIG_Postcode/js/view/hide-tig-attributes-mixin': true
            }
        }
    }
};
