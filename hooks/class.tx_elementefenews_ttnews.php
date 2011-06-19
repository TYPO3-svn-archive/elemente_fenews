<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 2008 Andre Steiling <steiling@elemente.ms>
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
	 * This hook extends class tx_ttnews.
	 * It adds new marker to method "getItemMarkerArray()".
	 *
	 */
	class tx_elementefenews_ttnews {
		function extraItemMarkerProcessor($markerArray, $row, $lConf, $parentObject) {
			$links		 = '';

			// Edit record
			if ($lConf['feEdit.']['editRecord'] == 1 && ($row['tx_elementefenews_feuser'] == $GLOBALS['TSFE']->fe_user->user['uid'])) {
				$label													 = $parentObject->cObj->stdWrap($parentObject->pi_getLL('tx-elementefenews-editRecord'), $lConf['feEdit.']['labelWrap.']);
				$lConf['feEdit.']['editRecord.']['title']				 = $parentObject->pi_getLL('tx-elementefenews-editRecord');
				$lConf['feEdit.']['editRecord.']['additionalParams']	.= '&tx_elementefenews_pi1[edit]=1&tx_elementefenews_pi1[uid]='.$row['uid'];
				$links													.= $parentObject->cObj->typoLink($label, $lConf['feEdit.']['editRecord.']);
			}

			// Delete record
			if ($lConf['feEdit.']['delRecord'] == 1 && ($row['tx_elementefenews_feuser'] == $GLOBALS['TSFE']->fe_user->user['uid'])) {
				$label	 												 = $parentObject->cObj->stdWrap($parentObject->pi_getLL('tx-elementefenews-delRecord'), $lConf['feEdit.']['labelWrap.']);
				$lConf['feEdit.']['delRecord.']['title']				 = $parentObject->pi_getLL('tx-elementefenews-delRecord'); 
				$lConf['feEdit.']['delRecord.']['additionalParams']		.= '&tx_elementefenews_pi1[del]=1&tx_elementefenews_pi1[uid]='.$row['uid'].'&tx_elementefenews_pi1[backPid]='.$GLOBALS['TSFE']->id;
				$links													.= $parentObject->cObj->typoLink($label, $lConf['feEdit.']['delRecord.']);
			}

			// Hide author when checkbox "anonymous record" is set:
			if ($row['tx_elementefenews_author'] == 1) {
				$markerArray['###NEWS_AUTHOR###']		= '';
				$markerArray['###NEWS_EMAIL###']		= '';
			}

			// Wrap output
			if ($links != '') {
				$markerArray['###FE_FUNCTIONS###']		= $parentObject->cObj->stdWrap($links, $lConf['feEdit.']);
			} else $markerArray['###FE_FUNCTIONS###']	= '';

			// return
			return $markerArray;
		}
	}


	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/elemente_fenews/hooks/class.tx_elementefenews_ttnews.php']) {
		include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/elemente_fenews/hooks/class.tx_elementefenews_ttnews.php']);
	}
?>