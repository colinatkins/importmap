<?php

/*
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Importmap',
    'description' => 'Easily import your JavaScript ES modules without the need of a build system like Webpack',
    'category' => 'fe',
    'state' => 'beta',
    'author' => 'Colin Atkins',
    'author_email' => 'atkins@hey.com',
    'uploadfolder' => '0',
    'version' => '0.1.5',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.999',
        ],
        'conflicts' => [],
        'suggests' => [
            'setup' => '13.4.0-13.4.999',
        ],
    ],
];