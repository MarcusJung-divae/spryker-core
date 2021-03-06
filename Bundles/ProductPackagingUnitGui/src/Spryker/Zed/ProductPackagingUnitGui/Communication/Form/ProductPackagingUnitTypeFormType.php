<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductPackagingUnitGui\Communication\Form;

use Generated\Shared\Transfer\ProductPackagingUnitTypeTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @method \Spryker\Zed\ProductPackagingUnitGui\Communication\ProductPackagingUnitGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductPackagingUnitGui\ProductPackagingUnitGuiConfig getConfig()
 */
class ProductPackagingUnitTypeFormType extends AbstractType
{
    public const FIELD_NAME = 'name';
    public const FIELD_PRODUCT_PACKAGING_UNIT_TYPE_TRANSLATIONS = 'translations';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => ProductPackagingUnitTypeTransfer::class,
            'constraints' => [
                $this->getFactory()->createUniqueProductPackagingUnitTypeNameConstraint(),
            ],
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $this->addNameField($event->getForm());
        });
        $this->addProductPackagingUnitTranslationFields($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function addNameField(FormInterface $form)
    {
        $form->add(
            static::FIELD_NAME,
            TextType::class,
            [
                'label' => 'Key *',
                'required' => true,
                'attr' => [
                    'readonly' => true,
                ],
            ]
        );

        return $form;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addProductPackagingUnitTranslationFields(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_PRODUCT_PACKAGING_UNIT_TYPE_TRANSLATIONS, CollectionType::class, [
            'entry_type' => ProductPackagingUnitTypeTranslationFormType::class,
            'allow_add' => false,
            'allow_delete' => false,
            'prototype' => true,
        ]);

        return $this;
    }
}
