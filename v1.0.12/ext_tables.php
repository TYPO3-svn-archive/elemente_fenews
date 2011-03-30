<?php
	if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

	t3lib_extMgm::addStaticFile($_EXTKEY,'static/ts/','Frontend News');

/*	if (TYPO3_MODE=='BE')	{
		t3lib_extMgm::insertModuleFunction(
			'user_task',
			'tx_elementefenews_modfunc1',
			t3lib_extMgm::extPath($_EXTKEY).'modfunc1/class.tx_elementefenews_modfunc1.php',
			'LLL:EXT:elemente_fenews/locallang_db.xml:moduleFunction.tx_elementefenews_modfunc1'
		);
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_elementefenews_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_elementefenews_pi1_wizicon.php';
	}
*/
	
	t3lib_div::loadTCA('tt_content');
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages,recursive';
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';

	// tt_news version
	require_once(t3lib_extMgm::extPath($_EXTKEY).'lib/class.tx_elementefenews_version.php');
	switch(tx_elementefenews_version::getEM_CONFVersion('tt_news')) {
		case '3.0':
			t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds30.xml');		
		break;
		default:
			t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');
		break;
	}
	
	t3lib_extMgm::addPlugin(array('LLL:EXT:elemente_fenews/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
?>