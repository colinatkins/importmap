<?php declare(strict_types=1);

/*
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

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