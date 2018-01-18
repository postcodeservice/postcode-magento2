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
namespace TIG\Postcode\Test\Unit\Config\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Module\Manager as ModuleManager;

use TIG\Postcode\Test\TestCase;

abstract class AbstractConfigurationTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var ModuleManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManagerMock;

    protected function initConfigMocks()
    {
        $this->scopeConfigMock   = $this->getMock(ScopeConfigInterface::class);
        $this->moduleManagerMock = $this->getFakeMock(ModuleManager::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * @param array $args
     *
     * @return object
     */
    public function getInstance(array $args = [])
    {
        $this->initConfigMocks();

        $args['scopeConfig']   = $this->scopeConfigMock;
        $args['moduleManager'] = $this->moduleManagerMock;

        return parent::getInstance($args);
    }

    /**
     * @param      $xpath
     * @param      $value
     * @param null $storeId
     * @param null $matcher
     */
    protected function setXpath($xpath, $value, $storeId = null, $matcher = null)
    {
        if ($matcher === null) {
            $matcher = $this->once();
        }

        $getValueExpects = $this->scopeConfigMock->expects($matcher);
        $getValueExpects->method('getValue');
        $getValueExpects->with(
            $xpath,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $getValueExpects->willReturn($value);
    }

    /**
     * @param array $xpaths
     * @param array $returns
     */
    protected function setXpathConsecutive($xpaths = [], $returns = [])
    {
        $getValueExpects = $this->scopeConfigMock->expects($this->any());
        $getValueExpects->method('getValue');
        $getValueExpects->withConsecutive($this->onConsecutiveCalls($xpaths));
        $getValueExpects->will(
            new \PHPUnit_Framework_MockObject_Stub_ConsecutiveCalls($returns)
        );
    }

    protected function setModuleOutputEnabled($enabled = true)
    {
        $moduleOutPutMock = $this->moduleManagerMock->expects($this->once());
        $moduleOutPutMock->method('isOutputEnabled');
        $moduleOutPutMock->with('TIG_Postcode');
        $moduleOutPutMock->willReturn($enabled);
    }

    /**
     * @return \Generator
     */
    protected function getRandomSyntax()
    {
        for ($i = 0; $i <= 3; $i++) {
            yield [uniqid()];
        }
    }
}
