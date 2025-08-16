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
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\ExtensionFileException;
use SyntaxOOps\PluploadBE\Exception\FileAlreadyExistsException;
use SyntaxOOps\PluploadBE\Service\UploadService;
use SyntaxOOps\PluploadBE\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;

/**
 * Class UploadController
 *
 * @author  Haythem Daoud <haythemdaoud.x@gmail.com>
 */
class UploadAjaxController extends ActionController
{
    protected ?string $defaultViewObjectName = JsonView::class;

    /**
     * @return ResponseInterface
     */
    public function uploadAction(): ResponseInterface
    {
        /** @var UploadService $uploadService */
        $uploadService = GeneralUtility::makeInstance(UploadService::class);

        $result = [
            'jsonrpc' => '2.0',
            'result' => null,
            'id' => 'id',
        ];

        $combinedFolderIdentifier = (string)($this->request->getParsedBody()['id'] ?? $this->request->getQueryParams()['id'] ?? null);
        $fileName = (string)($this->request->getParsedBody()['name'] ?? $this->request->getQueryParams()['name'] ?? '');

        $responseCode = 200;

        try {
            $uploadService->process($combinedFolderIdentifier, $fileName);
        } catch (ExtensionFileException|FileAlreadyExistsException $e) {
            $responseCode = 410;
            $result['error'] = [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        } catch (AccessDeniedException $e) {
            $responseCode = 403;
            $result['error'] = [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            $responseCode = 500;

            $result['error'] = [
                'code' => $e->getCode(),
                'message' => sprintf(LocalizationUtility::translate('exception.undefined'), $fileName),
            ];
        }

        $this->view->assign('value', $result);

        $response = $this->responseFactory->createResponse()
            ->withStatus($responseCode)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write($this->view->render());

        return $response;
    }
}
