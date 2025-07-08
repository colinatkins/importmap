<?php declare(strict_types=1);

/*
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Atkins\Importmap\Service;

use RuntimeException;
use TYPO3\CMS\Core\Site\Entity\Site;

class SettingsReaderService {
    public function __construct(
    )
    {
    }

    public function getImportmapDefinition(Site $site): array
    {
        if (!array_key_exists('importmap', $site->getConfiguration()['settings']['page'])) {
            throw new \RuntimeException('Importmap key was not found in site settings. Probably your forgot to define your importmap inside settings.yaml');
        }

        return $site->getConfiguration()['settings']['page']['importmap'];
    }
}