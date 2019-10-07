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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImageFileType
 */
class ImageFileType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, $this->composeFileTypeOptions($options));
        $builder->add('uri', HiddenType::class, ['required' => false]);

        $builder->addModelTransformer(new CallbackTransformer(
            function ($value) {
                return ['file' => null, 'uri' => $value];
            },
            function ($array) {
                if (!is_null($array['file']) && $array['file'] instanceof UploadedFile) {
                    return $array['file'];
                }

                return empty($array['uri']) ? '' : $array['uri'];
            }
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => false,
            'compound' => true,
            'error_bubbling' => false,
            'file_constraints' => null,
            'upload_button_text' => 'Click to upload an image',
            'replace_button_text' => 'Change image',
            'delete_button_text' => 'Delete image',
            'image_alt_text' => 'Uploaded image',
            'image_attributes' => [],
            'image_url_prefix' => '',
        ]);

        $resolver->addAllowedTypes('file_constraints', ['null', 'array']);
        $resolver->addAllowedTypes('image_attributes', 'array');
        $resolver->addAllowedTypes('upload_button_text', 'string');
        $resolver->addAllowedTypes('replace_button_text', 'string');
        $resolver->addAllowedTypes('delete_button_text', 'string');
        $resolver->addAllowedTypes('image_alt_text', 'string');
        $resolver->addAllowedTypes('image_url_prefix', 'string');
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['upload_button_text'] = $options['upload_button_text'];
        $view->vars['replace_button_text'] = $options['replace_button_text'];
        $view->vars['delete_button_text'] = $options['delete_button_text'];
        $view->vars['image_src'] = $form['uri']->getData();
        $view->vars['image_alt_text'] = $options['image_alt_text'];
        $view->vars['image_attributes'] = $options['image_attributes'];
        $view->vars['image_url_prefix'] = $options['image_url_prefix'];
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'image_file';
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function composeFileTypeOptions(array $options)
    {
        $return = [
            'required' => false,
            'error_bubbling' => true,
        ];

        if (!is_null($options['file_constraints'])) {
            $return['constraints'] = $options['file_constraints'];
        }

        foreach (['attr', 'disabled', 'multiple'] as $key) {
            if (array_key_exists($key, $options)) {
                $return[$key] = $options[$key];
            }
        }

        return $return;
    }
}
