<?php

$mondongoLibDir = '/apache/mondongo/lib';

// autoloader
require($mondongoLibDir.'/vendor/symfony/src/Symfony/Component/HttpFoundation/UniversalClassLoader.php');

use Symfony\Component\HttpFoundation\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Mondongo\\Tests'            => __DIR__,
    'Mondongo\\Extension\\Extra' => __DIR__.'/../lib',
    'Mondongo'                   => $mondongoLibDir,
    'Model'                      => __DIR__,
));
$loader->register();

// mondator
use \Mondongo\Mondator\Mondator;
use \Mondongo\Mondator\Output\Output;

$configClasses = array(
    'IdentifierAutoIncrement' => array(
        'fields' => array(
            'field' => 'string',
        ),
        'extensions' => array(
            array(
                'class' => 'Mondongo\Extension\Extra\IdentifierAutoIncrement',
            )
        ),
    ),
    'Ipable' => array(
        'fields' => array(
            'field' => 'string',
        ),
        'extensions' => array(
            array(
                'class' => 'Mondongo\Extension\Extra\Ipable',
            )
        ),
    ),
    'Timestampable' => array(
        'fields' => array(
            'field' => 'string'
        ),
        'extensions' => array(
            array(
                'class' => 'Mondongo\Extension\Extra\Timestampable',
            )
        ),
    ),
);

$mondator = new Mondator();
$mondator->setConfigClasses($configClasses);
$mondator->setExtensions(array(
    new Mondongo\Extension\CoreStart(array(
        'default_document_namespace'   => 'Model\Document',
        'default_repository_namespace' => 'Model\Repository',
        'default_document_output'      => __DIR__.'/Model/Document',
        'default_repository_output'    => __DIR__.'/Model/Repository',
    )),
    new Mondongo\Extension\CoreEnd(),
));
$mondator->process();
