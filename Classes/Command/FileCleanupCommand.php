<?php

declare(strict_types=1);

/*
 * This file is part of the "pluploadbe" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SyntaxOOps\PluploadBE\Command;

use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use SyntaxOOps\PluploadBE\Service\FileCleanupService;
use SyntaxOOps\PluploadBE\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FileCleanupCommand
 *
 * @author  Haythem Daoud <haythemdaoud.x@gmail.com>
 */
class FileCleanupCommand extends Command
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setDescription(LocalizationUtility::translate('cleanup.description'))
            ->setAliases(['pluploadbe:cleanup'])
            ->addArgument('max_age', InputArgument::OPTIONAL,
                LocalizationUtility::translate('cleanup.args.max_age'), 86400);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $maxAge = (int)$input->getArgument('max_age');

        /** @var FileCleanupService $service */
        $service = GeneralUtility::makeInstance(FileCleanupService::class);

        try {
            $service->process($maxAge);
        } catch (FileDoesNotExistException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());
        }

        $io->success(LocalizationUtility::translate('cleanup.success'));

        return Command::SUCCESS;
    }
}
