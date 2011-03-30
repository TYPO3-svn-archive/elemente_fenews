<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 2007 Andre Steiling <steiling@pilotprojekt.com>
	*  All rights reserved
	*
	*  This script is part of the TYPO3 project. The TYPO3 project is
	*  free software; you can redistribute it and/or modify
	*  it under the terms of the GNU General Public License as published by
	*  the Free Software Foundation; either version 2 of the License, or
	*  (at your option) any later version.
	*
	*  The GNU General Public License can be found at
	*  http://www.gnu.org/copyleft/gpl.html.
	*
	*  This script is distributed in the hope that it will be useful,
	*  but WITHOUT ANY WARRANTY; without even the implied warranty of
	*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	*  GNU General Public License for more details.
	*
	*  This copyright notice MUST APPEAR in all copies of the script!
	***************************************************************/


	/**
	 * Module extension (addition to function menu) 'Frontend News Management' for the 'elemente_fenews' extension.
	 *
	 * @author	Andre Steiling <steiling@pilotprojekt.com>
	 * @package	TYPO3
	 * @subpackage	tx_elementefenews
	 */
	class tx_elementefenews_modfunc1 extends mod_user_task {


		/**
		 * Makes the content for the overview frame...
		 *
		 * @return	HTML
		 */
		function overview_main()	{
			$icon = '<img src="'.$this->backPath.t3lib_extMgm::extRelPath('elemente_fenews').'ext_icon.gif" width="18" height="16" class="absmiddle">';
			$content = $this->mkMenuConfig($icon.$this->headLink(tx_elementefenews_modfunc1,1),'',$this->overviewContent());

			return $content;
		}


		/**
		 * Main method
		 *
		 * @return	HTML
		 */
		function main() {
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

			return $this->mainContent();
		}


		/**
		 * Returns content in overview frame
		 *
		 * @return	Content for overview frame
		 */
		function overviewContent()	{
		#	$content = 'Content in overview frame...';
		#	return '<a href="index.php?SET[function]=tx_elementefenews_modfunc1"  onClick="this.blur();"><img src="'.$this->backPath.'gfx/edit2.gif" style="float: left;"></a><div><a href="index.php?SET[function]=tx_elementefenews_modfunc1"  onClick="this.blur();">'.$content.'</a></div>';

		#	include_once('locallang.xml');
			global $LANG;
			$dbquery	= "SELECT * FROM tt_news where hidden=1 AND deleted=0";
			$dbres		=  mysql(TYPO3_db,$dbquery);
			$num		= mysql_num_rows($dbres);
			return $LANG->getLL('queue')." (<b>$num</b>)";
		}


					/**
		 * Main content method
		 *
		 * @return	Main content for the module
		 */
		function mainContent()	{
		#	include_once('locallang.xml');
			global $LANG;

			if(isset($_POST['command'])) {
				foreach($_POST['sluid'] as $uid) {
					switch($_POST['command']) {
						case $LANG->getLL('Delete'):
							mysql(TYPO3_db,"DELETE FROM tt_news WHERE uid=$uid");
						break;
						case $LANG->getLL('Publish'):
							mysql(TYPO3_db,"UPDATE tt_news SET hidden=0,datetime=".time()." WHERE uid=$uid");
						break;
					}
			 	}
			}
			$dbquery = "SELECT * FROM tt_news where hidden=1 AND deleted=0 ORDER BY crdate";

			$dbres = mysql(TYPO3_db,$dbquery);
			$content = '<form method="post"><table width="90%" border="0" cellspacing="2px" cellpadding="2px">';
			$content .= "\n<tr><td><b>".$LANG->getLL('Name')."</b></td><td width=\"5%\"><b>".$LANG->getLL('Selected')."</b></td></tr>";
			$bgcolor = "#cccccc";

			while($row = mysql_fetch_array($dbres)) {
				$content .= "<tr bgcolor=\"$bgcolor\"><td><a href=\"#\" onClick=\"top.launchView('tt_news', '".$row['uid']."');\">".$row['title']."</a></td><td><input type=\"checkbox\" name=\"sluid[]\" value=\"".$row['uid']."\"></td></tr>";
				if($bgcolor == "#cccccc") {
					$bgcolor = "#eeeeee";
				} else {
					$bgcolor = "#cccccc";
				}
			}

			$content .= "<tr><td colspan=\"2\" align=\"right\">\n<select name=\"command\">\n";
			$content .= "<option>".$LANG->getLL('Delete')."\n";
			$content .= "<option>".$LANG->getLL('Publish')."\n";
			$content .= "</select><br><input type=\"Submit\" value=\"".$LANG->getLL("Refresh")."\"></input></br></td></tr></table></form>";
			return($content);
		}


	} // class


	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/elemente_fenews/modfunc1/class.tx_elementefenews_modfunc1.php'])	{
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/elemente_fenews/modfunc1/class.tx_elementefenews_modfunc1.php']);
	}
?>