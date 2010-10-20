<?php

/*
 * Copyright 2010 Pablo Díez Pascual <pablodip@gmail.com>
 *
 * This file is part of Mondongo.
 *
 * Mondongo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Mondongo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Mondongo. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Mondongo\Extension\Extra;

use Mondongo\Mondator\Definition\Method;
use Mondongo\Mondator\Extension;
use Mondongo\Inflector;

/**
 * IdentifierAutoIncrement.
 *
 * @package Mondongo
 * @author  Pablo Díez Pascual <pablodip@gmail.com>
 */
class IdentifierAutoIncrement extends Extension
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->addOptions(array(
            'field_name' => 'identifier',
        ));
    }

    /**
     * @inheritdoc
     */
    protected function doProcess()
    {
        $fieldName = $this->getOption('field_name');

        // field
        $this->configClass['fields'][$fieldName] = 'integer';

        // index
        $this->configClass['indexes'][] = array(
            'keys'    => array($fieldName => 1),
            'options' => array('unique' => 1),
        );

        // event
        $setter = 'set'.Inflector::camelize($fieldName);

        $method = new Method('protected', 'updateIdentifierAutoIncrement', '', <<<EOF
        \$last = \$this->getRepository()
            ->getCollection()
            ->find(array(), array('$fieldName' => 1))
            ->sort(array('$fieldName' => -1))
            ->limit(1)
            ->getNext()
        ;

        \$identifier = null !== \$last ? \$last['$fieldName'] + 1 : 1;

        \$this->$setter(\$identifier);
EOF
        );
        $this->definitions['document_base']->addMethod($method);

        $this->configClass['extensions_events']['preInsert'][] = $method->getName();
    }
}
