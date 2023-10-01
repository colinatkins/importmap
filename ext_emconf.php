<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Importmap',
    'description' => 'Easily import your JavaScript ES modules without the need of a build system like Webpack',
    'category' => 'fe',
    'state' => 'beta',
    'author' => 'Colin Atkins',
    'author_email' => 'atkins@hey.com',
    'uploadfolder' => '0',
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-12.4.999',
        ],
        'conflicts' => [],
        'suggests' => [
            'setup' => '12.0.0-12.4.999',
        ],
    ],
];