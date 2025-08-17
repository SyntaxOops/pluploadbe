<?php

declare(strict_types=1);

/*
 * This file is part of the "pluploadbe" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SyntaxOOps\PluploadBE\Service;

use SyntaxOOps\PluploadBE\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * Class AssetLoaderService
 *
 * @author  Haythem Daoud <haythemdaoud.x@gmail.com>
 */
class AssetLoaderService
{
    /**
     * @param AssetCollector $assetCollector
     * @param PageRenderer $pageRenderer
     */
    public function __construct(
        protected AssetCollector $assetCollector,
        protected PageRenderer $pageRenderer
    ) {}

    /**
     * @param string $langCode
     */
    public function load(string $langCode): void
    {
        // @extensionScannerIgnoreLine
        $this->assetCollector->addStyleSheet(
            'plupload',
            'EXT:pluploadbe/Resources/Public/JavaScript/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css'
        );

        $this->assetCollector->addJavaScript(
            'jquery',
            'EXT:pluploadbe/Resources/Public/JavaScript/jquery/jquery-3.7.1.min.js'
        );

        $this->assetCollector->addJavaScript(
            'plupload-full',
            'EXT:pluploadbe/Resources/Public/JavaScript/plupload/js/plupload.full.min.js'
        );

        $this->assetCollector->addJavaScript(
            'jquery-plupload-queue',
            'EXT:pluploadbe/Resources/Public/JavaScript/plupload/js/jquery.plupload.queue/jquery.plupload.queue.min.js'
        );

        $this->assetCollector->addJavaScript(
            'plupload-i18n-' . $langCode,
            'EXT:pluploadbe/Resources/Public/JavaScript/plupload/js/i18n/' . $langCode . '.js'
        );

        $this->assetCollector->addJavaScript(
            'notify',
            'EXT:pluploadbe/Resources/Public/JavaScript/notifyjs/notify.min.js'
        );

        $this->pageRenderer->addInlineLanguageLabel(
            'fileUploaded',
            LocalizationUtility::translate('upload.file.success')
        );

        $this->assetCollector->addJavaScript(
            'plupload-main',
            'EXT:pluploadbe/Resources/Public/JavaScript/main.js'
        );
    }
}
