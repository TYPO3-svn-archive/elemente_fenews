<?php

########################################################################
# Extension Manager/Repository config file for ext "elemente_fenews".
#
# Auto generated 19-06-2011 16:33
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Frontend News',
	'description' => 'Frontend news submitter with RTE support (htmlArea RTE) and upload capabilities. Supports CAPTCHA (captcha, sr_freecap) for none access protected sites and DAM references (dam_ttnews) for image and file upload.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.0.15',
	'dependencies' => 'tt_news',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Andre Steiling',
	'author_email' => 'steiling@elemente.ms',
	'author_company' => 'elemente websolutions',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'tt_news' => '2.5.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:25:{s:9:"ChangeLog";s:4:"f40c";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"17b7";s:17:"ext_localconf.php";s:4:"1d88";s:14:"ext_tables.php";s:4:"59d5";s:14:"ext_tables.sql";s:4:"1b6d";s:15:"flexform_ds.xml";s:4:"e952";s:17:"flexform_ds30.xml";s:4:"7cfd";s:13:"locallang.xml";s:4:"8838";s:16:"locallang_db.xml";s:4:"a5d9";s:17:"locallang_tca.xml";s:4:"73e1";s:14:"doc/manual.sxw";s:4:"9cae";s:40:"hooks/class.tx_elementefenews_ttnews.php";s:4:"07b8";s:39:"lib/class.tx_elementefenews_version.php";s:4:"e688";s:45:"modfunc1/class.tx_elementefenews_modfunc1.php";s:4:"e21c";s:22:"modfunc1/locallang.xml";s:4:"1b11";s:14:"pi1/ce_wiz.gif";s:4:"2d8a";s:35:"pi1/class.tx_elementefenews_pi1.php";s:4:"bd32";s:43:"pi1/class.tx_elementefenews_pi1_wizicon.php";s:4:"65a3";s:17:"pi1/locallang.xml";s:4:"49a7";s:15:"res/ico_del.gif";s:4:"0deb";s:16:"res/ico_edit.gif";s:4:"4a05";s:21:"res/tmpl_default.html";s:4:"a92c";s:23:"static/ts/constants.txt";s:4:"e383";s:19:"static/ts/setup.txt";s:4:"63b7";}',
	'suggests' => array(
	),
);

?>