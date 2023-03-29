<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Mittwald Matomo Widget',
    'description' => 'Dashboard widget that displays the visitor data of your Matomo instance',
    'category' => 'be',
    'author' => 'Mittwald CM Service GmbH',
    'author_email' => 'support@mittwald.de',
    'author_company' => 'Mittwald CM Service GmbH',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '2.0.1',
    'uploadfolder' => false,
    'autoload' => [
        'psr-4' => [
            'Mittwald\\MatomoWidget\\' => 'Classes',
        ],
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
            'php' => '7.4.0-8.1.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
