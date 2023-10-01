<?php declare(strict_types=1);

namespace Atkins\Importmap\Middleware;

use Atkins\Importmap\Middleware\Exceptions\ImportmapFileNotFoundException;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Resource\FilePathSanitizer;

class ImportmapGenerator implements \Psr\Http\Server\MiddlewareInterface
{
    public function __construct(
    ) { }

    private function skipBeforeFilter($pageSetup): bool
    {
        return !is_array($pageSetup['importmap.'] ?? null);
    }

    public function process(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface
    {
        $controller = $request->getAttribute('frontend.controller');
        if ($this->skipBeforeFilter($controller->pSetup)) return $handler->handle($request);
        $controller->pSetup = $this->setOrOverrideShims($controller->pSetup);
        $controller->pSetup = $this->setOrOverrideApplicationName($controller->pSetup);

        $importmap = $this->parseImportmapTypoScript($controller->pSetup['importmap.']);
        $preloadmap = $this->parsePreloadTypoScript($controller->pSetup['importmap.']);

        $this->getPageRenderer()->addHeaderData(
            "\n"
            . implode("\n", [
                $this->constructImportmapTag($importmap),
                $this->constructModulePreloadTags($preloadmap),
                $this->constructShimsTag($controller->pSetup['importmap.']['shims']),
                $this->constructModuleTag('application')
            ])
            . "\n");

        return $handler->handle($request);
    }

    private function setOrOverrideShims($pageSetup): array
    {
        // Set shims if not set beforehand
        if (!is_string($pageSetup['importmap.']['shims'] ?? null))
            $pageSetup['importmap.']['shims'] = 'EXT:importmap/Resources/Public/JavaScript/shims/es-module-shims-1.5.1.js';

        return $pageSetup;
    }

    private function setOrOverrideApplicationName($pageSetup): array
    {
        // Set application if not predefined
        if (!is_array($pageSetup['importmap.']['application.'] ?? null) &&
            !is_string($pageSetup['importmap.']['application.']['path.'] ?? null))
            $pageSetup['importmap.']['application.']['path'] = 'EXT:importmap/Resources/Public/JavaScript/application.js';

        // Always preload the application
        $pageSetup['importmap.']['application.']['preload'] = '1';

        return $pageSetup;
    }

    /**
     * @throws ImportmapFileNotFoundException
     */
    private function parseImportmapTypoScript($importmapConf): array
    {
        $importmap = [];

        foreach (GeneralUtility::removeDotsFromTS($importmapConf) as $moduleName => $moduleConf) {
            if (is_string($moduleConf['overrideModuleName'] ?? null))
                $moduleName = $moduleConf['overrideModuleName'];
            if (!is_array($moduleConf ?? null) && !is_string($moduleConf['path'] ?? null))
                continue;
            $importmap[$moduleName] = $this->sanitizeAndCheckFilePath($moduleConf['path']);
        }

        return $importmap;
    }

    /**
     * @throws ImportmapFileNotFoundException
     */
    private function parsePreloadTypoScript($importmapConf): array
    {
        $preloads = [];


        foreach (GeneralUtility::removeDotsFromTS($importmapConf) as $module) {
            if ((bool)($module['preload'] ?? false) && is_string($module['path'] ?? null))
                $preloads[] = $this->sanitizeAndCheckFilePath($module['path']);
        }

        return $preloads;
    }

    private function constructImportmapTag(array $importmap): string
    {
        return '<script type="importmap">' . json_encode(['imports' => $importmap], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    private function constructModulePreloadTags(array $preloadmap): string
    {
        return implode("\n", array_map(function($path) {
            return '<link rel="modulepreload" href="' . $path . '">';
        }, $preloadmap));
    }

    private function constructModuleTag(string $applicationModuleName): string
    {
        return '<script type="module">import "' . $applicationModuleName . '"</script>';
    }

    private function constructShimsTag(string $shimsPath): string
    {
        return '<script src="' . $this->sanitizeAndCheckFilePath($shimsPath) . '" async="async"></script>';
    }

    protected function getPageRenderer(): PageRenderer
    {
        return GeneralUtility::makeInstance(PageRenderer::class);
    }

    private function sanitizeAndCheckFilePath($origPath): string
    {
        try {
            // Sanitize filePath first
            $sanitizedPath = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($origPath);
            // Check files existence
            if (!file_exists(GeneralUtility::getFileAbsFileName($origPath)))
                throw new FileDoesNotExistException();
        } catch (FileDoesNotExistException $e) {
            throw new ImportmapFileNotFoundException('A file specified in the importmap was not found. '
                . 'Double check that the specified path is correct. '
                . "The tried path was: \n"
                . $origPath);
        }

        return $sanitizedPath;
    }
}