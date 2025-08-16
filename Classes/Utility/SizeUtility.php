<?php

declare(strict_types=1);

/*
 * This file is part of the "pluploadbe" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SyntaxOOps\PluploadBE\Utility;

/**
 * Class SizeUtility
 *
 * @author  Haythem Daoud <haythemdaoud.x@gmail.com>
 */
class SizeUtility
{
    /**
     * @param string $input
     * @return int
     */
    public static function getBytes(string $input): int
    {
        // Extract the unit (e.g., KB, MB, GB)
        $unit = strtolower(preg_replace('/[^a-zA-Z]/', '', $input));

        // Extract the numeric value
        $value = (float)preg_replace('/[^0-9\.]/', '', $input);

        switch ($unit) {
            case 'p':    // Petabyte
            case 'pb':
                $value *= 1024;
                // no break
            case 't':    // Terabyte
            case 'tb':
                $value *= 1024;
                // no break
            case 'g':    // Gigabyte
            case 'gb':
                $value *= 1024;
                // no break
            case 'm':    // Megabyte
            case 'mb':
                $value *= 1024;
                // no break
            case 'k':    // Kilobyte
            case 'kb':
                $value *= 1024;
                // no break
            case 'b':    // Byte
                return (int)$value;
        }

        return (int)$value;
    }
}
