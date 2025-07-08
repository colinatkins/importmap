<?php declare(strict_types=1);

/*
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Atkins\Importmap\Service;

use Atkins\Importmap\Middleware\Exceptions\ImportmapFileNotFoundException;

class ImportmapParserService {

    private ?array $definition;
    private string $applicationName = 'application';
    private array $importmap;
    private array $preloadmap;

    public function __construct(
        private readonly FilepathResolverService $filepathResolverService
    )
    {
    }

    public function setImportmapDefinition(array $definition): void
    {
        $this->definition = $definition;
    }

    private function setOrOverrideShims(): void
    {
        if (!isset($this->definition['shims'])) {
            $this->definition['shims'] = [];
        }
        if (!isset($this->definition['shims']['path']) || !is_string($definition['shims']['path'] ?? null)) {
            $this->definition['shims']['path'] = 'EXT:importmap/Resources/Public/JavaScript/shims/es-module-shims-2.6.0.js';
        }

        $this->definition['shims']['path'] = $this->filepathResolverService->sanitizeAndCheckFilePath($this->definition['shims']['path']);
    }

    private function setOrOverrideApplicationName(): void
    {
        if (!isset($this->definition['application'])) {
            $this->definition['application'] = [];
        }
        if (!isset($this->definition['application']['path'])) {
            $this->definition['application']['path'] = NULL;
        }
        if (isset($this->definition['application']['override']) &&
            is_string($this->definition['application']['override'] ?? null)) {
            $this->applicationName = $this->definition['application']['override'];
        }

        // Set application if not predefined
        if (!is_string($this->definition['application']['path'] ?? null)) {
            $this->definition['application']['path'] = 'EXT:importmap/Resources/Public/JavaScript/application.js';
        }

        // Always preload
        $this->definition['application']['preload'] = '1';
    }

    /**
     * @throws ImportmapFileNotFoundException
     */
    public function parse(): ImportmapParserService
    {
        $this->setOrOverrideShims();
        $this->setOrOverrideApplicationName();

        $this->importmap = [];

        foreach ($this->definition as $moduleName => $moduleConf) {
            if ($moduleName == 'shims') continue;
            if (is_string($moduleConf['override'] ?? null))
                $moduleName = $moduleConf['override'];
            if (!is_array($moduleConf ?? null) && !is_string($moduleConf['path'] ?? null))
                continue;
            $this->importmap[$moduleName] = $this->filepathResolverService->sanitizeAndCheckFilePath($moduleConf['path']);
        }

        $this->preloadmap = [];

        foreach ($this->importmap as $module) {
            if ((bool)($module['preload'] ?? false) && is_string($module['path'] ?? null))
                $this->preloadmap[] = $this->filepathResolverService->sanitizeAndCheckFilePath($module['path']);
        }
        
        return $this;
    }

    public function getImportmap(): array
    {
        return $this->importmap;
    }

    public function getPreloadmap(): array
    {
        return $this->preloadmap;
    }

    public function getShims(): string
    {
        return $this->definition['shims']['path'];
    }

    public function getApplicationName(): string
    {
        return $this->applicationName;
    }
}