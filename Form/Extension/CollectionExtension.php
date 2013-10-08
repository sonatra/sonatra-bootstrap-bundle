<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\BootstrapBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Collection Form Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class CollectionExtension extends AbstractTypeExtension
{
    /**
     * @var FormFactory
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @param FormFactory $factory
     */
    public function __construct(FormFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['allow_add']) {
            $btnAdd = $options['btn_add'];

            if (is_array($btnAdd)) {
                $btnAdd = $builder->create('add', 'button', $options['btn_add'])->getForm();
            }

            $builder->setAttribute('btn_add', $btnAdd);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($form->getConfig()->hasAttribute('prototype')) {
            $view->vars['prototype_name'] = $options['prototype_name'];
        }

        if ($form->getConfig()->hasAttribute('btn_add')) {
            $add = $form->getConfig()->getAttribute('btn_add');
            $view->vars['btn_add'] = $add->createView($view);
            $view->vars['btn_add']->vars['attr']['data-target'] = $view->vars['id'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'btn_add'    => array(),
            'btn_delete' => array(),
        ));

        $resolver->addAllowedTypes(array(
            'btn_add'    => array('array', 'Symfony\Component\Form\Form'),
            'btn_delete' => array('array', 'Symfony\Component\Form\Form'),
        ));

        $btnAddNormalizer = function (Options $options, $value) {
            if (is_array($value)) {
                $value = array_merge(array('label' => '', 'glyphicon' => 'plus', 'size' => 'xs', 'style' => 'default'), $value);
            }

            return $value;
        };

        $optionsNormalizer = function (Options $options, $value) {
            $value['label_attr'] = array('class' => 'sr-only');

            // btn delete
            if ($options['allow_delete'] && $options['prototype']) {
                $value['append'] = $options['btn_delete'];

                if (is_array($value['append'])) {
                    $value['append'] = array_merge(
                        array(
                            'label' => '',
                            'glyphicon' => 'remove',
                            'style' => 'danger',
                            'attr' => array(
                                'class' => 'btn-remove'
                                )
                            ),
                        $value['append']
                    );

                    $value['append'] = $this->factory->createNamed('delete', 'button', null, $value['append']);
                }

                $value['row_attr'] = array('class' => 'form-collection-row');
            }

            return $value;
        };

        $resolver->setNormalizers(array(
            'btn_add' => $btnAddNormalizer,
            'options' => $optionsNormalizer,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'collection';
    }
}
