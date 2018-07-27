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
namespace TIG\Postcode\Test\Unit\Plugin\Address;

use TIG\Postcode\Test\TestCase;
use TIG\Postcode\Plugin\Address\LayoutProcessor;
use TIG\Postcode\Config\Provider\ModuleConfiguration;
use Magento\Framework\App\Config\ScopeConfigInterface;

class LayoutProcessorTest extends TestCase
{
    protected $instanceClass = LayoutProcessor::class;

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

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            'isDisplayBillingOnPaymentMethodAvailble true' => [true, $this->addressFields],
            'isDisplayBillingOnPaymentMethodAvailble false' => [false, $this->addressFieldsForMultipleBillingFields]
        ];
    }

    /**
     * @param $isDisplayBillingOnPaymentMethodAvailble
     * @param $fields
     *
     * @dataProvider dataProvider
     */
    public function testAfterProcess($isDisplayBillingOnPaymentMethodAvailble, $fields)
    {
        $instance = $this->getInstance([
            'moduleConfiguration' => $this->getModuleMock(),
            'scopeConfig' => $this->getScopeConfigMock($isDisplayBillingOnPaymentMethodAvailble)
        ]);

        $result = $instance->afterProcess(null, $fields);

        $checkBillingFields = $result['components']['checkout']['children']['steps']['children']['billing-step']
                              ['children']['payment']['children']['afterMethods']['children']['billing-address-form']
                              ['children']['form-fields']['children'];

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
        $scopeExpects = $scopeMock->expects($this->once());
        $scopeExpects->method('getValue')->with(
            'checkout/options/display_billing_address_on',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $scopeExpects->willReturn($returns);

        return $scopeMock;
    }

    /**
     * @param bool $returns
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getModuleMock($returns = false)
    {
        $moduleMock = $this->getFakeMock(ModuleConfiguration::class)->getMock();
        $moduleExpects = $moduleMock->expects($this->once());
        $moduleExpects->method('isModusOff');
        $moduleExpects->willReturn($returns);

        return $moduleMock;
    }
}
