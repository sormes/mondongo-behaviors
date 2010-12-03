<?php

$mondongoLibDir = __DIR__.'/../../mondongo/lib';

// autoloader
require($mondongoLibDir.'/vendor/symfony/src/Symfony/Component/HttpFoundation/UniversalClassLoader.php');

use Symfony\Component\HttpFoundation\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Mondongo\Tests'    => __DIR__,
    'Mondongo\Behavior' => __DIR__.'/../lib',
    'Mondongo'          => $mondongoLibDir,
    'Model'             => __DIR__,
));
$loader->register();

// mondator
use \Mondongo\Mondator\Mondator;
use \Mondongo\Mondator\Output\Output;

$configClasses = array(
    'Model\IdentifierAutoIncrement' => array(
        'fields' => array(
            'field' => 'string',
        ),
        'behaviors' => array(
            array(
                'class' => 'Mondongo\Behavior\IdentifierAutoIncrement',
            )
        ),
    ),
    'Model\Ipable' => array(
        'fields' => array(
            'field' => 'string',
        ),
        'behaviors' => array(
            array(
                'class' => 'Mondongo\Behavior\Ipable',
            )
        ),
    ),
    'Model\Sluggable' => array(
        'fields' => array(
            'title' => 'string',
        ),
        'behaviors' => array(
            array(
                'class'   => 'Mondongo\Behavior\Sluggable',
                'options' => array(
                    'from_field' => 'title',
                ),
            )
        ),
    ),
    'Model\Timestampable' => array(
        'fields' => array(
            'field' => 'string'
        ),
        'behaviors' => array(
            array(
                'class' => 'Mondongo\Behavior\Timestampable',
            )
        ),
    ),
    'Model\TranslationDocument' => array(
        'fields' => array(
            'title'     => 'string',
            'body'      => 'string',
            'date'      => 'date',
            'is_active' => 'boolean',
        ),
        'behaviors' => array(
            array(
                'class'   => 'Mondongo\Behavior\Translation',
                'options' => array(
                    'fields' => array('title', 'body')
                ),
            ),
        ),
    ),
    'Model\Cacheable' => array(
        'fields' => array(
            'field' => 'string'
        ),
        'behaviors' => array(
            array(
                'class' => 'Mondongo\Behavior\Cacheable',
                'options' => array(
                    'from_fields' => array(
                        array(
                            'name' => 'field',
                            'function' => 'cacheable_function'
                        )
                    )
                 )
            )
        )
    )
);

$mondator = new Mondator();
$mondator->setConfigClasses($configClasses);
$mondator->setExtensions(array(
    new Mondongo\Extension\Core(array(
        'default_output' => __DIR__.'/Model',
    )),
    new Mondongo\Extension\DocumentFromToArray(),
));
$mondator->process();
