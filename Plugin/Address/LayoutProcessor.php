<?php
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
namespace TIG\Postcode\Plugin\Address;

use TIG\Postcode\Config\Provider\CheckoutConfiguration;
use TIG\Postcode\Config\Provider\ModuleConfiguration;
use Magento\Framework\App\Config\ScopeConfigInterface;

// @codingStandardsIgnoreFile
class LayoutProcessor
{
    /**
     * @var ModuleConfiguration
     */
    private $moduleConfiguration;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CheckoutConfiguration
     */
    private $checkoutConfiguration;

    /**
     * AddressLayoutProcessor constructor.
     *
     * @param ModuleConfiguration   $moduleConfiguration
     * @param CheckoutConfiguration $checkoutConfiguration
     * @param ScopeConfigInterface  $scopeConfig
     */
    public function __construct(
        ModuleConfiguration $moduleConfiguration,
        CheckoutConfiguration $checkoutConfiguration,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->moduleConfiguration = $moduleConfiguration;
        $this->scopeConfig         = $scopeConfig;
        $this->checkoutConfiguration = $checkoutConfiguration;
    }

    /**
     * @param $subject
     * @param array $jsLayout
     *
     * @return array|mixed
     */
    public function afterProcess($subject, array $jsLayout)
    {
        if ($this->moduleConfiguration->isModusOff()) {
            return $jsLayout;
        }

        if ($this->moduleConfiguration->getCheckoutCompatibility() != 'mageplaza'
            && $this->moduleConfiguration->isBECheckEnabled()) {
            $jsLayout = $this->processBeShippingFields($jsLayout);
            $jsLayout = $this->processBeBillingFields($jsLayout);
        }

        if ($this->moduleConfiguration->isNLCheckEnabled()) {
            $jsLayout = $this->processShippingFields($jsLayout);
            $jsLayout = $this->processBillingFields($jsLayout);
        }

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     *
     * @return mixed
     */
    private function processBeShippingFields($jsLayout)
    {
        $shippingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                           ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

        $this->setFieldToAutocomplete($shippingFields);
        if (!$this->moduleConfiguration->isNLCheckEnabled()) {
            $shippingFields = $this->processBeAddress($shippingFields, 'shippingAddress', []);
        }

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     *
     * @return mixed
     */
    private function processBeBillingFields($jsLayout)
    {
        $billingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                          ['children']['payment']['children']['payments-list']['children'];

        foreach ($billingFields as $key => &$billingForm) {
            if (strpos($key, '-form') === false) {
                continue;
            }

            $this->setFieldToAutocomplete($billingForm['children']['form-fields']['children']);
            if (!$this->moduleConfiguration->isNLCheckEnabled()) {
                $billingForm['children']['form-fields']['children'] = $this->processBeAddress(
                    $billingForm['children']['form-fields']['children'],
                    $billingForm['dataScopePrefix'],
                    []
                );
            }
        }

        return $jsLayout;
    }

    /**
     * Hide the original postcode field and retrieve the postcode-field-group even when the NL check is off
     *
     * @param $fieldset
     * @param $scope
     * @param $deps
     *
     * @return mixed
     */
    private function processBeAddress($fieldset, $scope, $deps)
    {
        $jsLayout = $this->processAddress($fieldset, $scope, $deps);
        $jsLayout['postcode-field-group']['component'] = 'Magento_Ui/js/form/components/group';
        $jsLayout['postcode-field-group']['config']['template'] = 'TIG_Postcode/checkout/field-be-group';
        $this->setFieldToHide($jsLayout, 'postcode', true);

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     *
     * @return mixed
     */
    private function processShippingFields($jsLayout)
    {
        $shippingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

        $shippingFields = $this->processAddress($shippingFields, 'shippingAddress', []);

        $this->setFieldToHide($shippingFields, 'postcode', true);
        $this->setFieldToHide($shippingFields, 'city');
        $this->setFieldToHide($shippingFields, 'street');

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     *
     * @return mixed
     */
    private function processBillingFields($jsLayout)
    {
        $billingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['payments-list']['children'];

        if (!isset($billingFields) || !$this->isDisplayBillingOnPaymentMethodAvailable()) {
            return $this->processSingleBillingForm($jsLayout) ?: $jsLayout;
        }

        foreach ($billingFields as $key => &$billingForm) {
            if (strpos($key, '-form') === false) {
                continue;
            }

            $billingForm['children']['form-fields']['children'] = $this->processAddress(
                $billingForm['children']['form-fields']['children'],
                isset($billingForm['dataScopePrefix']) ? $billingForm['dataScopePrefix'] : '',
                []
            );

            $this->setFieldToHide(
                $billingForm['children']['form-fields']['children'], 'postcode', true
            );
            $this->setFieldToHide($billingForm['children']['form-fields']['children'], 'city');
            $this->setFieldToHide($billingForm['children']['form-fields']['children'], 'street');
        }

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     *
     * @return mixed
     */
    private function processSingleBillingForm($jsLayout)
    {
        $billingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['afterMethods']['children']['billing-address-form'];

        if (!isset($billingFields)) {
            return false;
        }

        $billingFields['children']['form-fields']['children'] = $this->processAddress(
            $billingFields['children']['form-fields']['children'],
            isset($billingFields['dataScopePrefix']) ? $billingFields['dataScopePrefix'] : '',
            []
        );

        $this->setFieldToHide($billingFields['children']['form-fields']['children'], 'postcode', true);
        $this->setFieldToHide($billingFields['children']['form-fields']['children'], 'city');
        $this->setFieldToHide($billingFields['children']['form-fields']['children'], 'street');

        return $jsLayout;
    }

    /**
     * @param $fieldset
     * @param $scope
     * @param $deps
     *
     * @return mixed
     */
    private function processAddress($fieldset, $scope, $deps)
    {
        $fieldset['postcode-field-group']  = [
            'validation' => null,
            'component'  => 'TIG_Postcode/js/view/form/fields',
            'type'       => 'group',
            'provider'   => 'checkoutProvider',
            'sortOrder'  => $this->checkoutConfiguration->getPostcodeSortOrder(),
            'config'     => [
                'customScope'       => $scope,
                'template'          => 'TIG_Postcode/checkout/field-group',
                'additionalClasses' => $this->moduleConfiguration->getCheckoutCompatibility()
            ],
            'deps'       => $deps,
            'children'   => [
                'field-group' => [
                    'component'   => 'uiComponent',
                    'displayArea' => 'field-group',
                    'children'    => [
                        'postcode'             => $fieldset['postcode'],
                        'housenumber'          => $this->getHouseNumberField($scope),
                        'housenumber_addition' => $this->getHouseNumberAdditionField($scope)
                    ]
                ]
            ],
            'dataScope'  => '',
            'visible'    => true
        ];
        $fieldset['country_id']['sortOrder'] = $this->checkoutConfiguration->getCountrySortOrder();
        $fieldset['city']['sortOrder'] = $this->checkoutConfiguration->getCitySortOrder();

        return $fieldset;
    }

    /**
     * @param string $scope
     *
     * @return array
     */
    private function getHouseNumberField($scope = 'shippingAddress')
    {
        return [
            'component'  => 'Magento_Ui/js/form/element/abstract',
            'config'     => [
                'customScope' => $scope . '.custom_attributes',
                'template'    => 'ui/form/field',
                'elementTmpl' => 'TIG_Postcode/form/element/housenumber'
            ],
            'provider'   => 'checkoutProvider',
            'dataScope'  => $scope . '.custom_attributes.tig_housenumber',
            'label'      => __('House number'),
            'sortOrder'  => '115',
            'validation' => [
                'required-entry' => true,
            ],
            'visible'    => true
        ];
    }

    /**
     * @param string $scope
     *
     * @return array
     */
    private function getHouseNumberAdditionField($scope = 'shippingAddress')
    {
        return [
            'component'  => 'Magento_Ui/js/form/element/abstract',
            'config'     => [
                'customScope' => $scope . '.custom_attributes',
                'template'    => 'ui/form/field',
                'elementTmpl' => 'TIG_Postcode/form/element/addition'
            ],
            'provider'   => 'checkoutProvider',
            'dataScope'  => $scope . '.custom_attributes.tig_housenumber_addition',
            'label'      => __('Addition'),
            'sortOrder'  => '120',
            'validation' => [
                'required-entry' => false,
            ],
            'visible'    => true
        ];
    }

    /**
     * Sets visible on false for the shipping fields that are re-writend by the postcode service.
     *
     * @param      $fields
     * @param      $section
     * @param bool $disableRequired
     * @param bool $be
     */
    private function setFieldToHide(&$fields, $section, $disableRequired = false)
    {
        $additionalClass = null;
        if (isset($fields[$section]['config']['additionalClasses'])) {
            $additionalClass = $fields[$section]['config']['additionalClasses'];
        }

        if ($section == 'street' || $section == 'city') {
            $additionalClass = $additionalClass . ' ' . 'tig_hidden';
        }

        $fields[$section]['visible'] = false;
        if ($disableRequired) {
            $fields[$section]['validation']['required-entry'] = false;
        }

        $fields[$section]['config']['additionalClasses'] = $additionalClass;
    }

    /**
     * @param $fields
     */
    private function setFieldToAutocomplete(&$fields)
    {
        $additionalClass = null;
        if (isset($fields['postcode']['config']['additionalClasses'])) {
            $additionalClass = $fields['postcode']['config']['additionalClasses'];
        }
        $additionalClass .= ' tig_zipcodezone_autocomplete';
        $fields['postcode']['config']['additionalClasses'] = $additionalClass;
        $fields['postcode']['config']['elementTmpl'] = 'TIG_Postcode/form/element/autocomplete';
        $fields['postcode']['sortOrder'] = $this->checkoutConfiguration->getPostcodeSortOrder();

        $additionalClass = null;
        if (isset($fields['street']['children'][0]['config']['additionalClasses'])) {
            $additionalClass = $fields['street']['children'][0]['config']['additionalClasses'];
        }

        $additionalClass .= ' tig_street_autocomplete';
        $fields['street']['children'][0]['config']['additionalClasses'] = $additionalClass;
        $fields['street']['children'][0]['config']['elementTmpl'] = 'TIG_Postcode/form/element/autocomplete';

        $fields['country_id']['sortOrder'] = $this->checkoutConfiguration->getCountrySortOrder();
        $fields['city']['sortOrder'] = $this->checkoutConfiguration->getCitySortOrder();
    }

    /**
     * @return bool
     */
    private function isDisplayBillingOnPaymentMethodAvailable()
    {
        return (bool) !$this->scopeConfig->getValue(
            'checkout/options/display_billing_address_on',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
