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
namespace TIG\Postcode\Services\Converter;

use TIG\Postcode\Services\Validation\Response as ValidationResponse;

class Response implements ConverterInterface
{
    private $validation;

    /**
     * Request constructor.
     *
     * @param ValidationResponse $validation
     */
    public function __construct(
        ValidationResponse $validation
    ) {
        $this->validation = $validation;
    }

    /**
     * {@inheritDoc}
     */
    public function convert($data)
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (!$this->validation->validate($data)) {
            return false;
        }

        return $data;
    }
}
