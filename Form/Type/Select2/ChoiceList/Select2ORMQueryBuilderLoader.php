<?php

/*
 * This file is part of the NadiaFormBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaFormBundle\Form\Type\Select2\ChoiceList;

use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;

/**
 * Class Select2ORMQueryBuilderLoader
 */
class Select2ORMQueryBuilderLoader extends ORMQueryBuilderLoader
{
    /**
     * @inheritdoc
     */
    public function getEntities()
    {
        return [];
    }
}
