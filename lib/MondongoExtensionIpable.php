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
 * MondongoExtensionIpable.
 *
 * @package    Mondongo
 * @subpackage Extensions
 * @author     Pablo Díez Pascual <pablodip@gmail.com>
 */
class MondongoExtensionIpable extends MondongoExtension
{
  protected $options = array(
    'created_field'   => 'created_from',
    'updated_field'   => 'updated_from',
    'get_ip_callable' => array('MondongoExtensionIpable', 'getIp'),
  );

  protected function setup($definition)
  {
    $definition->setField($this->options['created_field'], 'string');
    $definition->setField($this->options['updated_field'], 'string');
  }

  public function preInsert()
  {
    $this->getInvoker()->set($this->options['created_field'], call_user_func($this->options['get_ip_callable']));
  }

  public function preUpdate()
  {
    $this->getInvoker()->set($this->options['updated_field'], call_user_func($this->options['get_ip_callable']));
  }

  static public function getIp()
  {
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
  }
}
