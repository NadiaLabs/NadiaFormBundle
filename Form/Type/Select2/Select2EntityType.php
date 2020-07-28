<?php

/*
 * This file is part of the NadiaFormBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaFormBundle\Form\Type\Select2;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Nadia\Bundle\NadiaFormBundle\Form\Type\Select2\ChoiceList\Select2ORMQueryBuilderLoader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class Select2EntityType
 */
class Select2EntityType extends EntityType
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var array Option keys for initialize Select2 elements
     */
    protected $s2OptionKeys = [
        's2_allow_clear',
        's2_close_on_select',
        's2_debug',
        's2_dropdown_auto_width',
        's2_dropdown_css_class',
        's2_maximum_input_length',
        's2_maximum_selection_length',
        's2_minimum_input_length',
        's2_minimum_results_for_search',
        's2_selection_css_class',
        's2_select_on_close',
        's2_tags',
        's2_theme',
        's2_width',
        's2_scroll_after_select',
    ];

    /**
     * Select2EntityType constructor.
     *
     * @param ManagerRegistry $registry
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(ManagerRegistry $registry, UrlGeneratorInterface $urlGenerator, array $defaultOptions = [])
    {
        parent::__construct($registry);

        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritDoc
     */
    public function getLoader(ObjectManager $manager, $queryBuilder, $class)
    {
        if (!$queryBuilder instanceof QueryBuilder) {
            throw new \TypeError(
                sprintf(
                    'Expected an instance of "%s", but got "%s".',
                    QueryBuilder::class,
                    \is_object($queryBuilder) ? \get_class($queryBuilder) : \gettype($queryBuilder)
                )
            );
        }

        return new Select2ORMQueryBuilderLoader($queryBuilder);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $s2DataFormatter = function ($rows, array $options) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $results = [];

            foreach ($rows as $row) {
                $results[] = [
                    $options['data_key_id'] => $propertyAccessor->getValue($row, $options['data_property_id']),
                    $options['data_key_text'] => $propertyAccessor->getValue($row, $options['data_property_text']),
                ];
            }

            return $results;
        };

        $s2BooleanValueNormalizer = function (Options $options, $value) {
            return true === $value ? 'true' : 'false';
        };

        $resolver->setDefaults([
            'data_key_id' => 'id',
            'data_key_text' => 'text',
            'data_property_id' => 'id',
            'data_property_text' => 'id',
            'data_formatter' => $s2DataFormatter,
            'ajax_route_name' => '',
            'ajax_route_parameters' => [],
        ]);
        $resolver->setDefined($this->s2OptionKeys);

        $resolver->addAllowedTypes('ajax_route_name', 'string');
        $resolver->addAllowedTypes('ajax_route_parameters', 'array');

        $booleanOptionKeys = [
            's2_allow_clear',
            's2_close_on_select',
            's2_debug',
            's2_dropdown_auto_width',
            's2_select_on_close',
            's2_tags',
            's2_scroll_after_select',
        ];
        foreach ($booleanOptionKeys as $key) {
            $resolver->addAllowedTypes($key, 'boolean');
            $resolver->addNormalizer($key, $s2BooleanValueNormalizer);
        }

        $resolver->addAllowedTypes('s2_dropdown_css_class', 'string');
        $resolver->addAllowedTypes('s2_selection_css_class', 'string');
        $resolver->addAllowedTypes('s2_theme', 'string');
        $resolver->addAllowedTypes('s2_width', 'string');

        $resolver->addAllowedTypes('s2_maximum_input_length', 'int');
        $resolver->addAllowedTypes('s2_maximum_selection_length', 'int');
        $resolver->addAllowedTypes('s2_minimum_input_length', 'int');
        $resolver->addAllowedTypes('s2_minimum_results_for_search', 'int');
    }

    /**
     * @inheritDoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $attr =& $view->vars['attr'];

        if (!empty($options['ajax_route_name'])) {
            $attr['data-ajax--url'] = $this->urlGenerator->generate(
                $options['ajax_route_name'],
                $options['ajax_route_parameters']
            );
        }

        if ($options['disabled']) {
            $attr['data-disabled'] = 'true';
        }
        if ($options['multiple']) {
            $attr['data-multiple'] = 'true';
        }
        if (!empty($options['placeholder'])) {
            $attr['data-placeholder'] = $options['placeholder'];
        }

        foreach ($this->s2OptionKeys as $key) {
            if (!array_key_exists($key, $options)) {
                continue;
            }

            // 去掉前綴的 "s2_"
            $s2AttributeKey = substr($key, 3);
            $s2AttributeKey = str_replace('_', '-', $s2AttributeKey);
            $attr['data-' . $s2AttributeKey] = $options[$key];
        }

        if (!empty($view->vars['data'])) {
            $rows = $options['multiple'] ? $view->vars['data'] : [$view->vars['data']];
            $select2Choices = call_user_func($options['data_formatter'], $rows, $options);
            $attr['data-data'] = json_encode($select2Choices);

            $view->vars['choices'] = $this->createChoiceViews($select2Choices, $options);
        }

        $attr['data-selected-value'] = json_encode((array) $view->vars['value']);
    }

    /**
     * @param array $select2Choices
     * @param array $options
     *
     * @return ChoiceView[]
     */
    private function createChoiceViews(array $select2Choices, array $options)
    {
        $choiceViews = [];

        foreach ($select2Choices as $choice) {
            $choiceViews[] = new ChoiceView($choice, $choice[$options['data_key_id']], $choice[$options['data_key_text']]);
        }

        return $choiceViews;
    }
}
