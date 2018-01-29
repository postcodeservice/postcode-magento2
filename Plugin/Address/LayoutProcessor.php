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
namespace TIG\Postcode\Plugin\Address;

use Magento\Checkout\Block\Checkout\LayoutProcessor as MagentoProcessor;
use TIG\Postcode\Config\Provider\ModuleConfiguration;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @codingStandardsIgnoreStart
 */
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
     * AddressLayoutProcessor constructor.
     *
     * @param ModuleConfiguration $moduleConfiguration
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ModuleConfiguration $moduleConfiguration,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->moduleConfiguration = $moduleConfiguration;
        $this->scopeConfig         = $scopeConfig;
    }

    /**
     * @param MagentoProcessor $subject
     * @param array           $jsLayout
     *
     * @return array|mixed
     */
    public function afterProcess(MagentoProcessor $subject, array $jsLayout)
    {
        if ($this->moduleConfiguration->isModusOff()) {
            return $jsLayout;
        }

        $jsLayout = $this->processShippingFields($jsLayout);
        $jsLayout = $this->processBillingFields($jsLayout);

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

        $fieldGroup = &$shippingFields['postcode-field-group']['children']['field-group']['children'];

        $fieldGroup['postcode']             = $shippingFields['postcode'];
        $fieldGroup['housenumber']          = $this->getHouseNumberField();
        $fieldGroup['housenumber_addition'] = $this->getHouseNumberAdditionField();

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
            return $this->processSingleBillingForm($jsLayout);
        }

        foreach ($billingFields as $key => &$billingForm) {
            if (!strpos($key, '-form')) {
                continue;
            }

            $billingForm['children']['form-fields']['children'] = $this->processAddress(
                $billingForm['children']['form-fields']['children'],
                $billingForm['dataScopePrefix'],
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
            return $jsLayout;
        }

        $billingFields['children']['form-fields']['children'] = $this->processAddress(
            $billingFields['children']['form-fields']['children'],
            $billingFields['dataScopePrefix'],
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
        $fieldset['postcode-field-group'] = [
            'component' => 'TIG_Postcode/js/view/form/fields',
            'type'      => 'group',
            'provider'  => 'checkoutProvider',
            'sortOrder' => '65',
            'config'    => [
                'customScope' => $scope,
                'template'    => 'TIG_Postcode/checkout/field-group',
            ],
            'deps'      => $deps,
            'children'  => [
                'field-group' => [
                    'component'   => 'uiComponent',
                    'displayArea' => 'field-group',
                    'children'  => [
                        'postcode'             => $fieldset['postcode'],
                        'housenumber'          => $this->getHouseNumberField($scope),
                        'housenumber_addition' => $this->getHouseNumberAdditionField($scope)
                    ]
                ]
            ],
            'visible' => true
        ];

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
                'customScope' => $scope,
                'template'    => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input'
            ],
            'provider'   => 'checkoutProvider',
            'dataScope'  => $scope . '.tig_housenumber',
            'label'      => __('Housenumber'),
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
                'customScope' => $scope,
                'template'    => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input'
            ],
            'provider'   => 'checkoutProvider',
            'dataScope'  => $scope . '.tig_housenumber_addition',
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
     * @param $fields
     * @param $section
     * @param $disableRequired
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
/**
 * @codingStandardsIgnoreEnd
 */
