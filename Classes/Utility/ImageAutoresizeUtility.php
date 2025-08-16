<?php

declare(strict_types=1);

/*
 * This file is part of the "pluploadbe" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SyntaxOOps\PluploadBE\Utility;

use Causal\ImageAutoresize\Controller\ConfigurationController;

/**
 * Class ImageAutoresizeUtility
 *
 * @author  Haythem Daoud <haythemdaoud.x@gmail.com>
 */
class ImageAutoresizeUtility
{
    protected static string $default = 'jpg,jpeg,png,ai,bmp,pcx,tga,tif,tiff';

    /**
     * @return array
     */
    public static function getExtensions(): array
    {
        if (!class_exists('\\Causal\\ImageAutoresize\\Controller\\ConfigurationController')) {
            return explode(',', self::$default);
        }

        try {
            $conf = ConfigurationController::readConfiguration();

            $extensions = isset($conf['file_types']) ? explode(',', $conf['file_types']) : [];

            if (isset($conf['conversion_mapping'])) {
                foreach (explode(',', $conf['conversion_mapping']) as $item) {
                    $extensions[] = trim(explode('=', $item)[0]);
                }
            }
        } catch (\Exception $e) {
            return explode(',', self::$default);
        }

        return $extensions;
    }
}
