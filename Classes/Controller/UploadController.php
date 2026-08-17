<?php

declare(strict_types=1);

/*
 * This file is part of the "pluploadbe" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SyntaxOOps\PluploadBE\Controller;

use Psr\Http\Message\ResponseInterface;
use SyntaxOOps\PluploadBE\Service\AssetLoaderService;
use SyntaxOOps\PluploadBE\Utility\ConfigurationUtility;
use SyntaxOOps\PluploadBE\Utility\LocalizationUtility;
use SyntaxOOps\PluploadBE\Utility\SizeUtility;
use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * Class UploadController
 *
 * @author  Haythem Daoud <haythemdaoud.x@gmail.com>
 */
class UploadController extends ActionController
{
    protected ModuleTemplate $moduleTemplate;
    protected string $returnUrl = '';

    /**
     * @param Context $context
     * @param ModuleTemplateFactory $moduleTemplateFactory
     * @param IconFactory $iconFactory
     * @param AssetLoaderService $assetLoaderService
     * @param AssetCollector $assetCollector
     */
    public function __construct(
        private readonly Context $context,
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        protected readonly IconFactory $iconFactory,
        protected AssetLoaderService $assetLoaderService,
        protected AssetCollector $assetCollector
    ) {}

    public function initializeAction(): void
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->setReturnUrl();
        $this->setDocHeader();
        parent::initializeAction();
    }

    protected function initializeView(): void
    {
        $langCode = $GLOBALS['LANG']->getLocale()->getLanguageCode();
        $this->assetLoaderService->load($langCode);
    }

    protected function setDocHeader(): void
    {
        if ($this->returnUrl) {
            $backButton = GeneralUtility::makeInstance(LinkButton::class)
                ->setHref($this->returnUrl)
                ->setTitle(LocalizationUtility::translate('upload.back'))
                ->setShowLabelText(true)
                ->setIcon($this->iconFactory->getIcon('actions-caret-left', IconSize::SMALL));
            $this->moduleTemplate->getDocHeaderComponent()->getButtonBar()->addButton($backButton);
        }
    }

    /**
     * @return ResponseInterface
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws AspectNotFoundException
     */
    public function indexAction(): ResponseInterface
    {
        $request = $this->request;
        $timestamp = $this->context->getPropertyFromAspect('date', 'timestamp');

        $configuration = ConfigurationUtility::getExtensionConfiguration();

        $maxSize = $configuration['file']['maxSize'];
        if (empty($maxSize)) {
            $maxSize = SizeUtility::getBytes((string)ini_get('upload_max_filesize'));
        }

        $chunkSize = empty($configuration['file']['chunkSize']) ? $maxSize : (int)$configuration['file']['chunkSize'];
        $chunkSize = min($chunkSize, $maxSize);

        // Image processing
        $resizeEnabled = $configuration['image']['autoresizeMode'] == 1;
        unset($configuration['image']['autoresizeMode']);

        $parsedBody = (array)$request->getParsedBody();
        $storageId = (string)($parsedBody['id'] ?? $request->getQueryParams()['id'] ?? '');

        $chunkSize = empty($chunkSize) ? $maxSize : $chunkSize;
        $maxFileSize = round($maxSize / (1024 * 1024), 2);

        $this->moduleTemplate->assignMultiple([
            'uid' => $timestamp,
            'maxFileSizeMB' => $maxFileSize,
            'allowedExtensions' => $configuration['file']['allowedExtensions'],
            'excludedExtensions' => $configuration['file']['excludedExtensions'],
        ]);

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uriBuilder->reset()->setRequest($this->request);
        $uploadUrl = $uriBuilder->uriFor(
            'upload',
            ['id' => $storageId],
            'UploadAjax'
        );

        $pluploadSettings = [
            'settings' => [
                'uid' => $timestamp,
                'chunkSize' => $chunkSize,
                'maxFileSize' => $maxSize,
                'allowedExtensions' => $configuration['file']['allowedExtensions'],
                'excludedExtensions' => $configuration['file']['excludedExtensions'],
                'resizeEnabled' => $resizeEnabled,
                'resize' => $configuration['image'],
                'uploadUrl' => $uploadUrl,
                'error' => LocalizationUtility::translate('upload.error'),
                'success' => LocalizationUtility::translate('upload.success'),
            ],
        ];

        $this->assetCollector->addInlineJavaScript(
            'plupload-settings',
            'var Plupload_BE = ' . json_encode($pluploadSettings, JSON_FORCE_OBJECT) . ';',
            [],
            ['priority' => true, 'csp' => true]
        );

        return $this->moduleTemplate->renderResponse('Upload/Index');
    }

    private function setReturnUrl(): void
    {
        $parsedBody = (array)$this->request->getParsedBody();
        $this->returnUrl = (string)($parsedBody['returnUrl']
            ?? $this->request->getQueryParams()['returnUrl']
            ?? '');
    }
}
