<?php
# Alert the user that this is not a valid access point to MediaWiki if they try to access the special pages file directly.
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/ExternalLinkAnalysis/ExternalLinkAnalysis.php" );
EOT;
	exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'ExternalLinkAnalysis',
	'author' => 'Raymond Kertezc',
	'url' => 'https://www.mediawiki.org/wiki/Extension:ExternalLinkAnalysis',
	'descriptionmsg' => 'externallinkanalysis-desc',
	'version' => '1.0.1',
);

$wgAutoloadClasses['SpecialExternalLinkAnalysis'] = __DIR__ . '/SpecialExternalLinkAnalysis.php'; # Location of the SpecialMyExtension class (Tell MediaWiki to load this file)
$wgMessagesDirs['ExternalLinkAnalysis'] = __DIR__ . "/i18n"; # Location of localisation files (Tell MediaWiki to load them)
$wgExtensionMessagesFiles['ExternalLinkAnalysisAlias'] = __DIR__ . '/ExternalLinkAnalysis.alias.php'; # Location of an aliases file (Tell MediaWiki to load it)
$wgSpecialPages['ExternalLinkAnalysis'] = 'SpecialExternalLinkAnalysis'; # Tell MediaWiki about the new special page and its class name

$wgHooks['LoadExtensionSchemaUpdates'][] = 'createLinkDesc';
function createLinkDesc( DatabaseUpdater $updater ) {
	$updater->addExtensionTable( 'link_desc',
		__DIR__ . '/link_desc.sql' );
	return true;
}