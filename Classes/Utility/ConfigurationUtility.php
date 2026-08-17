<?php

declare(strict_types=1);

/*
 * This file is part of the "pluploadbe" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SyntaxOOps\PluploadBE\Utility;

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ConfigurationUtility
 *
 * @author  Haythem Daoud <haythemdaoud.x@gmail.com>
 */
class ConfigurationUtility
{
    private const EXTENSION_NAME = 'pluploadbe';

    /**
     * @return array{
     *     file: array{chunkSize: int, maxSize: int, allowedExtensions: string, excludedExtensions: string},
     *     image: array{autoresizeMode: int, width: int, height: int, quality: int}
     * }
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     */
    public static function getExtensionConfiguration(): array
    {
        $configuration = (array)GeneralUtility::makeInstance(ExtensionConfiguration::class)->get(self::EXTENSION_NAME);
        $fileConfiguration = (array)($configuration['file'] ?? []);
        $imageConfiguration = (array)($configuration['image'] ?? []);

        return [
            'file' => [
                'chunkSize' => (int)($fileConfiguration['chunkSize'] ?? 0),
                'maxSize' => (int)($fileConfiguration['maxSize'] ?? 0),
                'allowedExtensions' => (string)($fileConfiguration['allowedExtensions'] ?? ''),
                'excludedExtensions' => (string)($fileConfiguration['excludedExtensions'] ?? ''),
            ],
            'image' => [
                'autoresizeMode' => (int)($imageConfiguration['autoresizeMode'] ?? 0),
                'width' => (int)($imageConfiguration['width'] ?? 0),
                'height' => (int)($imageConfiguration['height'] ?? 0),
                'quality' => (int)($imageConfiguration['quality'] ?? 0),
            ],
        ];
    }
}
