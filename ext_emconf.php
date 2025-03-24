<?php


$EM_CONF[$_EXTKEY] = array(
	'title' => 'Toumoro Extended links',
	'description' => 'Réecriture de l\'extension ml_links en extbase, pour fonctionner sur la version 8.7 de TYPO3.',
	'category' => 'fe',
	'version' => '11.5.2',
	'state' => 'stable',
	'author' => 'Toumoro',
	'author_email' => '',
	'author_company' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '11.5.0-11.5.99',
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
	'autoload' => array(
		'psr-4' => array('Toumoro\\TmMlLinks\\' => 'Classes')
	),
);

