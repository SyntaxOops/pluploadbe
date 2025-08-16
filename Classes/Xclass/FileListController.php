<?php

declare(strict_types=1);

/*
 * This file is part of the "pluploadbe" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SyntaxOOps\PluploadBE\Xclass;

use Psr\Http\Message\ServerRequestInterface;
use SyntaxOOps\PluploadBE\Utility\LocalizationUtility;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Filelist\Controller\FileListController as BaseFileListController;

/**
 * Class FileListController
 *
 * @author  Haythem Daoud <haythemdaoud.x@gmail.com>
 */
class FileListController extends BaseFileListController
{
    /**
     * @inheritDoc
     * @throws RouteNotFoundException
     */
    protected function registerAdditionalDocHeaderButtons(ServerRequestInterface $request): void
    {
        parent::registerAdditionalDocHeaderButtons($request);

        // Upload button (only if upload to this directory is allowed)
        if (!(
            $this->folderObject && $this->folderObject->getStorage()->checkUserActionPermission('add', 'File')
            && $this->folderObject->checkActionPermission('write')
        )) {
            return;
        }

        $buttonBar = $this->view->getDocHeaderComponent()->getButtonBar();

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $uploadButton = $buttonBar->makeLinkButton()
            ->setHref((string)$uriBuilder->buildUriFromRoute(
                'Plupload_BE',
                [
                    'id' => $this->folderObject->getCombinedIdentifier(),
                    'returnUrl' => $this->filelist->createModuleUri(),
                ]
            ))
            ->setClasses('uploaded')
            ->setShowLabelText(true)
            ->setTitle(LocalizationUtility::translate('upload.title'))
            ->setIcon($this->iconFactory->getIcon('actions-upload', Icon::SIZE_SMALL));

        $buttonBar->addButton($uploadButton, ButtonBar::BUTTON_POSITION_LEFT, 2);
    }
}
