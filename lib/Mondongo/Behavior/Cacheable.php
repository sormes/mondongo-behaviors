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

namespace Mondongo\Behavior;

use Mondongo\Mondator\Definition\Method;
use Mondongo\Mondator\Extension;
use Mondongo\Inflector;

/**
 * Cacheable
 *
 * @package Mondongo
 * @author  Francisco Alvarez Alonso <sormes@gmail.com>
 */
class Cacheable extends Extension
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->addRequiredOption('from_fields');

        $this->addOptions(array(
            'cache_prefix' => 'cache_'
        ));

    }

    /**
     * @inheritdoc
     */
    protected function doProcess()
    {
        $cachePrefix = $this->getOption('cache_prefix');

        foreach($this->getOption('from_fields') as $field)  {

            if(isset($this->configClass['fields'][$field['name']])) {

                $cacheField = $cachePrefix.$field['name'];

                $fieldGetter = 'get'.Inflector::camelize($field['name']);
                $fieldCacheSetter = 'set'.Inflector::camelize($cacheField);
                $function = $field['function'];

                $this->configClass['fields'][$cacheField] = 'string';
                $method = new Method('protected',Inflector::camelize('update'.$cacheField),'',<<<EOF
                \$value = \$this->$fieldGetter();
                if(null != \$value)
                {
                   \$value = \$this->$function(\$value);
                   \$this->$fieldCacheSetter(\$value);
                }

EOF
                );

                $this->definitions['document_base']->addMethod($method);
                $this->configClass['extensions_events']['preInsert'][] = $method->getName();


                $method = new Method('public',$function,'$value',<<<EOF
                /*** override ***/
                return \$value;
EOF

                );

                $this->definitions['document_base']->addMethod($method);
            }
        }
    }
}