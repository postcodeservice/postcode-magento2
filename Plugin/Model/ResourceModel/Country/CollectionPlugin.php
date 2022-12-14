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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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

namespace TIG\Postcode\Plugin\Model\ResourceModel\Country;

use Magento\Framework\Module\FullModuleList;
use TIG\Postcode\Config\Provider\ModuleConfiguration;
use Magento\Directory\Model\ResourceModel\Country\Collection;

class CollectionPlugin
{
    const SORT_ORDER_BASE = 'sortOrderBase';
    const SORT_ORDER_INCREMENT = 'sortOrderIncrement';

    const SORT_ORDER_CONFIG = [
        'Mageplaza_OSC' => [
            self::SORT_ORDER_BASE => 10,
            self::SORT_ORDER_INCREMENT => 0.1
        ]
    ];

    /**
     * @var ModuleConfiguration
     */
    private $moduleConfiguration;

    /**
     * @var FullModuleList
     */
    private $fullModuleList;

    public function __construct(
        ModuleConfiguration $moduleConfiguration,
        FullModuleList $fullModuleList
    ){
        $this->moduleConfiguration = $moduleConfiguration;
        $this->fullModuleList = $fullModuleList;
    }

    /**
     * @param $sortOrderBase
     * @param $sortOrderIncrement
     *
     * @return array
     */
    private function getPostcodeNLConfig($sortOrderBase, $sortOrderIncrement): array
    {
        return [
            'enabled' => $this->moduleConfiguration->isNLCheckEnabled(),
            'postcode' => [
                'sortOrder' => $sortOrderBase,
                'classes' => [
                    'tig_postcode_field' => true,
                    'tig_postcode_nl' => true
                ],
                'visible' => true,
            ],
            'tig_housenumber' => [
                'sortOrder' => $sortOrderBase + $sortOrderIncrement,
                'visible' => true,
                'classes' => [
                    'tig_housenumber_field' => true,
                    'tig_postcode_nl' => true

                ]
            ],
            'tig_housenumber_addition' => [
                'sortOrder' => $sortOrderBase + 2 * $sortOrderIncrement,
                'visible' => true,
                'classes' => [
                    'tig_housenumber_addition_field' => true,
                    'tig_postcode_nl' => true
                ]
            ],
            'tig_street' => [
                'sortOrder' => $sortOrderBase + 3 * $sortOrderIncrement,
                'visible' => false,
                'classes' => [
                    'tig_street_field' => true,
                    'tig_postcode_nl' => true
                ]
            ],
            'street' => [
                'sortOrder' => $sortOrderBase + 4 * $sortOrderIncrement,
                'visible' => false,
                'classes' => [
                    'tig_street_fields' => true,
                    'tig_postcode_nl' => true
                ]
            ],
            'city' => [
                'sortOrder' => $sortOrderBase + 5 * $sortOrderIncrement,
                'visible' => false,
                'classes' => [
                    'tig_city_field' => true,
                    'tig_postcode_nl' => true
                ]
            ]
        ];
    }

    /**
     * @param $sortOrderBase
     * @param $sortOrderIncrement
     *
     * @return array
     */
    private function getPostcodeBEConfig($sortOrderBase, $sortOrderIncrement) {
        return [
            'enabled' => $this->moduleConfiguration->isBECheckEnabled(),
            'postcode' => [
                'sortOrder' => $sortOrderBase,
                'classes' => [
                    'tig_postcode_field' => true,
                    'tig_postcode_be' => true
                ],
                'visible' => true,
            ],
            'tig_housenumber' => [
                'sortOrder' => $sortOrderBase + $sortOrderIncrement,
                'visible' => true,
                'classes' => [
                    'tig_housenumber_field' => true,
                    'tig_postcode_be' => true
                ]
            ],
            'tig_housenumber_addition' => [
                'sortOrder' => $sortOrderBase + 2 * $sortOrderIncrement,
                'visible' => true,
                'classes' => [
                    'tig_housenumber_addition_field' => true,
                    'tig_postcode_be' => true
                ]
            ],
            'tig_street' => [
                'sortOrder' => $sortOrderBase + 3 * $sortOrderIncrement,
                'visible' => true,
                'classes' => [
                    'tig_street_field' => true,
                    'tig_postcode_be' => true
                ],
            ],
            'street' => [
                'sortOrder' => $sortOrderBase + 4 * $sortOrderIncrement,
                'visible' => false,
                'classes' => [
                    'tig_street_fields' => true,
                    'tig_postcode_be' => true
                ]
            ],
            'city' => [
                'sortOrder' => $sortOrderBase + 5 * $sortOrderIncrement,
                'visible' => true,
                'classes' => [
                    'tig_city_field' => true,
                    'tig_postcode_be' => true
                ]
            ],
        ];
    }

    /**
     * @param $countryOption
     * @param $country
     * @param $sortOrderBase
     * @param $sortOrderIncrement
     */
    private function addPostcodeConfig(&$countryOption, $country, $sortOrderBase, $sortOrderIncrement)
    {
        if ($country === "NL") {
            $countryOption['tig_postcode'] = $this->getPostcodeNLConfig($sortOrderBase, $sortOrderIncrement);
        }

        if ($country === "BE") {
            $countryOption['tig_postcode'] = $this->getPostcodeBEConfig($sortOrderBase, $sortOrderIncrement);
        }
    }

    /**
     * @return int[]
     */
    private function getSortOrderAndIncrement(){
        $sortOrderBase      = 81;
        $sortOrderIncrement = 1;

        foreach(self::SORT_ORDER_CONFIG as $module => $config){
            if(!$this->fullModuleList->has($module)) {
                continue;
            }
            $sortOrderBase      = $config[self::SORT_ORDER_BASE];
            $sortOrderIncrement = $config[self::SORT_ORDER_INCREMENT];
        }

        return [$sortOrderBase, $sortOrderIncrement];
    }

    /**
     * @param Collection    $subject
     * @param               $result
     *
     * @return mixed
     * @see Collection::toOptionArray
     */
    public function afterToOptionArray(
        $subject,
        $result
    ) {
        list($sortOrderBase, $sortOrderIncrement) = $this->getSortOrderAndIncrement();

        foreach ($result as &$countryOption) {
            $this->addPostcodeConfig(
                $countryOption,
                $countryOption['value'],
                $sortOrderBase,
                $sortOrderIncrement
            );
        }

        return $result;
    }
}
