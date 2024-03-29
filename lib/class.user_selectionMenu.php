<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 2011 Andre Steiling <steiling@elemente.ms>
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
	 * Class 'user_selectionMenu' for the 'elemente_fenews' extension.
	 *
	 * @author	Andre Steiling <steiling@elemente.ms>
	 * @package	TYPO3
	 * @subpackage	tx_elementefenews
	 */
	class user_selectionMenu {
		public		$extKey		= 'elemente_fenews';
		protected	$getLL		= 'LLL:EXT:elemente_fenews/pi1/locallang.xml';


		/**
		 * Method adapted from extension "tt_news":
		 *
		 * @param	array		$menuArr: HMENU array structure
		 * @param	array		$conf: HMENU configuration
		 * 
		 * @return	array		All categories in a nested array
		 */
		public function getSelectionMenu($arrMenu, $conf) {
			$lConf							= $conf['userFunc.']; 
			$arrMenu				 		= array();

			// Static items of a selection field
			if ($lConf['staticItems'] == 1) {
				switch ($lConf['table']) {
					case 'fe_groups':
						$arrMenu[]	= array(
										'uid'		=> '-1',
										'title'		=> $GLOBALS['TSFE']->sL($this->getLL.':ts_fe_group_hide'),
										'selected'	=> 0,
									);
						$arrMenu[]	= array(
										'uid'		=> '-2',
										'title'		=> $GLOBALS['TSFE']->sL($this->getLL.':ts_fe_group_show'),
										'selected'	=> 0,
									);
					break;
					case 'sys_language':
						$arrMenu[]	= array(
										'uid'		=> '0',
										'title'		=> $GLOBALS['TSFE']->sL($this->getLL.':ts_sys_language_default'),
										'selected'	=> 0,
									);
						$arrMenu[]	= array(
										'uid'		=> '-1',
										'title'		=> $GLOBALS['TSFE']->sL($this->getLL.':ts_sys_language_all'),
										'selected'	=> 0,
									);
					break;
				}
			}
			
			// Database items
			$res	 						= $GLOBALS['TYPO3_DB']->exec_SELECTquery($lConf['select'], $lConf['table'], $lConf['where'].$this->cObj->enableFields($lConf['table']), '', $lConf['order']);
			while (($row					= $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				// Recursive only if a field "parent" is given in database table
				if ($lConf['parent'] != '') {
					$arrSub['_SUB_MENU']	= $this->getSubSelectionMenu($row['uid'], $lConf);
					$arrMenu[]				= is_array($arrSub['_SUB_MENU']) ? array_merge($row, $arrSub) : '';
				} else {
					// Selected entries
					$row['selected'] 		= 0;
					if (is_array($lConf['selected'])) {
						foreach($lConf['selected'] as $sel) {
							if ($sel == $row['uid']) $row['selected'] = 1;
						}
					}
					$arrMenu[]				= $row;
				}
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			
			// Static items, selected entries
			if ($lConf['staticItems'] == 1) {
				if (is_array($lConf['selected'])) {
					for($i=0; $i<=count($arrMenu); $i++) {
						foreach($lConf['selected'] as $sel) {
							if ($sel == $arrMenu[$i]['uid']) $arrMenu[$i]['selected'] = 1;
						}	
					}
				}
			}
			
			// skipFirst is used for category menu, it skips the root category - something like entrylevel ...
			if ($lConf['skipFirst'] == 1) $arrMenu = $arrMenu[0]['_SUB_MENU'];

			// return
			return $arrMenu;
		}

	
		/**
		 * Method adapted from extension "tt_news":
		 * Extends a given list of categories by their subcategories.
		 * This function returns a nested array with subcategories.
		 *
		 * @param	integer		$item: category uid which will be extended by subcategories
		 * @param	array		$lConf: userFunc configuration
		 * 
		 * @return	array		All categories in a nested array
		 */
		public function getSubSelectionMenu($item, $lConf) {
			$arrMenuSub					= array();
			$res						= $GLOBALS['TYPO3_DB']->exec_SELECTquery($lConf['select'], $lConf['table'], $lConf['parent'].' IN ('.$item.')'.$this->cObj->enableFields($lConf['table']), '', $lConf['order']);
			while (($row				= $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				$cc++;
				if ($cc > 10000) {
					$GLOBALS['TT']->setTSlogMessage('elemente_fenews: one or more recursive categories were found');
					return $arrMenuSub;
				}
				$arrSub['_SUB_MENU']	= $this->getSubSelectionMenu($row['uid'], $lConf);
				// Selected entries
				$row['selected'] 		= 0;
				if (is_array($lConf['selected'])) {
					foreach($lConf['selected'] as $sel) {
						if ($sel == $row['uid']) $row['selected'] = 1;
					}
				}
				$arrMenuSub[]			= is_array($arrSub['_SUB_MENU']) ? array_merge($row, $arrSub) : '';
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			// return
			return $arrMenuSub;
		}

	}
?>