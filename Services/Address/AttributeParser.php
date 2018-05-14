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
namespace TIG\Postcode\Services\Address;

class AttributeParser
{
    private $tigHousenumber = null;

    private $tigHousenumberAddition = null;

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function set(array $attributes)
    {
        if (isset($attributes['tig_housenumber'])) {
            $this->tigHousenumber = $attributes['tig_housenumber'];
        }

        if (isset($attributes['tig_housenumber_addition'])) {
            $this->tigHousenumberAddition = $attributes['tig_housenumber_addition'];
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getTigHousenumber()
    {
        return $this->tigHousenumber;
    }

    /**
     * @return null
     */
    public function getTigHousenumberAddition()
    {
        return $this->tigHousenumberAddition;
    }
}
