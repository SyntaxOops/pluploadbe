<?php
defined('TYPO3') or die;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;


(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\CMS\Filelist\Controller\FileListController'] = [
        'className' => 'SyntaxOOps\PluploadBE\Xclass\FileListController',
    ];

    ExtensionManagementUtility::addUserTSConfig('options.hideModules := addToList(Plupload_BE)');
})();
