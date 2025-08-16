<?php

declare(strict_types=1);

/*
 * This file is part of the "pluploadbe" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SyntaxOOps\PluploadBE\Utility;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility as BaseLocalizationUtility;

/**
 * Class LocalizationUtility
 *
 * @author  Haythem Daoud <haythemdaoud.x@gmail.com>
 */
class LocalizationUtility
{
    /**
     * @param string $key
     * @return string
     */
    public static function translate(string $key): string
    {
        return (string)BaseLocalizationUtility::translate($key, 'pluploadbe');
    }
}
