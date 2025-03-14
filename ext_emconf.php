<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Mittwald Matomo Widget',
    'description' => 'Dashboard widget that displays the visitor data of your Matomo instance',
    'category' => 'be',
    'author' => 'Mittwald CM Service GmbH',
    'author_email' => 'support@mittwald.de',
    'author_company' => 'Mittwald CM Service GmbH',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '4.0.0',
    'uploadfolder' => false,
    'autoload' => [
        'psr-4' => [
            'Mittwald\\MatomoWidget\\' => 'Classes',
        ],
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
