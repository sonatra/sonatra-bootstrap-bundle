<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\BootstrapBundle\Block\Type;

use Sonatra\Bundle\BlockBundle\Block\AbstractType;
use Sonatra\Bundle\BlockBundle\Block\BlockView;
use Sonatra\Bundle\BlockBundle\Block\BlockInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Dropdown Item Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class DropdownItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $linkAttr = $options['link_attr'];

        if (null !== $options['src']) {
            $linkAttr['href'] = $options['src'];
        }

        $view->vars = array_replace($view->vars, array(
            'link_attr' => $linkAttr,
            'disabled' => $options['disabled'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'src'           => '#',
            'link_attr'     => array(),
            'disabled'      => false,
            'chained_block' => true,
        ));

        $resolver->setAllowedTypes(array(
            'src'       => array('null', 'string'),
            'link_attr' => 'array',
            'disabled'  => 'bool',
        ));

        $resolver->setNormalizers(array(
            'src' => function (Options $options, $value = null) {
                if (isset($options['data'])) {
                    return $options['data'];
                }

                return $value;
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dropdown_item';
    }
}
