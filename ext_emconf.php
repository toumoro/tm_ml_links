<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Toumoro Extended links',
    'description' => 'Réecriture de l\'extension ml_links en extbase, pour fonctionner sur la version 8.7 de TYPO3.',
    'category' => 'plugin',
    'author' => 'Toumoro',
    'author_email' => '',
    'state' => 'stable',
    'version' => '12.4.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    /*'autoload' => [
        'psr-4' => array('Toumoro\\TmMlLinks\\' => 'Classes')
    ],*/
];

