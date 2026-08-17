<?php

declare(strict_types=1);

/*
 * This file is part of the "pluploadbe" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SyntaxOOps\PluploadBE\EventListener;

use Psr\Http\Message\ServerRequestInterface;
use SyntaxOOps\PluploadBE\Utility\LocalizationUtility;
use TYPO3\CMS\Backend\Module\ModuleInterface;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;
use TYPO3\CMS\Backend\Template\Components\ModifyButtonBarEvent;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class AddUploadButtonToFileListEventListener
{
    public function __construct(
        private readonly UriBuilder $uriBuilder,
        private readonly IconFactory $iconFactory,
        private readonly ResourceFactory $resourceFactory,
    ) {}

    /**
     * @throws RouteNotFoundException
     */
    public function __invoke(ModifyButtonBarEvent $event): void
    {
        $request = $this->getRequest($event);
        if (!$request instanceof ServerRequestInterface) {
            return;
        }

        $module = $request->getAttribute('module');
        if (!$module instanceof ModuleInterface || $module->getIdentifier() !== 'media_management') {
            return;
        }

        $parsedBody = (array)$request->getParsedBody();
        $combinedFolderIdentifier = (string)($parsedBody['id'] ?? $request->getQueryParams()['id'] ?? '');
        if ($combinedFolderIdentifier === '') {
            return;
        }

        try {
            $folder = $this->resourceFactory->getFolderObjectFromCombinedIdentifier($combinedFolderIdentifier);
        } catch (ResourceDoesNotExistException) {
            return;
        }

        if (!$folder->getStorage()->checkUserActionPermission('add', 'File')
            || !$folder->checkActionPermission('write')
        ) {
            return;
        }

        $requestUri = $request->getUri();
        $returnUrl = $requestUri->getPath();
        if ($requestUri->getQuery() !== '') {
            $returnUrl .= '?' . $requestUri->getQuery();
        }

        $uploadButton = GeneralUtility::makeInstance(LinkButton::class)
            ->setHref((string)$this->uriBuilder->buildUriFromRoute(
                'Plupload_BE',
                [
                    'id' => $folder->getCombinedIdentifier(),
                    'returnUrl' => $returnUrl,
                ]
            ))
            ->setClasses('uploaded')
            ->setShowLabelText(true)
            ->setTitle(LocalizationUtility::translate('upload.title'))
            ->setIcon($this->iconFactory->getIcon('actions-upload', IconSize::SMALL));

        $buttons = $event->getButtons();
        $buttons[ButtonBar::BUTTON_POSITION_LEFT][2][] = $uploadButton;
        $event->setButtons($buttons);
    }

    private function getRequest(ModifyButtonBarEvent $event): ?ServerRequestInterface
    {
        // ModifyButtonBarEvent::getRequest() was added in TYPO3 14.
        // @phpstan-ignore-next-line TYPO3 13 does not provide this method.
        if (method_exists($event, 'getRequest')) {
            return $event->getRequest();
        }

        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;

        return $request instanceof ServerRequestInterface ? $request : null;
    }
}
