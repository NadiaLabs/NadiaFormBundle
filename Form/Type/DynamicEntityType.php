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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DynamicEntityType
 */
class DynamicEntityType extends DynamicChoiceType
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $emNormalizer = function (Options $options, $em) {
            if (null !== $em) {
                if ($em instanceof ObjectManager) {
                    return $em;
                }

                return $this->registry->getManager($em);
            }

            $em = $this->registry->getManagerForClass($options['class']);

            if (null === $em) {
                throw new RuntimeException(sprintf('Class "%s" seems not to be a managed Doctrine entity. Did you forget to map it?', $options['class']));
            }

            return $em;
        };

        $resolver->setDefaults([
            'em' => null,
        ]);

        $resolver->setRequired(['class']);

        $resolver->setNormalizer('em', $emNormalizer);

        $resolver->setAllowedTypes('em', ['null', 'string', 'Doctrine\Common\Persistence\ObjectManager']);
    }

    /**
     * {@inheritDoc}
     */
    protected function getModelTransformer(array $options = [])
    {
        return new CallbackTransformer(
            function ($entity) use ($options) {
                $id = $entity instanceof $options['class'] ? $entity->getId() : null;

                return ['list' => null, 'value' => $id];
            },
            function ($array) use ($options) {
                $id = $array['value'];
                /** @var ObjectManager $em */
                $em = $options['em'];
                $repo = $em->getRepository($options['class']);
                $entity = $repo->find($id);

                return empty($array['value']) ? null : $entity;
            }
        );
    }
}
