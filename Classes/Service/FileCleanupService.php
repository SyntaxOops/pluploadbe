<?php

declare(strict_types=1);

/*
 * This file is part of the "pluploadbe" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SyntaxOOps\PluploadBE\Service;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FileCleanupService
 *
 * @author  Haythem Daoud <haythemdaoud.x@gmail.com>
 */
class FileCleanupService
{
    protected string $extension = 'part';
    protected string $table = 'sys_file';

    /**
     * @param int $age
     * @throws Exception
     * @throws FileDoesNotExistException
     */
    public function process(int $age): void
    {
        /** @var ResourceFactory $resourceFactory */
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

        $stale = $this->getStale($age);

        foreach ($stale as $file) {
            $fileObject = $resourceFactory->getFileObject($file['uid']);
            $fileObject->delete();
        }
    }

    /**
     * @param int $age
     * @return array
     * @throws Exception
     */
    protected function getStale(int $age): array
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->select('uid')
            ->from($this->table)
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->or(
                        $queryBuilder->expr()->lte('tstamp', $queryBuilder->createNamedParameter(time() - $age, ParameterType::INTEGER)),
                        $queryBuilder->expr()->lte('modification_date', $queryBuilder->createNamedParameter(time() - $age, ParameterType::INTEGER))
                    ),
                    $queryBuilder->expr()->eq('extension', $queryBuilder->createNamedParameter($this->extension)),
                    $queryBuilder->expr()->eq('missing', $queryBuilder->createNamedParameter(0, ParameterType::INTEGER))
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
    }
}
