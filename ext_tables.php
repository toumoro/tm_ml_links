<?php
if (!defined ('TYPO3')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('tm_ml_links', 'Configuration/TypoScript/', 'Default TS');

?>