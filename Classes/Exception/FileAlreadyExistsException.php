<?php

declare(strict_types=1);

/*
 * This file is part of the "pluploadbe" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SyntaxOOps\PluploadBE\Exception;

use InvalidArgumentException;
use SyntaxOOps\PluploadBE\Utility\LocalizationUtility;

/**
 * Class FileAlreadyExistsException
 *
 * @author  Haythem Daoud <haythemdaoud.x@gmail.com>
 */
class FileAlreadyExistsException extends InvalidArgumentException
{
    public function __construct(string $path)
    {
        $message = sprintf(LocalizationUtility::translate('exception.fileAlreadyExist'), $path);
        parent::__construct($message, 1604656850);
    }
}
