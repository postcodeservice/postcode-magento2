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
    const ONE_STREETFIELD    = 1;
    const TWO_STREETFIELDS   = 2;
    const THREE_STREETFIELDS = 3;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => static::ONE_STREETFIELD, 'label' => __('Use 1 street field')],
            ['value' => static::TWO_STREETFIELDS, 'label' => __('Use 2 street fields')],
            ['value' => static::THREE_STREETFIELDS, 'label' => __('Use 3 street fields')],
        ];
        // @codingStandardsIgnoreEnd
        return $options;
    }
}
