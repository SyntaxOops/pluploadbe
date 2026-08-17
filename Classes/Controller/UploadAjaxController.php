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

/**
 * Class UploadController
 *
 * @author  Haythem Daoud <haythemdaoud.x@gmail.com>
 */
class UploadAjaxController extends ActionController
{
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

        $parsedBody = (array)$this->request->getParsedBody();
        $queryParams = $this->request->getQueryParams();
        $combinedFolderIdentifier = (string)($parsedBody['id'] ?? $queryParams['id'] ?? '');
        $fileName = (string)($parsedBody['name'] ?? $queryParams['name'] ?? '');
        $chunk = (int)($parsedBody['chunk'] ?? $queryParams['chunk'] ?? 0);
        $chunks = (int)($parsedBody['chunks'] ?? $queryParams['chunks'] ?? 0);

        $responseCode = 200;

        try {
            $uploadService->process($combinedFolderIdentifier, $fileName, $chunk, $chunks);
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

        $response = $this->responseFactory->createResponse()
            ->withStatus($responseCode)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write((string)json_encode($result, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));

        return $response;
    }
}
