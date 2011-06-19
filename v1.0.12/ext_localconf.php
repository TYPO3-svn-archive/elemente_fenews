<?php
	if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

	## Extending TypoScript from static template uid=43 to set up userdefined tag:
	t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg', 'tt_content.CSS_editor.ch.tx_elementefenews_pi1 = < plugin.tx_elementefenews_pi1.CSS_editor', 43);
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_elementefenews_pi1.php', '_pi1','list_type', 0);

	## Include and register class/hook for "extraItemMarkerProcessor" simultaneously!
	## There is a nice feature in the getUserObj method which allows us to combine the two steps loading the file and registering the class:
	## Instead of putting require_once into ext_tables.php and registering your class in ext_localconf.php, you may register your new class with a line like this (in ext_localconf.php):
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['extraItemMarkerHook'][] = 'EXT:elemente_fenews/hooks/class.tx_elementefenews_ttnews.php:tx_elementefenews_ttnews';
?>