<?php

namespace TIG\Postcode\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Module\ModuleList;

class LessLoader implements ObserverInterface
{
    /** @var ModuleList  */
    public $moduleList;

    /**
     * @param ModuleList $moduleList
     */
    public function __construct(ModuleList $moduleList){
        $this->moduleList = $moduleList;
    }

    /**
     * @param Observer $observer
     *
     * @return Observer
     */
    public function execute(Observer $observer)
    {
        $array = $this->getEnabledModuleList();

        // TODO: implement a better way
        if (in_array('Amasty_Checkout', $array)){
            $observer->getLayout()->getUpdate()->addUpdate($this->getXmlCode());
        }

        return $observer;
    }

    /**
     * Return the correct Less file in XML format
     *
     * @return string
     */
    private function getXmlCode(){
        // TODO: implement better?
        return '<head>
                    <css src="TIG_Postcode::css/amasty_checkout.less"/>
                </head>';
    }

    /**
     * Return an array of the enabled modules (bin/magento module:status)
     *
     * @return array
     */
    private function getEnabledModuleList(){
        return $this->moduleList->getNames();
    }
}
