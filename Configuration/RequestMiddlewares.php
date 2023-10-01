<?php declare(strict_types=1);


return [
    'frontend' => [
        'importmap-generator' => [
            'target' => \Atkins\Importmap\Middleware\ImportmapGenerator::class,
            'after' => [
                'typo3/cms-frontend/tsfe',
            ],
        ],
    ],
];