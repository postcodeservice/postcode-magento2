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
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Postcode\Block\Frontend\Checkout;

use Magento\Backend\Block\Template;
use Magento\Framework\View\Element\BlockInterface;
use TIG\Postcode\Config\Provider\ModuleConfiguration;

class DataProvider extends Template implements BlockInterface
{
    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_Postcode::checkout/DataProvider.phtml';

    /**
     * @var ModuleConfiguration
     */
    private $configuration;

    /**
     * DataProvider constructor.
     *
     * @param Template\Context      $context
     * @param ModuleConfiguration   $configuration
     * @param array                 $data
     */
    public function __construct(
        Template\Context $context,
        ModuleConfiguration $configuration,
        array $data = []
    ) {
        $this->configuration = $configuration;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isPostcodeNLCheckOn()
    {
        return $this->configuration->isNLCheckEnabled();
    }
}