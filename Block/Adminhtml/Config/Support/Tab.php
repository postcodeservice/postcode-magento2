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
namespace TIG\Postcode\Block\Adminhtml\Config\Support;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use TIG\Postcode\Config\Provider\ModuleConfiguration;

class Tab extends Template implements RendererInterface
{
    const MODULE_NAME = 'TIG_Postcode';

    const EXTENSION_VERSION = '1.5.1';

    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_Postcode::config/support/tab.phtml';

    /**
     * @var ModuleConfiguration
     */
    private $moduleConfiguration;

    /**
     * Tab constructor.
     *
     * @param Template\Context    $context
     * @param ModuleConfiguration $moduleConfiguration
     * @param array               $data
     */
    public function __construct(
        Template\Context $context,
        ModuleConfiguration $moduleConfiguration,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->moduleConfiguration = $moduleConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->setElement($element);

        return $this->toHtml();
    }

    /**
     * Retrieve the version number from the database.
     *
     * @return bool|false|string
     */
    public function getVersionNumber()
    {
        return static::EXTENSION_VERSION;
    }

    /**
     * @return string
     */
    public function getSupportedMagentoVersions()
    {
        return $this->moduleConfiguration->getSupportedMagentoVersions();
    }
}
