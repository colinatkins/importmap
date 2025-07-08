<?php declare(strict_types=1);

/*
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Atkins\Importmap\Service;

use Atkins\Importmap\Middleware\Exceptions\ImportmapFileNotFoundException;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Resource\FilePathSanitizer;

class FilepathResolverService {
    public function __construct(
        private readonly FilePathSanitizer $filePathSanitizer
    )
    {
    }

    public function sanitizeAndCheckFilePath($origPath): string
    {
        try {
            // Sanitize filePath first
            $sanitizedPath = $this->filePathSanitizer->sanitize($origPath);
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