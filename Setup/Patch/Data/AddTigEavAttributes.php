<?php

namespace TIG\Postcode\Setup\Patch\Data;

use Magento\Customer\Model\Indexer\Address\AttributeProvider;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Eav\Setup\EavSetupFactory;

class AddTigEavAttributes implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @param ModuleDataSetupInterface     $moduleDataSetup
     * @param EavSetupFactory              $eavSetupFactory
     * @param EavConfig                    $eavConfig
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        EavConfig $eavConfig,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->moduleDataSetup     = $moduleDataSetup;
        $this->eavSetupFactory     = $eavSetupFactory;
        $this->eavConfig           = $eavConfig;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @param string   $entityType
     * @param string   $attribute
     * @param string[] $forms
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function addAttributeToForms(
        $entityType,
        $attribute,
        $forms = [
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address',
            'customer_address'
        ]
    ) {
        $attribute = $this->eavConfig->getAttribute($entityType, $attribute);
        $attribute->addData([
            'used_in_forms' => $forms
        ]);
        $this->attributeRepository->save($attribute);
    }

    /**
     * {@inheritDoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            AttributeProvider::ENTITY,
            'tig_housenumber',
            [
                'type'             => 'varchar',
                'input'            => 'text',
                'label'            => 'Housenumber',
                'visible'          => true,
                'required'         => false,
                'user_defined'     => true,
                'system'           => false,
                'group'            => 'General',
                'sort_order'       => 65,
                'global'           => true,
                'visible_on_front' => true,
            ]
        );

        $eavSetup->addAttribute(
            AttributeProvider::ENTITY,
            'tig_housenumber_addition',
            [
                'type'             => 'varchar',
                'input'            => 'text',
                'label'            => 'Housenumber Addition',
                'visible'          => true,
                'required'         => false,
                'user_defined'     => true,
                'system'           => false,
                'group'            => 'General',
                'sort_order'       => 66,
                'global'           => true,
                'visible_on_front' => true,
            ]
        );

        $eavSetup->addAttribute(
            AttributeProvider::ENTITY,
            'tig_street',
            [
                'type'             => 'varchar',
                'input'            => 'text',
                'label'            => 'Street',
                'visible'          => true,
                'required'         => false,
                'user_defined'     => true,
                'system'           => false,
                'group'            => 'General',
                'sort_order'       => 64,
                'global'           => true,
                'visible_on_front' => true,
            ]
        );

        $this->addAttributeToForms(AttributeProvider::ENTITY, 'tig_housenumber');
        $this->addAttributeToForms(AttributeProvider::ENTITY, 'tig_housenumber_addition');
        $this->addAttributeToForms(AttributeProvider::ENTITY, 'tig_street');

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(AttributeProvider::ENTITY, 'tig_housenumber');
        $eavSetup->removeAttribute(AttributeProvider::ENTITY, 'tig_housenumber_addition');
        $eavSetup->removeAttribute(AttributeProvider::ENTITY, 'tig_street');
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritDoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getAliases()
    {
        return [];
    }
}
