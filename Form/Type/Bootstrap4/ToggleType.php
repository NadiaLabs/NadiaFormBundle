<?php
/*
 * This file is part of the NadiaFormBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaFormBundle\Form\Type\Bootstrap4;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ToggleType
 *
 * A FormType for supporting Bootstrap 4 Toggle
 *
 * @see https://gitbrent.github.io/bootstrap4-toggle/
 */
class ToggleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new CallbackTransformer(
            // Convert data from the normalized to the view format
            function ($data) use ($options) {
                return $data == $options['on_value'] ? 1 : 0;
            },
            // Convert from the view to the normalized format
            function ($data) use ($options) {
                return $data == 1 ? $options['on_value'] : $options['off_value'];
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $emptyData = function (FormInterface $form, $viewData) {
            return $viewData;
        };

        $resolver->setDefaults([
            'toggle_options' => [],
            'on_value' => true,
            'off_value' => false,
            'empty_data' => $emptyData,
            'value' => 1,
            'compound' => false,
        ]);

        $resolver->addAllowedTypes('toggle_options', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bootstrap4_toggle';
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (empty($view->vars['attr'])) {
            $view->vars['attr'] = [];
        }

        $toggleOptions = array_merge([
            'on' => 'On',
            'of' => 'Off',
            'onstyle' => 'success',
            'offstyle' => 'danger',
            'size' => null,
            'style' => null,
            'width' => null,
            'height' => null,
        ], $options['toggle_options']);

        foreach ($toggleOptions as $key => $value) {
            if (!is_null($value)) {
                $view->vars['attr']['data-'.$key] = $value;
            }
        }

        $view->vars['checked'] = $form->getData() === $options['on_value'];
        $view->vars['value'] = $options['value'];
    }
}
