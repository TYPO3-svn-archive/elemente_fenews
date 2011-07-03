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
	 * Class 'user_stdWrapPreUserFunc' for the 'elemente_fenews' extension.
	 *
	 * @author	Andre Steiling <steiling@elemente.ms>
	 * @package	TYPO3
	 * @subpackage	tx_elementefenews
	 */
	class user_stdWrapPreUserFunc { 

		/**
		 * preUserFunc for tt_news stdWrap and fields:
		 * I didn't find a TypoScript function to strip the backslashes
		 * insert by DBAL->quoteStr call, when saving a record, so I wrote a
		 * simple preUserFunc and added to the tt_news TypoScript setup.
		 *
		 * @param	string		$content: The stdWrap content
		 * @param	array		$conf: The stdWrap configuration
		 * 
		 * @return	string		Stripslashes content
		 */
   		function stripSlashes($content, $conf) {
   			return stripslashes($content);
   		}
   
	}
?>