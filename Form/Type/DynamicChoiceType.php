<?php
/*
 * This file is part of the NadiaFormBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DynamicChoiceType
 */
class DynamicChoiceType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('list', ChoiceType::class, $this->composeChoiceTypeOptions($options));
        $builder->add('value', HiddenType::class, ['required' => false, 'error_bubbling' => true]);

        $builder->addModelTransformer($this->getModelTransformer($options));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $choices = $event->getForm()->getConfig()->getOption('choices');

            if (empty($choices)) {
                $data['list'] = null;

                $event->setData($data);
            }
        }, 256);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound' => true,
            'error_bubbling' => false,
            'ajax_uri' => '',
            'ajax_method' => 'GET',
            'ajax_extra_settings' => new \stdClass(),
            'target' => '',
            'build_ajax_uri_callback' => '',
            'build_ajax_data_callback' => '',
            'default_ajax_data_key' => 'q',
            'render_html_callback' => '',
            'auto_call_ajax_onload' => true,

            // For ChoiceType
            'choice_constraints' => null,
            'choices' => null,
            'choice_attr' => null,
            'choice_label' => null,
            'choice_loader' => null,
            'choice_name' => null,
            'choice_translation_domain' => null,
            'choice_value' => null,
            'expanded' => null,
            'group_by' => null,
            'multiple' => null,
            'placeholder' => null,
            'preferred_choices' => null,
        ]);

        $resolver->addAllowedTypes('choice_constraints', ['null', 'array']);
        $resolver->addAllowedTypes('ajax_uri', 'string');
        $resolver->addAllowedTypes('ajax_extra_settings', 'stdClass');
        $resolver->addAllowedTypes('target', 'string');
        $resolver->addAllowedTypes('build_ajax_uri_callback', 'string');
        $resolver->addAllowedTypes('build_ajax_data_callback', 'string');
        $resolver->addAllowedTypes('default_ajax_data_key', 'string');
        $resolver->addAllowedTypes('render_html_callback', 'string');
        $resolver->addAllowedTypes('auto_call_ajax_onload', 'bool');

        $resolver->addAllowedValues('ajax_method', ['GET', 'POST']);
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['build_ajax_uri_callback_name'] = '__default__DynamicChoiceBuildAjaxUriCallback';
        $view->vars['build_ajax_uri_callback'] = '';
        $view->vars['build_ajax_data_callback_name'] = '__default__DynamicChoiceBuildAjaxDataCallback';
        $view->vars['build_ajax_data_callback'] = '';
        $view->vars['render_html_callback_name'] = '__default__DynamicChoiceRenderHtmlCallback';
        $view->vars['render_html_callback'] = '';

        if (!empty($options['build_ajax_uri_callback'])) {
            $view->vars['build_ajax_uri_callback'] = $options['build_ajax_uri_callback'];
            $view->vars['build_ajax_uri_callback_name'] =
                '__'.$this->getCallbackNameById($view->vars['id']).'_DynamicChoiceBuildAjaxUriCallback';
        }

        if (!empty($options['build_ajax_data_callback'])) {
            $view->vars['build_ajax_data_callback'] = $options['build_ajax_data_callback'];
            $view->vars['build_ajax_data_callback_name'] =
                '__'.$this->getCallbackNameById($view->vars['id']).'_DynamicChoiceBuildAjaxDataCallback';
        }

        if (!empty($options['render_html_callback'])) {
            $view->vars['render_html_callback'] = $options['render_html_callback'];
            $view->vars['render_html_callback_name'] =
                '__'.$this->getCallbackNameById($view->vars['id']).'_DynamicChoiceRenderHtmlCallback';
        }

        $view->vars['attr'] = array_merge($view->vars['attr'], [
            'data-form-type' => 'dynamic-choice',
            'data-ajax-uri' => $options['ajax_uri'],
            'data-ajax-method' => $options['ajax_method'],
            'data-ajax-extra-settings' => json_encode($options['ajax_extra_settings']),
            'data-target' => $options['target'],
            'data-build-ajax-uri-callback-name' => $view->vars['build_ajax_uri_callback_name'],
            'data-build-ajax-data-callback-name' => $view->vars['build_ajax_data_callback_name'],
            'data-default-ajax-data-key' => $options['default_ajax_data_key'],
            'data-render-html-callback-name' => $view->vars['render_html_callback_name'],
            'data-auto-call-ajax-onload' => $options['auto_call_ajax_onload'] ? 1 : 0,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dynamic_choice';
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function composeChoiceTypeOptions(array $options)
    {
        $return = [
            'required' => $options['required'],
            'error_bubbling' => true,
        ];
        $keys = [
            'choices', 'choice_attr', 'choice_label', 'choice_loader', 'choice_name', 'choice_translation_domain',
            'choice_value', 'expanded', 'group_by', 'multiple', 'placeholder', 'preferred_choices',
        ];

        if (!is_null($options['choice_constraints'])) {
            $return['constraints'] = $options['choice_constraints'];
        }

        foreach ($keys as $key) {
            if (!is_null($options[$key])) {
                $return[$key] = $options[$key];
            }
        }

        return $return;
    }

    /**
     * @param array $options Form options
     *
     * @return DataTransformerInterface
     */
    protected function getModelTransformer(array $options = [])
    {
        return new CallbackTransformer(
            function ($value) {
                return ['list' => $value, 'value' => $value];
            },
            function ($array) {
                return empty($array['value']) ? null : $array['value'];
            }
        );
    }

    /**
     * @param string $id
     *
     * @return string
     */
    private function getCallbackNameById($id)
    {
        return preg_replace_callback(
            '/[^a-zA-Z0-9_]+/',
            function ($matches) {
                return str_repeat('_', strlen($matches[0]));
            },
            $id
        );
    }
}
