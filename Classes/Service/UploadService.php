<?php

declare(strict_types=1);

/*
 * This file is part of the "pluploadbe" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SyntaxOOps\PluploadBE\Service;

use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\ExtensionFileException;
use SyntaxOOps\PluploadBE\Exception\FileAlreadyExistsException;
use SyntaxOOps\PluploadBE\Utility\ConfigurationUtility;
use SyntaxOOps\PluploadBE\Utility\ImageAutoresizeUtility;
use SyntaxOOps\PluploadBE\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\Driver\LocalDriver;
use TYPO3\CMS\Core\Resource\Exception\InvalidFileNameException;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\Security\FileNameValidator;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class UploadService
 *
 * @author  Haythem Daoud <haythemdaoud.x@gmail.com>
 */
class UploadService
{
    protected ?Folder $folderObject = null;
    protected string $fileName;
    protected string $uploadPath;

    /**
     * @var array{
     *     file: array{chunkSize: int, maxSize: int, allowedExtensions: string, excludedExtensions: string},
     *     image: array{autoresizeMode: int, width: int, height: int, quality: int}
     * }
     */
    private array $configuration;

    /**
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     */
    public function __construct()
    {
        $this->configuration = ConfigurationUtility::getExtensionConfiguration();
    }

    /**
     * @param string $combinedIdentifier
     * @param string $file
     * @throws AccessDeniedException
     * @throws ExtensionFileException
     * @throws FileAlreadyExistsException
     * @throws InvalidFileNameException
     */
    public function process(string $combinedIdentifier, string $file, int $chunk = 0, int $chunks = 0): void
    {
        /** @var ResourceFactory $resourceFactory */
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

        if ($combinedIdentifier === '') {
            throw new \InvalidArgumentException('A folder identifier is required.', 1755446663);
        }

        $this->folderObject = $resourceFactory->getFolderObjectFromCombinedIdentifier($combinedIdentifier);

        $this->uploadPath = $this->getUploadPath();
        $this->setFileName($file);
        $this->checkRequired();
        $this->uploadFile($chunk, $chunks);
    }

    /**
     * @return string
     */
    protected function getUploadPath(): string
    {
        $folderObject = $this->folderObject;
        if (!$folderObject instanceof Folder) {
            throw new \InvalidArgumentException('The upload folder has not been initialized.', 1755446664);
        }

        $basePath = $folderObject->getStorage()->getConfiguration()['basePath'];
        $fullPath = Environment::getPublicPath() . '/' . $basePath . $folderObject->getReadablePath();
        $fullPath = str_replace('//', '/', $fullPath);

        return rtrim($fullPath, '/');
    }

    /**
     * @return bool
     */
    public function checkAccess(): bool
    {
        return $this->folderObject
            && $this->folderObject->getStorage()->checkUserActionPermission('add', 'File')
            && $this->folderObject->checkActionPermission('write');
    }

    /**
     * @return bool
     * @throws ExtensionFileException
     */
    protected function checkExtension(): bool
    {
        $ext = pathinfo($this->getFileName(), PATHINFO_EXTENSION);
        $message = sprintf(LocalizationUtility::translate('exception.extension'), $ext);

        $allowedExtensions = $this->configuration['file']['allowedExtensions'];

        /** @var FileNameValidator $fileValidator */
        $fileValidator = GeneralUtility::makeInstance(FileNameValidator::class);

        if (!$fileValidator->isValid($this->getFileName())) {
            throw new ExtensionFileException($message, 1604596435);
        }

        if (empty($allowedExtensions)) {
            return true;
        }

        if (empty($ext) || !in_array($ext, explode(',', $allowedExtensions))) {
            throw new ExtensionFileException($message, 1604596436);
        }

        return true;
    }

    /**
     * @return bool
     * @throws AccessDeniedException
     */
    protected function checkRequired(): bool
    {
        $folderObject = $this->folderObject;
        if (!$folderObject instanceof Folder || !$this->checkAccess()) {
            $path = $folderObject?->getReadablePath() ?? '';
            $message = sprintf(LocalizationUtility::translate('exception.accessDenied'), $path);
            throw new AccessDeniedException($message);
        }

        return $this->checkExtension();
    }

    /**
     * @param string $fileName
     * @throws InvalidFileNameException
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = GeneralUtility::makeInstance(LocalDriver::class)
            ->sanitizeFileName(ltrim($fileName, '/'));
    }

    /**
     * @return string
     */
    protected function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Handles file upload.
     *
     * Copyright 2013, Moxiecode Systems AB
     * Released under GPL License.
     *
     * License: http://www.plupload.com/license
     * Contributing: http://www.plupload.com/contributing
     */
    protected function uploadFile(int $chunk, int $chunks): void
    {
        // Clean the fileName for security reasons
        $filePath = $this->uploadPath . DIRECTORY_SEPARATOR . $this->getFileName();

        // Do not override file
        if (file_exists($filePath)) {
            throw new FileAlreadyExistsException($this->getFileName());
        }

        $fileExist = file_exists("{$filePath}.part");

        // Open temp file
        $out = @fopen("{$filePath}.part", $chunks && $fileExist ? 'ab' : 'wb+');
        if (!$out) {
            throw new \InvalidArgumentException('Failed to open output stream.', 102);
        }

        if (!empty($_FILES)) {
            if ($_FILES['file']['error'] || !is_uploaded_file($_FILES['file']['tmp_name'])) {
                throw new \InvalidArgumentException('Failed to move uploaded file.', 103);
            }

            // Read binary input stream and append it to temp file
            $in = @fopen($_FILES['file']['tmp_name'], 'rb');
        } else {
            $in = @fopen('php://input', 'rb');
        }

        if (!$in) {
            throw new \InvalidArgumentException('Failed to open input stream.', 101);
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        // Check if the file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off
            rename($filePath . '.part', $filePath);
            $this->processFile($filePath);
        }
    }

    /**
     * @param string $filePath
     */
    protected function processFile(string $filePath): void
    {
        GeneralUtility::fixPermissions($filePath);

        if (!class_exists('\\Causal\\ImageAutoresize\\Service\\ImageResizer')) {
            return;
        }

        if ($this->configuration['image']['autoresizeMode'] == 2) {
            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
            $allowedExtensions = ImageAutoresizeUtility::getExtensions();
            if (in_array($ext, $allowedExtensions, true)) {
                $imageResizer = GeneralUtility::makeInstance(\Causal\ImageAutoresize\Service\ImageResizer::class);
                $backendUser = GeneralUtility::makeInstance(BackendUserAuthentication::class);

                $imageResizer->processFile(
                    $filePath,
                    '',
                    '',
                    null,
                    $backendUser,
                    'notify'
                );
            }
        }
    }
}
