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
namespace TIG\Postcode\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Parser implements ArrayInterface
{
    const STREETFIELD_ONE    = 1;
    const STREETFIELD_TWO    = 2;
    const STREETFIELD_THREE  = 3;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => static::STREETFIELD_ONE, 'label' => __('Street address line %1', [1])],
            ['value' => static::STREETFIELD_TWO, 'label' => __('Street address line %1', [2])],
            ['value' => static::STREETFIELD_THREE, 'label' => __('Street address line %1', [3])],
        ];
        // @codingStandardsIgnoreEnd
        return $options;
    }
}
