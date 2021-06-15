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
namespace TIG\Postcode\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Checkouts implements ArrayInterface
{
    /**
     * Return option array for compatiblity checkout modus.
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => 'default', 'label' => __('Two Step Checkout Luma')],
            ['value' => 'blank', 'label' => __('Two Step Checkout Blank')],
            ['value' => 'onestepcheckout', 'label' => __('Iosc Onestepcheckout v1.2.036')],
            ['value' => 'mageplaza', 'label' => __('Mageplaza One Step Checkout v2.5.0 - v2.6.1')],
            ['value' => 'danslo', 'label' => __('Rubic Clean Checkout v1.1.0 - v2.0.0')],
            ['value' => 'amasty', 'label' => __('Amasty One Step Checkout v1.6.0 - v1.8.17')]
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
