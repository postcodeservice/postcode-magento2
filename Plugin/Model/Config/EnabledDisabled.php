<?php

namespace TIG\Postcode\Plugin\Model\Config;

use Magento\Framework\Option\ArrayInterface;

class EnabledDisabled implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Enabled')],
            ['value' => '0', 'label' => __('Disabled')],
        ];
    }
}
