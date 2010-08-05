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
 * MondongoExtensionSluggable.
 *
 * @package    Mondongo
 * @subpackage Extensions
 * @author     Pablo Díez Pascual <pablodip@gmail.com>
 */
class MondongoExtensionSluggable extends MondongoExtension
{
  protected $options = array(
    'from_field' => null,
    'slug_field' => 'slug',
    'unique'     => true,
    'update'     => false,
    'builder'    => array('MondongoExtensionSluggable', 'slugify'),
  );

  protected function setup($definition)
  {
    if (null === $this->options['from_field'])
    {
      throw new RuntimeException('The option "from_field" is required.');
    }

    $definition->setField($this->options['slug_field'], 'string');

    if ($this->options['unique'])
    {
      $definition->addIndex(array('fields' => array($this->options['slug_field'] => 1), array('unique' => 1)));
    }
  }

  public function preInsert()
  {
    $this->process();
  }

  public function preUpdate()
  {
    $invoker = $this->getInvoker();

    if ($this->options['update'] && array_key_exists($this->options['from_field'], $invoker->getFieldsModified()))
    {
      $this->process();
    }
  }

  protected function process()
  {
    $invoker = $this->getInvoker();

    $slug = $proposal = call_user_func($this->options['builder'], $invoker->get($this->options['from_field']));

    if ($this->options['unique'])
    {
      $similarSlugs = array();
      foreach ((array) $invoker->getRepository()->find(array($this->options['slug_field'] => new MongoRegex('/^'.$slug.'/'))) as $document)
      {
        $similarSlugs[] = $document->get($this->options['slug_field']);
      }

      $i = 1;
      while (in_array($slug, $similarSlugs))
      {
        $slug = $proposal.'-'.++$i;
      }
    }

    $invoker->set($this->options['slug_field'], $slug);
  }

  public function findOneBySlugRepositoryProxy($slug)
  {
    return $this->getInvoker()->findOne(array($this->options['slug_field'] => $slug));
  }

  static public function slugify($text)
  {
    // replace all non letters or digits by -
    $text = preg_replace('/\W+/', '-', $text);

    // trim and lowercase
    $text = strtolower(trim($text, '-'));

    return $text;
  }
}
