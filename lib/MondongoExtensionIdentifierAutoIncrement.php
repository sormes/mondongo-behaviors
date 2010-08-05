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

/**
 * MondongoExtensionIdentifierAutoIncrement.
 *
 * @package    Mondongo
 * @subpackage Extensions
 * @author     Pablo Díez Pascual <pablodip@gmail.com>
 */
class MondongoExtensionIdentifierAutoIncrement extends MondongoExtension
{
  protected $options = array(
    'field' => 'identifier',
  );

  protected function setup($definition)
  {
    $definition->setField($this->options['field'], 'integer');

    $definition->addIndex(array('fields' => array($this->options['field'] => 1), 'options' => array('unique' => 1)));
  }

  public function preInsert()
  {
    $invoker = $this->getInvoker();

    $last = $invoker->getRepository()->getMongoCollection()
      ->find(array(), array($this->options['field'] => 1))
      ->sort(array($this->options['field'] => -1))
      ->limit(1)
      ->getNext()
    ;

    $id = null !== $last ? $last[$this->options['field']] + 1 : 1;

    $invoker->set($this->options['field'], $id);
  }
}
