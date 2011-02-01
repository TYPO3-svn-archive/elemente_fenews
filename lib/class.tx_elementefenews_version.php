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


	class tx_elementefenews_version {
		
		/**
		 * Return the EM_CONF version (slice to main / sub version) of the extension.
		 * 
		 * @param string $extKey extension key
		 */
		function getEM_CONFVersion($extKey) {
			$extFile = t3lib_extMgm::extPath($extKey).'ext_emconf.php';
			$_EXTKEY = $extKey;
			include($extFile);
			return substr($EM_CONF[$_EXTKEY]['version'], 0, 3);
		}

	}

	
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/elemente_fenews/lib/class.tx_elementefenews_version.php']) {
		include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/elemente_fenews/lib/class.tx_elementefenews_version.php']);
	}
?>