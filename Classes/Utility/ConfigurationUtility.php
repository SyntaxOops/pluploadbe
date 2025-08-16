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
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     */
    public static function getExtensionConfiguration(): array
    {
        return (array)GeneralUtility::makeInstance(ExtensionConfiguration::class)->get(self::EXTENSION_NAME);
    }
}
