<?php declare(strict_types=1);

/*
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Atkins\Importmap\Middleware;

use Atkins\Importmap\Service\ImportmapParserService;
use Atkins\Importmap\Service\SettingsReaderService;
use RuntimeException;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImportmapGenerator implements \Psr\Http\Server\MiddlewareInterface
{
    public function __construct(
        private readonly SettingsReaderService $settingsReaderService,
        private readonly ImportmapParserService $importmapParserService
    ) { }

    public function process(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface
    {
        $this->importmapParserService->setImportmapDefinition(
            $this->settingsReaderService->getImportmapDefinition(
                $request->getAttribute('site')
            )
        );
        try {
        } catch (\RuntimeException $e) {
            return $handler->handle($request);
        }
        
        $importmap = $this->importmapParserService->parse()->getImportmap();
        $preloadmap = $this->importmapParserService->getPreloadmap();

        $this->getPageRenderer()->addHeaderData(
            "\n"
            . implode("\n", [
                $this->constructImportmapTag($importmap),
                $this->constructModulePreloadTags($preloadmap),
                $this->constructShimsTag($this->importmapParserService->getShims()),
                $this->constructModuleTag($this->importmapParserService->getApplicationName())
            ])
            . "\n");

        return $handler->handle($request);
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
        return '<script src="' . $shimsPath . '" async="async"></script>';
    }

    protected function getPageRenderer(): PageRenderer
    {
        return GeneralUtility::makeInstance(PageRenderer::class);
    }
}