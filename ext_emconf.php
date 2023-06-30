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
    'version' => '3.0.0-dev',
    'uploadfolder' => false,
    'autoload' => [
        'psr-4' => [
            'Mittwald\\MatomoWidget\\' => 'Classes',
        ],
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'php' => '8.1.0-8.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
