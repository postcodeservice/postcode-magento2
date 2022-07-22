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
 * to support@postcodeservice.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.com for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Postcode\Test\Unit\Plugin\Address;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Plugin\Address\LayoutProcessor;
use TIG\Postcode\Config\Provider\ModuleConfiguration;
use Magento\Framework\App\Config\ScopeConfigInterface;

class LayoutProcessorTest extends TestCase
{
    /** @var LayoutProcessor */
    protected $instanceClass = LayoutProcessor::class;

    /** @var \array[][][][][]  */
    private $addressFieldsForMultipleBillingFields = [
        'components' => [
            'checkout' => [
                'children' => [
                    'steps' => [
                        'children' => [
                            'shipping-step' => [
                                'children' => [
                                    'shippingAddress' => [
                                        'children' => [
                                            'shipping-address-fieldset' => [
                                                'children' => [
                                                    'postcode' => [
                                                        'config' => [
                                                            'additionalClasses' => 'test'
                                                        ]
                                                    ],
                                                    'street' => [
                                                        'children' => [
                                                            0 => [
                                                                'config' => [
                                                                    'additionalClasses' => 'test'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'billing-step' => [
                                'children' => [
                                    'payment' => [
                                        'children' => [
                                            'payments-list' => [
                                                'children' => [
                                                    'test' => [
                                                        'children' => [
                                                            'form-fields' => [
                                                                'children' => [
                                                                    'postcode' => []
                                                                ]
                                                            ]
                                                        ]
                                                    ],
                                                    'test-form' => [
                                                        'children' => [
                                                            'form-fields' => [
                                                                'children' => [
                                                                    'postcode' => []
                                                                ]
                                                            ],
                                                        ],
                                                        'dataScopePrefix' => 'billing'
                                                    ]
                                                ]
                                            ],
                                            'afterMethods' => [
                                                'children' => [
                                                    'billing-address-form' => [
                                                        'children' => [
                                                            'form-fields' => [
                                                                'children' => [
                                                                    'postcode' => []
                                                                ]
                                                            ]
                                                        ],
                                                        'dataScopePrefix' => 'billing'
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    /** @var \array[][][][][]  */
    private $addressFields = [
        'components' => [
            'checkout' => [
                'children' => [
                    'steps' => [
                        'children' => [
                            'shipping-step' => [
                                'children' => [
                                    'shippingAddress' => [
                                        'children' => [
                                            'shipping-address-fieldset' => [
                                                'children' => [
                                                    'postcode' => [
                                                        'config' => [
                                                            'additionalClasses' => 'test'
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'billing-step' => [
                                'children' => [
                                    'payment' => [
                                        'children' => [
                                            'payments-list' => [
                                                'children' => [
                                                    'form-fields' => [
                                                        'children' => [
                                                            'postcode' => []
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'afterMethods' => [
                                                'children' => [
                                                    'billing-address-form' => [
                                                        'children' => [
                                                            'form-fields' => [
                                                                'children' => [
                                                                    'postcode' => []
                                                                ]
                                                            ]
                                                        ],
                                                        'dataScopePrefix' => 'billing'
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    /** @var \array[][][][][]  */
    private $addressFieldsWithoutBilling = [
        'components' => [
            'checkout' => [
                'children' => [
                    'steps' => [
                        'children' => [
                            'shipping-step' => [
                                'children' => [
                                    'shippingAddress' => [
                                        'children' => [
                                            'shipping-address-fieldset' => [
                                                'children' => [
                                                    'postcode' => [
                                                        'config' => [
                                                            'additionalClasses' => 'test'
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'billing-step' => [
                                'children' => [
                                    'payment' => [
                                        'children' => [
                                            'payments-list' => [
                                                'children' => [
                                                    'form-fields' => [
                                                        'children' => [
                                                            'postcode' => []
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            'isDisplayBillingOnPaymentMethodAvailble true' => [true, $this->addressFields, true],
            'isDisplayBillingOnPaymentMethodAvailble false' => [false,
                $this->addressFieldsForMultipleBillingFields,
                true],
            'isDisplayBillingOnPaymentMethodAvailble true without billingfields' =>
            [
                true, $this->addressFieldsWithoutBilling, false
            ]
        ];
    }

    /**
     * @return array
     */
    public function beDataProvider()
    {
        return [
            'test without additional classes' => [$this->addressFieldsForMultipleBillingFields],
//            'test with additional classes' => [$this->addressFieldsForAdditionalClasses]
        ];
    }

    /**
     * @param $isDisplayBillingOnPaymentMethodAvailble
     * @param $fields
     * @param $hasBilling
     *
     * @dataProvider dataProvider
     * @throws \Exception
     */
    public function testAfterProcess($isDisplayBillingOnPaymentMethodAvailble, $fields, $hasBilling)
    {
        $instance = $this->getInstance([
            'moduleConfiguration' => $this->getModuleMock(false, true),
            'scopeConfig' => $this->getScopeConfigMock($isDisplayBillingOnPaymentMethodAvailble)
        ]);

        $result = $instance->afterProcess(null, $fields);

        if (!$hasBilling) {
            $billingField = $result['components']['checkout']['children']['steps']['children']['billing-step']
                            ['children']['payment']['children']['payments-list']['children'];
            $this->assertTrue(count($billingField) == 1);
            return;
        }

        $checkBillingFields = $result['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['payment']['children']['afterMethods']['children']
        ['billing-address-form']['children']['form-fields']['children'];

        $checkShippingFields = $result['components']['checkout']['children']['steps']['children']['shipping-step']
                               ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

        if (!$isDisplayBillingOnPaymentMethodAvailble) {
            $checkBillingFields = $result['components']['checkout']['children']['steps']['children']['billing-step']
                                  ['children']['payment']['children']['payments-list']['children']['test-form']
                                  ['children']['form-fields']['children'];
        }

        $this->assertArrayHasKey('postcode-field-group', $checkBillingFields);
        $this->assertArrayHasKey('postcode-field-group', $checkShippingFields);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testBeAfterProcess()
    {
        $instance = $this->getInstance([
            'moduleConfiguration' => $this->getModuleMock(false, false, true),
            'scopeConfig' => $this->getScopeConfigMock(true)
        ]);

        $result = $instance->afterProcess(null, $this->addressFieldsForMultipleBillingFields);

        $checkShippingFields = $result['components']['checkout']['children']['steps']['children']['shipping-step']
                               ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

        $checkBillingFields = $result['components']['checkout']['children']['steps']['children']['billing-step']
                              ['children']['payment']['children']['payments-list']['children']['test-form']
                              ['children']['form-fields']['children'];

        $this->assertContains(
            'tig_zipcodezone_autocomplete',
            $checkBillingFields['postcode']['config']['additionalClasses']
        );
        $this->assertContains(
            'tig_zipcodezone_autocomplete',
            $checkShippingFields['postcode']['config']['additionalClasses']
        );
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testAfterProcessWhereModusIsOff()
    {
        $instance = $this->getInstance([
            'moduleConfiguration' => $this->getModuleMock(true)
        ]);

        $result = $instance->afterProcess(null, $this->addressFields);

        $this->assertEquals($this->addressFields, $result);
    }

    /**
     * @param bool $returns
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getScopeConfigMock($returns = false)
    {
        $scopeMock = $this->getFakeMock(ScopeConfigInterface::class)->getMock();
        $scopeExpects = $scopeMock->expects($this->any());
        $scopeExpects->method('getValue')->with(
            'checkout/options/display_billing_address_on',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $scopeExpects->willReturn($returns);

        return $scopeMock;
    }

    /**
     * @param bool $returns
     * @param bool $nl
     * @param bool $be
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getModuleMock($returns = false, $nl = false, $be = false)
    {
        $moduleMock = $this->getFakeMock(ModuleConfiguration::class)->getMock();
        $moduleExpects = $moduleMock->expects($this->once());
        $moduleExpects->method('isModusOff');
        $moduleExpects->willReturn($returns);

        if (!$returns) {
            $moduleCheckNl = $moduleMock->expects($this->any());
            $moduleCheckNl->method('isNLCheckEnabled');
            $moduleCheckNl->willReturn($nl);

            $moduleCheckBe = $moduleMock->expects($this->any());
            $moduleCheckBe->method('isBECheckEnabled');
            $moduleCheckBe->willReturn($be);
        }

        return $moduleMock;
    }
}
