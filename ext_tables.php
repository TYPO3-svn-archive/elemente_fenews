<?php
	if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

	t3lib_extMgm::addStaticFile($_EXTKEY,'static/ts/','Frontend News');
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/css/','Frontend News Default Styles');

	if (TYPO3_MODE=='BE')	{
// New BE module is planed for version 1.2
/*		t3lib_extMgm::insertModuleFunction(
			'user_task',
			'tx_elementefenews_modfunc1',
			t3lib_extMgm::extPath($_EXTKEY).'modfunc1/class.tx_elementefenews_modfunc1.php',
			'LLL:EXT:elemente_fenews/locallang_db.xml:moduleFunction.tx_elementefenews_modfunc1'
		);
*/		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_elementefenews_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_elementefenews_pi1_wizicon.php';
	}

	t3lib_div::loadTCA('tt_content');
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages,recursive';
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');
	t3lib_extMgm::addPlugin(array('LLL:EXT:elemente_fenews/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	
	// Add new fiels to tt_news	
	$tempColumns = Array (
		'tx_elementefenews_fegroup' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:elemente_fenews/locallang_tca.xml:tt_news.tx_elementefenews_fegroup',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'fe_groups',
	            'size' => 1,
			),
		),
		'tx_elementefenews_feuser' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:elemente_fenews/locallang_tca.xml:tt_news.tx_elementefenews_feuser',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'fe_users',
	            'size' => 1,
			),
		),
		'tx_elementefenews_author' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:elemente_fenews/locallang_tca.xml:tt_news.tx_elementefenews_author',
			'config' => Array (
				'type' => 'none',
				'size' => 20,
			)
		),
	);

	t3lib_div::loadTCA('tt_news');
	t3lib_extMgm::addTCAcolumns('tt_news',$tempColumns,1);
	t3lib_extMgm::addToAllTCAtypes('tt_news','tx_elementefenews_fegroup,tx_elementefenews_feuser,tx_elementefenews_author');
?>