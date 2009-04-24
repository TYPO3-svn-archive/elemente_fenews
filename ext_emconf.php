<?php
	########################################################################
	# Extension Manager/Repository config file for ext: "elemente_fenews"
	#
	# Auto generated 31-10-2007 15:03
	#
	# Manual updates:
	# Only the data in the array - anything else is removed by next write.
	# "version" and "dependencies" must not be touched!
	########################################################################

	$EM_CONF[$_EXTKEY] = array(
		'title' => 'Frontend News',
		'description' => 'Frontend news submitter with RTE support (htmlArea RTE) and upload capabilities. Supports CAPTCHA (captcha, sr_freecap) for none access protected sites and DAM references (dam_ttnews) for image and file upload.',
		'category' => 'plugin',
		'author' => 'Andre Steiling',
		'author_email' => 'steiling@elemente.ms',
		'author_company' => 'elemente websolutions',
		'shy' => '',
		'dependencies' => 'tt_news',
		'conflicts' => '',
		'priority' => '',
		'module' => '',
		'state' => 'stable',
		'internal' => '',
		'uploadfolder' => 0,
		'createDirs' => '',
		'modify_tables' => '',
		'clearCacheOnLoad' => 0,
		'lockType' => '',
		'version' => '1.0.2',
		'constraints' => array(
			'depends' => array(
				'taskcenter' => '',
				'tt_news' => '',
			),
			'conflicts' => array(
			),
			'suggests' => array(
			),
		),
		'_md5_values_when_last_written' => 'a:13:{s:9:"ChangeLog";s:4:"f40c";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"49a0";s:14:"ext_tables.php";s:4:"3126";s:16:"locallang_db.xml";s:4:"a1cf";s:19:"doc/wizard_form.dat";s:4:"7e0b";s:20:"doc/wizard_form.html";s:4:"6bb0";s:45:"modfunc1/class.tx_elementefenews_modfunc1.php";s:4:"85b7";s:22:"modfunc1/locallang.xml";s:4:"9830";s:35:"pi1/class.tx_elementefenews_pi1.php";s:4:"64ed";s:17:"pi1/locallang.xml";s:4:"0a3b";s:24:"pi1/static/editorcfg.txt";s:4:"ab5e";}',
	);
?>