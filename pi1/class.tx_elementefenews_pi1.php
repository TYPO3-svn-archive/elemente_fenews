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

	require_once(PATH_tslib.'class.tslib_pibase.php');


	/**
	 * Plugin 'Frontend News' for the 'elemente_fenews' extension.
	 *
	 * @author	Andre Steiling <steiling@pilotprojekt.com>
	 * @package	TYPO3
	 * @subpackage	tx_elementefenews
	 */
	class tx_elementefenews_pi1 extends tslib_pibase {

		public $prefixId      = 'tx_elementefenews_pi1';		// Same as class name
		public $scriptRelPath = 'pi1/class.tx_elementefenews_pi1.php';	// Path to this script relative to the extension dir.
		public $extKey        = 'elemente_fenews';	// The extension key.
		public $pi_checkCHash = true;


		// Start adding fields for the RTE API
		public $RTEObj;
		public $docLarge				= 0;
		public $RTEcounter				= 0;
		public $formName;
		    // Initial JavaScript to be printed before the form
		    // (should be in head, but cannot due to IE6 timing bug)
		public $additionalJS_initial	= '';
		    // Additional JavaScript to be printed before the form
		    // (works in Mozilla/Firefox when included in head, but not in IE6)
		public $additionalJS_pre		= array();
		    // Additional JavaScript to be printed after the form
		public $additionalJS_post		= array();
		    // Additional JavaScript to be executed on submit
		public $additionalJS_submit		= array();
		public $PA = array(
			'itemFormElName'			=>  '',
			'itemFormElValue'			=> '',
		);
		public $specConf = array(
			'rte_transform'				=> array(
				'parameters'			=> array('mode' => 'ts_css')
			)
		);
		public $thisConfig				= array();
		public $RTEtypeVal				= 'text';
		public $thePidValue;		
		// End adding fields for the RTE API


		/**
		 *	The main method of the Plugin
		 *
		 *	@param		string		$content: The Plugin content
		 *	@param		array		$conf: The Plugin configuration
		 *	@return		The content that is displayed on the website
		 */
		function main($content, $conf) {
			$this->conf = $conf;
			$this->pi_setPiVarDefaults();
			$this->pi_loadLL();
			$this->pi_USER_INT_obj = 1; // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
			$this->pi_initPIflexForm(); // Init FlexForm configuration for plugin

			// System Language
			$this->languageUID				= $GLOBALS['TSFE']->config['config']['sys_language_uid']?$GLOBALS['TSFE']->config['config']['sys_language_uid']:0;

			// Storage PID
			$storagePID						= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'storagePID', 'baseConfig');
			$this->storagePID				= $storagePID?$storagePID:$GLOBALS['TSFE']->id;

			// Redirect PID
			$this->redirectPID				= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'redirectPID', 'baseConfig');

			// HTML Template
			$selectTMPL						= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'selectTMPL', 'baseConfig');
			$selectTMPL						= $selectTMPL?str_replace('/html/', '', $selectTMPL):$this->conf['template'];
			$this->mainTMPL					= $this->cObj->fileResource($selectTMPL);

			// Render/ Requierd fields
			$renderFields					= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'renderFields', 'fieldConfig');
			$renderFields					= $renderFields?t3lib_div::trimExplode(',', $renderFields):'title,archivedate,author,author_email,tx_elementefenews_author,short,bodytext,keywords,category,image,links';
			$requiredFields					= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'requiredFields', 'fieldConfig');
			$requiredFields					= $requiredFields?t3lib_div::trimExplode(',', $requiredFields):'title,author,author_email,bodytext';

			$this->renderFields				= array(
				'title'						=> array('render' => 0, 'req' => 0, 'file' => 0, 'sort' => 0, 'htmlsp' => 0),
				'archivedate'				=> array('render' => 0, 'req' => 1, 'file' => 0, 'sort' => 0, 'htmlsp' => 0), // if archivedate is set, it has always to be filled in!
				'author'					=> array('render' => 0, 'req' => 0, 'file' => 0, 'sort' => 0, 'htmlsp' => 0),
				'author_email'				=> array('render' => 0, 'req' => 0, 'file' => 0, 'sort' => 0, 'htmlsp' => 0),
				'tx_elementefenews_author'	=> array('render' => 0, 'req' => 0, 'file' => 0, 'sort' => 0, 'htmlsp' => 0),
				'short'						=> array('render' => 0, 'req' => 0, 'file' => 0, 'sort' => 0, 'htmlsp' => 0),
				'bodytext'					=> array('render' => 0, 'req' => 0, 'file' => 0, 'sort' => 0, 'htmlsp' => 0),
				'keywords'					=> array('render' => 0, 'req' => 0, 'file' => 0, 'sort' => 0, 'htmlsp' => 0),
				'category'					=> array('render' => 0, 'req' => 0, 'file' => 0, 'sort' => 0, 'htmlsp' => 0),
				'image'						=> array('render' => 0, 'req' => 0, 'file' => 1, 'sort' => 0, 'htmlsp' => 0),
				'imagecaption'				=> array('render' => 0, 'req' => 0, 'file' => 0, 'sort' => 0, 'htmlsp' => 0),
				'imagealttext'				=> array('render' => 0, 'req' => 0, 'file' => 0, 'sort' => 0, 'htmlsp' => 0),
				'imagetitletext'			=> array('render' => 0, 'req' => 0, 'file' => 0, 'sort' => 0, 'htmlsp' => 0),
				'links'						=> array('render' => 0, 'req' => 0, 'file' => 0, 'sort' => 0, 'htmlsp' => 0),
				'news_files'				=> array('render' => 0, 'req' => 0, 'file' => 1, 'sort' => 0, 'htmlsp' => 0)
			);
			
			if (is_array($renderFields)) {
				foreach($renderFields as $sort => $field) {
					if (is_array($this->renderFields[$field])) {
						$this->renderFields[$field]['render']	= 1;
						$this->renderFields[$field]['sort']		= $sort;
					}
				}
			}
			
			if (is_array($requiredFields)) {
				$this->requiredFields = 1;
				foreach($requiredFields as $field) {
					if (is_array($this->renderFields[$field])) $this->renderFields[$field]['req'] = 1;
				}
			}
			
			// Sorting $this->renderFields:
			// Build "columns" using the values of sub-array of $this->renderFields:
			// We get an array with keys named by the values of sub-array.
			$arrSort = array();
			foreach($this->renderFields as $field => $row) {
				foreach($row as $key => $value) {
					$arrSort[$key][$field] = $value;
				}
			}
			// Select key 'sort', it will do the sorting 
			array_multisort($arrSort['sort'], SORT_ASC, $this->renderFields); 

			// Captcha
			$useCaptcha	= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'useCaptcha', 'fieldConfig');
			if (t3lib_extMgm::isLoaded('sr_freecap') && $useCaptcha == 'sr_freecap' && !$GLOBALS['TSFE']->loginUser) {
				require_once(t3lib_extMgm::extPath('sr_freecap').'pi2/class.tx_srfreecap_pi2.php');
				$this->arrCaptcha['ext']		= 'sr_freecap';
				$this->arrCaptcha['captcha']	= t3lib_div::makeInstance('tx_srfreecap_pi2');
 			} else if (t3lib_extMgm::isLoaded('captcha') && $useCaptcha == 'captcha' && !$GLOBALS['TSFE']->loginUser) {
 				$this->arrCaptcha['ext']		= 'captcha';
				$this->arrCaptcha['captcha']	= '<img src="'.t3lib_extMgm::siteRelPath('captcha').'captcha/captcha.php" alt="" />';
			}
			
			// Category settings
			$this->categoryDefault	 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'categoryDefault', 'catConfig');
			$this->categorySelection = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'categorySelection', 'catConfig');

			// DAM support
			$damUse	= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'damUse', 'damConfig');
			if ($damUse != '') {
				// Needed libs for DAM
				require_once(PATH_t3lib.'class.t3lib_userauth.php');
				require_once(PATH_t3lib.'class.t3lib_userauthgroup.php');
				require_once(PATH_t3lib.'class.t3lib_beuserauth.php');
				require_once(PATH_t3lib.'class.t3lib_befunc.php');
				require_once(t3lib_extMgm::extPath('dam').'lib/class.tx_dam.php');
				require_once(t3lib_extMgm::extPath('dam').'lib/class.tx_dam_db.php');
				// DAM settings
				$this->damUse		= 1; // Activate DAM
				$this->damIdent		= $damUse; // Set MM ref ident to the given DAM News connector
				$this->damBeUser	= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'damBeUser', 'damConfig');
				$damImgPath			= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'damImgPath', 'damConfig');
				$this->damImgPath	= $damImgPath>0?$this->getFileMount($damImgPath):false;
				$damFilePath		= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'damFilePath', 'damConfig');
				$this->damFilePath	= $damFilePath>0?$this->getFileMount($damFilePath):false;
			}

			// "Publish" options
			$this->mailFeedback		= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'mailFeedback', 'mailConfig');
			$this->mailHTML			= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'mailHTML', 'mailConfig');
			$this->mailFrom			= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'mailFrom', 'mailConfig');
			$this->mailFromName		= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'mailFromName', 'mailConfig');
			$this->queuePublish		= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'queuePublish', 'mailConfig');
			$this->queueBeUser		= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'queueBeUser', 'mailConfig');

			// "Render" options
			$this->enableRTE		= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'enableRTE', 'optionsConfig');
			if ($this->enableRTE == 1) {
				require_once(t3lib_extMgm::extPath('rtehtmlarea').'pi2/class.tx_rtehtmlarea_pi2.php');
			}

			// "Time" options
			$this->autoEndtime		= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'autoEndtime', 'optionsConfig');
			$this->autoArchive		= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'autoArchive', 'optionsConfig');

			// Record delete mode
			$this->delMode			= $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'delMode', 'optionsConfig');

			// return
			$content = $this->controller();
			return $this->pi_wrapInBaseClass($content);
		}


		/**
		 *	Method to switch between the two methods "renderForm()" and "saveForm()"
		 *	in addiction on incomming piVars. Validates piVars by pre-calling "checkForm()" method.
		 *	If validation fails, "renderForm()" is called with additional user feedback.
		 *
		 *	@return		Rendered form by "renderForm()" or void
		 */
		function controller() {
			// Validate & save record
			if ($this->piVars['submit']) {
				$error = $this->validateForm();
				if ($error != '') return $this->renderForm($error);
					else $this->saveForm();
			}
			// Delete record & redirect
			else if ($this->piVars['del'] == 1) $this->deleteRecord();

			// New record
			else return $this->renderForm();
		}


		/**
		 *	Renders the form by the given settings from FlexForms configuration.
		 *	Checks also for some logical dependences like field exclusion if a setting
		 *	overrides an earlier one or disable captcha capabilities if "loginUser" are activated.
		 *
		 *	@param:		string		$error: piVars validation feedback returned by "checkForm()"
		 *	@return		Rendered form based on a HTML template
		 */
		function renderForm($error='') {
			// Set flag/uid to differ between new/edit record mode
			$editMode												= $this->piVars['edit']==1?$this->piVars['uid']:0;
			
			// Get record
			if ($editMode > 0) {
				// Check for use of categories
				if ($this->renderFields['category']['render'] == 1) {
					$res											= $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tt_news.*, tt_news_cat.uid AS catUid, tt_news_cat.shortcut', 'tt_news', 'tt_news_cat_mm', 'tt_news_cat', ' AND tt_news.uid='.intval($this->piVars['uid']).$this->cObj->enableFields('tt_news'));
					$this->piVars									= $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				} else {
					$res											= $GLOBALS['TYPO3_DB']->exec_SELECTquery('tt_news.*', 'tt_news', 'tt_news.uid='.intval($this->piVars['uid']).$this->cObj->enableFields('tt_news'));
					$this->piVars									= $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				}
				// If shortcut is set, put it into the value for redirect after saving the news
				$this->piVars['category']							= $this->piVars['shortcut']!=0?$this->piVars['catUid'].'|'.$this->piVars['shortcut']:$this->piVars['catUid'];
				
				// Check current user is owner of record
				$this->piVars['owner']								= ($this->piVars['tx_elementefenews_feuser'] == $GLOBALS['TSFE']->fe_user->user['uid'])?true:false;	
			}
			
			// Throw error message if current user is not the owner of record
			if ($editMode > 0 && $this->piVars['owner'] == false) {
				$subpart											= $this->cObj->getSubpart($this->mainTMPL, '###TEMPLATE_NOACCESS###');
				$markerArray										= array();
				$markerArray['###NOACCESS###']						= $this->pi_getLL('l_error_noaccess');
				
			} else {
				// Template
				$subpart											= $this->cObj->getSubpart($this->mainTMPL, '###TEMPLATE_FORM###');
			
				// Marker
				$markerArray										= array();
				$markerArray['###PREFIX_ID###']						= $this->prefixId;
				$markerArray['###FORM_URL###']						= $this->pi_getPageLink($GLOBALS['TSFE']->id);
				$markerArray['###FORM_EDIT###']						= $this->piVars['uid'];
				$markerArray['###LABEL_LEGEND###']					= $this->pi_getLL('form_legend');
				$markerArray['###LABEL_SUBMIT###']					= $this->pi_getLL('form_submit');
				$markerArray['###LABEL_RESET###']					= $this->pi_getLL('form_reset');
				$markerArray['###ERROR_MESSAGE###']					= $error;
	
				// Render fields
				foreach($this->renderFields as $field => $conf) {
					$fieldUpper										= strtoupper($field);
					if ($conf['render'] == 1) {
						$fieldSubpart								= $this->cObj->getSubpart($subpart, '###'.$fieldUpper.'###');
						$fieldArray['###PREFIX_ID###']				= $this->prefixId;
						$fieldArray['###LABEL_'.$fieldUpper.'###']	= $this->pi_getLL('l_'.$field, '', 1);
						$fieldArray['###VALUE_'.$fieldUpper.'###']	= $this->piVars[$field]?($conf['htmlsp']==1?htmlspecialchars($this->piVars[$field]):$this->piVars[$field]):'';
						$fieldArray['###CHECKED_'.$fieldUpper.'###']= $this->piVars[$field]?'checked="checked"':'';
						$fieldArray['###REQMARKER###']				= $conf['req']==1?$this->spanReplace($this->pi_getLL('l_required', '', 1), ' class="hili"'):'';
						// Render category selector
						if ($field == 'category') {
							$fieldArray['###CATEGORY_SELECT###']	= $this->getCategories();
						}
						// Render archivedate selectors
						if ($field == 'archivedate') {
							$fieldArray['###ARCHIVEDATE_SELECT###']	= $this->setArchivedate('adate').' '.$this->setArchivedate('amonth').' '.$this->setArchivedate('ayear');
						}
						// Complete subpart substitution
						$subpartArray['###'.$fieldUpper.'###']		= $this->cObj->substituteMarkerArray($fieldSubpart, $fieldArray);
					} else {
						$subpartArray['###'.$fieldUpper.'###']		= '';
					}
				}
	
				// Render current image in edit mode
				if ($this->piVars['uid'] && $this->renderFields['image']['render'] == 1) {
					$fieldSubpart									= $this->cObj->getSubpart($subpart, '###CURRENT_IMAGE###');
					$fieldArray['###LABEL_CURRENT_IMAGE###']		= $this->pi_getLL('l_current_image', '', 1);
					$fieldArray['###VALUE_CURRENT_IMAGE###']		= $this->getCurrentImage($this->piVars['uid']);
					$subpartArray['###CURRENT_IMAGE###']			= $this->cObj->substituteMarkerArray($fieldSubpart, $fieldArray);
				} else {
					$subpartArray['###CURRENT_IMAGE###']			= '';
				}
	
				// loginUser?
				if ($GLOBALS['TSFE']->loginUser) {
					$subpartArray['###AUTHOR###']					= ''; // Unset "normal" author field
					$subpartArray['###AUTHOR_EMAIL###']				= ''; // Unset "normal" email field
				}
	
				// Auto hide & auto archive?
				if ($this->autoEndtime > 0) {
					$subpartArray['###ARCHIVEDATE###']				= ''; // Unset "normal" email field
				}
	
				// Enable htmlAreaRTE? (rtehtmlarea_api_manual v2.1.0)
				if ($this->renderFields['bodytext']['render'] == 1 && $this->enableRTE == 1) {
					if (!$this->RTEObj) $this->RTEObj				= t3lib_div::makeInstance('tx_rtehtmlarea_pi2');
					if ($this->RTEObj->isAvailable()) {
						$this->RTEcounter++;
						$this->table								= 'tt_news';
						$this->field								= 'bodytext';
						$this->formName								= $this->prefixId.'-form';
						$this->PA['itemFormElName']					= $this->prefixId.'[bodytext]';
						$this->PA['itemFormElValue']				= $this->piVars['bodytext'];
						$this->thePidValue							= $GLOBALS['TSFE']->id;
						$RTEItem = $this->RTEObj->drawRTE(
							$this,
							'tt_news',
							'bodytext',
							$row = array(),
							$this->PA,
							$this->specConf,
							$this->thisConfig,
							$this->RTEtypeVal,
							'',
							$this->thePidValue
						);
						// "Global" marker array
						$markerArray['###ADDITIONALJS_PRE###']		= $this->additionalJS_initial.'<script type="text/javascript">'. implode(chr(10), $this->additionalJS_pre).'</script>';
						$markerArray['###ADDITIONALJS_POST###']		= '<script type="text/javascript">'. implode(chr(10), $this->additionalJS_post).'</script>';
						$markerArray['###ADDITIONALJS_SUBMIT###']	= 'onsubmit="'.implode(';', $this->additionalJS_submit).'"';
						// "Field" marker array
						$fieldSubpart								= $this->cObj->getSubpart($subpart, '###BODYTEXT_RTE###');
						$fieldArray['###PREFIX_ID###']				= $this->prefixId;
						$fieldArray['###LABEL_BODYTEXT_RTE###']		= $this->pi_getLL('l_bodytext', '', 1);
						$fieldArray['###VALUE_BODYTEXT_RTE###']		= $this->piVars['bodytext']?$this->piVars['bodytext']:'';
						$fieldArray['###REQMARKER###']				= $this->renderFields['bodytext']['req']==1?$this->spanReplace($this->pi_getLL('l_required', '', 1), ' class="hili"'):'';
						$fieldArray['###RTE_ITEM###']				= $RTEItem;
						// Complete subpart substitution
						$subpartArray['###BODYTEXT###']				= $this->cObj->substituteMarkerArray($fieldSubpart, $fieldArray);
						$subpartArray['###BODYTEXT_RTE###']			= ''; // Unset "RTE" bodytext field
					}
				} else {
					$markerArray['###ADDITIONALJS_PRE###']			= '';
					$markerArray['###ADDITIONALJS_POST###']			= '';
					$markerArray['###ADDITIONALJS_SUBMIT###']		= '';
					$subpartArray['###BODYTEXT_RTE###']				= ''; // Unset RTE bodytext field
				}
	
				// Captcha
				if (!$GLOBALS['TSFE']->loginUser) {
					$markerArray['###LABEL_CAPTCHA###']				= $this->pi_getLL('l_captcha', '', 1);
	    			// FreeCap
	    			if ($this->arrCaptcha['ext'] == 'sr_freecap' && is_object($this->arrCaptcha['captcha'])) {
	     				$markerArray								= array_merge($markerArray, $this->arrCaptcha['captcha']->makeCaptcha());
	    				$subpart									= $this->cObj->substituteSubpart($subpart, '###CAPTCHA_INSERT###', '');
	    			// Captcha
	    			} else if ($this->arrCaptcha['ext'] == 'captcha') {
	    				$markerArray['###CAPTCHA_TEXT###']			= $this->pi_getLL('l_captcha_text', '', 1);
	    				$markerArray['###CAPTCHA_IMAGE###']			= $this->arrCaptcha['captcha'];
	    				$subpart									= $this->cObj->substituteSubpart($subpart, '###SR_FREECAP_INSERT###', '');
	    			// No captcha
	     			} else {
	    				$subpart									= $this->cObj->substituteSubpart($subpart, '###SR_FREECAP_INSERT###', '');
	     				$subpart									= $this->cObj->substituteSubpart($subpart, '###CAPTCHA_INSERT###', '');
	     			}
				} else {
					$subpart										= $this->cObj->substituteSubpart($subpart, '###SR_FREECAP_INSERT###', '');
					$subpart										= $this->cObj->substituteSubpart($subpart, '###CAPTCHA_INSERT###', '');
				}
	
				// Render requird fields info
				if ($this->requiredFields == 1 || is_array($this->arrCaptcha)) {
					$markerArray['###REQUIRED_TEXT###']				= $this->spanReplace($this->pi_getLL('l_required_text', '', 1), ' class="hili"');
				} else {
					$subpart										= $this->cObj->substituteSubpart($subpart, '###REQUIRED_INFO###', '');
				}
	
				// "Clear" all fields in template an fill in the sorted subpart array: 
				foreach($subpartArray as $field) $sortedFields .= $field;
				$subpartArray['###SUBPART_SORTED_FIELDS###']		= $sortedFields;
			}

			// return
			return $this->cObj->substituteMarkerArrayCached($subpart, $markerArray, $subpartArray, array());
		}


		/**
		 *	Method to validate the user input after submitting the form in addiction on the
		 *	"render fields" and "required fields" set by FlexForm configuration.
		 *	Calls the handleUpload() method for images and files, if there is no validation error.
		 *	TODO: Enable multiple uploads
		 *
		 *	@return		A unsorted list with user feedback or void
		 */
		function validateForm() {
			$error		= '';
			// Check fields
			foreach ($this->renderFields as $field => $conf) {
				if ($conf['file']==0) { // Normal fields excluding archivedate!
					if (empty($this->piVars[$field]) && $field!='archivedate' && $conf['render']==1 && $conf['req']==1) {
						$error	.= '<li>'.str_replace('###FIELD###', '<strong>'.$this->pi_getLL('l_'.$field, '', 1).'</strong>', $this->pi_getLL('l_error_field', '', 1)).'</li>'.chr(10);
					}
				} else { // Upload fields
					if ($_FILES[$this->prefixId]['error'][$field] > 0 && $conf['req']==1) {
						$error	.= '<li>'.str_replace('###FIELD###', '<strong>'.$this->pi_getLL('l_'.$field, '', 1).'</strong>', $this->pi_getLL('l_error_field', '', 1)).'</li>'.chr(10);
					}
				}
			}
			// Check vaild mail
			if (!empty($this->piVars['author_email']) && $this->renderFields['author_email']['render']==1 && $this->renderFields['author_email']['req']==1) {
				if (t3lib_div::validEmail($this->piVars['author_email']) == false) {
					$error .= '<li>'.str_replace('###FIELD###', '<strong>'.$this->pi_getLL('l_author_email', '', 1).'</strong>', $this->pi_getLL('l_error_email', '', 1)).'</li>'.chr(10);
				}
			}
			// Check ALWAYS archivedate if it is set
			if ($this->renderFields['archivedate']['render']==1) {
				$noDate  = 0;
				$noDate  = empty($this->piVars['adate'])?1:0;
				$noDate += empty($this->piVars['amonth'])?1:0;
				$noDate += empty($this->piVars['ayear'])?1:0;
				$tsDate	 = mktime(0, 0, 0, intval($this->piVars['amonth']), intval($this->piVars['adate']), intval($this->piVars['ayear']));
				if ($noDate > 0) {
					$error	.= '<li>'.str_replace('###FIELD###', '<strong>'.$this->pi_getLL('l_archivedate', '', 1).'</strong>', $this->pi_getLL('l_error_field', '', 1)).'</li>'.chr(10);
				} else if ($tsDate <= time()) {
					$error	.= '<li>'.str_replace('###FIELD###', '<strong>'.$this->pi_getLL('l_archivedate', '', 1).'</strong>', $this->pi_getLL('l_error_archivedate', '', 1)).'</li>'.chr(10);
				}
			}
			// Check captcha
			if (!$GLOBALS['TSFE']->loginUser) {
				// FreeCap
    			if ($this->arrCaptcha['ext'] == 'sr_freecap' && is_object($this->arrCaptcha['captcha']) && !$this->arrCaptcha['captcha']->checkWord($this->piVars['captcha_response'])) {
					$error .= '<li>'.str_replace('###FIELD###', '<strong>'.$this->pi_getLL('l_captcha', '', 1).'</strong>', $this->pi_getLL('l_error_captcha', '', 1)).'</li>'.chr(10);
	    		// Captcha
    			} else if ($this->arrCaptcha['ext'] == 'captcha') {
	    			session_start();
					$captchaStr						= $_SESSION['tx_captcha_string'];
					$_SESSION['tx_captcha_string']	= '';
					if ($captchaStr != $this->piVars['captcha_response']) {
						$error .= '<li>'.str_replace('###FIELD###', '<strong>'.$this->pi_getLL('l_captcha', '', 1).'</strong>', $this->pi_getLL('l_error_captcha', '', 1)).'</li>'.chr(10);
					}
				}
	        }
	        // Handle uploads if there is no error until this point
	        if ($error == '') {
				$error .= $this->handleUpload('image');
				$error .= $this->handleUpload('news_files');
	       	}
			// return
			if ($error != '') {
				return '<h4>'.$this->pi_getLL('l_error_message', '', 1).'</h4><ul>'.$error.'</ul>';
			}
		}


		/**
		 *	Method to prepare the user imput like quoting strings.
		 *	Takes account of some logical dependences like field exclusion if a setting overrides a erlier one.
		 *	Reformats some fields like "links" and calls the "handleDAM()" method if needed.
		 *	At least it prepars the mail content and handle sending mails to admin user or submitter.
		 *	TODO: Enable multiple uploads
		 *
		 *	@return		Void (redirection to defined page)
		 */
		function saveForm() {
			// New or edit record
			$newsUID						= $this->piVars['edit']>0?intval($this->piVars['edit']):false;

			// Category settings
			if (!empty($this->piVars['category'])) {
				$arrCat						= t3lib_div::trimExplode('|', $this->piVars['category']);
				$redirect					= isset($arrCat[1])?$arrCat[1]:$this->redirectPID; // Redirect to category shortcut page, if set
				$this->piVars['category']	= 1; // Reset category, only one category could be seleted!
			} else {
				$this->piVars['category']	= 0;
				$redirect					= $this->redirectPID;
			}

			// News settings
			$arrNews						= array();
			$arrNews['pid']					= isset($arrCat[1])?$arrCat[1]:$this->storagePID; // Save news on category shortcut page, if set
			$arrNews['tstamp']				= time();
			$arrNews['crdate']				= time();
			$arrNews['hidden']				= $this->queuePublish==1?1:0; // queuePublish?
			$arrNews['datetime']			= time();
			$arrNews['category']			= $this->piVars['category'];
			$arrNews['sys_language_uid']	= $this->languageUID;

			// Archivedate or auto-hide / auto-archive?
			$day							= date('j', time());
			$month							= date('n', time());
			$year							= date('Y', time());
			if ($this->autoEndtime > 0) {
				$tsNow	= mktime(0, 0, 0, $month, $day, $year);
				$tsEnd	= $tsNow+(86400*$this->autoEndtime); // This time + days from FF
				if ($this->autoArchive == 1) {
					$arrNews['archivedate']	= $tsEnd;
				} else {
					$arrNews['endtime']		= $tsEnd;
				}
			} else if ($this->renderFields['archivedate']['render']==1) {
				$arrNews['archivedate']		= mktime(0, 0, 0, intval($this->piVars['amonth']), intval($this->piVars['adate']), intval($this->piVars['ayear']));
			}
			
			// Unset not needed piVars & quote inputs
			unset($this->piVars['edit']);
			unset($this->piVars['adate']);
			unset($this->piVars['amonth']);
			unset($this->piVars['ayear']);
			unset($this->piVars['submit']);
			foreach($this->piVars as $field => $input) {
				$arrNews[$field] = $GLOBALS['TYPO3_DB']->quoteStr(htmlspecialchars(trim($input)), 'tt_news');
			}
			
			// loginUser?
			if ($GLOBALS['TSFE']->loginUser) {
				$arrNews['tx_elementefenews_feuser']	= $GLOBALS['TSFE']->fe_user->user['uid'];
				$arrNews['author']						= $GLOBALS['TSFE']->fe_user->user['name'];
				$arrNews['author_email']				= $GLOBALS['TSFE']->fe_user->user['email'];
			}

			// RTE transformation (rtehtmlarea_api_manual v2.1.0)
			if (!empty($this->piVars['bodytext']) && $this->enableRTE == 1) {
				if (!$this->RTEObj) $this->RTEObj = t3lib_div::makeInstance('tx_rtehtmlarea_pi2');
				if ($this->RTEObj->isAvailable()) {
					$pageTSConfig			= $GLOBALS['TSFE']->getPagesTSconfig();
					$RTEsetup				= $pageTSConfig['RTE.'];
					$this->thisConfig		= $RTEsetup['default.'];
					$this->thisConfig		= $this->thisConfig['FE.'];
					$this->thePidValue		= $GLOBALS['TSFE']->id;
					$arrNews['bodytext']	= $this->RTEObj->transformContent(
						'db',
						$this->piVars['bodytext'],
						'tt_news',
						'bodytext',
						$arrNews,
						$this->specConf,
						$this->thisConfig,
						'',
						$this->thePidValue
					);
				}
				unset($arrNews['_TRANSFORM_bodytext']); // Unset not needed field
			} else {
				$arrNews['bodytext']		= str_replace('\r\n', chr(10), htmlspecialchars($this->piVars['bodytext']));
			}

			// Image handling
			if (!empty($_FILES[$this->prefixId]['name']['image'])) {
				if ($this->damUse == 1) {
					$damUidImg				= $this->handleDAM($this->arrUploads['image']['path']);
					if ($this->conf['debug'] == 1) t3lib_div::devLog('saveForm - handleDAM', 'elemente_fenews', 0, array('damUidImg' => $damUidImg));
					$arrNews['tx_damnews_dam_images'] = 1;
				} else {
					$arrNews['image']		= $GLOBALS['TYPO3_DB']->quoteStr($this->arrUploads['image']['hash'], 'tt_news');
				}
				$arrNews['imagealttext']	= !$arrNews['imagealttext']?$GLOBALS['TYPO3_DB']->quoteStr($this->arrUploads['image']['name'], 'tt_news'):$arrNews['imagealttext']; // ALT tag
				$arrNews['imagetitletext']	= !$arrNews['imagetitletext']?$GLOBALS['TYPO3_DB']->quoteStr($this->arrUploads['image']['name'], 'tt_news'):$arrNews['imagetitletext']; // TITLE tag
				$this->piVars['image']		= $this->arrUploads['image']['name']; // Put into piVars for mail content
			}

			// File handling
			if (!empty($_FILES[$this->prefixId]['name']['news_files'])) {
				if ($this->damUse == 1) {
					$damUidFile				= $this->handleDAM($this->arrUploads['news_files']['path']);
					$arrNews['tx_damnews_dam_media'] = 1;
				} else {
					$arrNews['news_files']	= $GLOBALS['TYPO3_DB']->quoteStr($this->arrUploads['news_files']['hash'], 'tt_news');
				}
				$this->piVars['news_files']	= $this->arrUploads['news_files']['name']; // Put into piVars for mail content
			}

			// Links reformating
			if (!empty($this->piVars['links'])) {
				$links						= t3lib_div::trimExplode('\r\n', $arrNews['links']);
				$arrNews['links']			= '';
				foreach($links as $url) {
					if ($this->isURL($url) == true) $arrNews['links'] .= $url.chr(10);
				}
			}

			// New record
			if ($newsUID == 0) {
				// DB: Insert news
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tt_news', $arrNews);
				$newsUID = $GLOBALS['TYPO3_DB']->sql_insert_id();
				if ($this->conf['debug'] == 1) t3lib_div::devLog('saveForm - new: record', 'elemente_fenews', 0, array('newsUID' => $newsUID));

				// DB: Default category
				$sort = 1;
				if (!empty($this->categoryDefault)) {
					$arrCatDef = t3lib_div::trimExplode(',', $this->categoryDefault);
					foreach ($arrCatDef as $sort => $uidCat) {
						$arrMM = array('uid_local' => $newsUID, 'uid_foreign' => intval($uidCat), 'sorting' => $sort);
						$GLOBALS['TYPO3_DB']->exec_INSERTquery('tt_news_cat_mm', $arrMM);
						if ($this->conf['debug'] == 1) t3lib_div::devLog('saveForm - new: def cat mm', 'elemente_fenews', 0, array('sql' => $GLOBALS['TYPO3_DB']->INSERTquery('tt_news_cat_mm', $arrMM)));
					}
				}
				
				// DB: Insert category relation
				if (!empty($this->piVars['category'])) {
					$arrMM = array('uid_local' => $newsUID, 'uid_foreign' => intval($arrCat[0]), 'sorting' => $sort);
					$GLOBALS['TYPO3_DB']->exec_INSERTquery('tt_news_cat_mm', $arrMM);
					if ($this->conf['debug'] == 1) t3lib_div::devLog('saveForm - new: fe cat mm', 'elemente_fenews', 0, array('sql' => $GLOBALS['TYPO3_DB']->INSERTquery('tt_news_cat_mm', $arrMM)));
				}

			// Edit record
			} else {
				// DB: Update news
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_news', 'uid='.$newsUID, $arrNews);
				if ($this->conf['debug'] == 1) t3lib_div::devLog('saveForm - upd: record', 'elemente_fenews', 0, array('sql' => $GLOBALS['TYPO3_DB']->UPDATEquery('tt_news', 'uid='.$newsUID, $arrNews)));

				// DB: Delete image DAM relation
				if (!empty($_FILES[$this->prefixId]['name']['image']) && $this->damUse == 1) {
					$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dam_mm_ref', 'uid_foreign='.$newsUID.' AND tablenames=\'tt_news\' AND ident=\''.$this->damIdent.'_dam_images\'');
					if ($this->conf['debug'] == 1) t3lib_div::devLog('saveForm - upd: del dam img', 'elemente_fenews', 0, array('sql' => $GLOBALS['TYPO3_DB']->DELETEquery('tx_dam_mm_ref', 'uid_foreign='.$newsUID.' AND tablenames=\'tt_news\' AND ident=\''.$this->damIdent.'_dam_images\'')));
				}

				// DB: Delete file DAM relation
				if (!empty($_FILES[$this->prefixId]['name']['news_files']) && $this->damUse == 1) {
					$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dam_mm_ref', 'uid_foreign='.$newsUID.' AND tablenames=\'tt_news\' AND ident=\''.$this->damIdent.'_dam_media\'');
					if ($this->conf['debug'] == 1) t3lib_div::devLog('saveForm - upd: del dam file', 'elemente_fenews', 0, array('sql' => $GLOBALS['TYPO3_DB']->DELETEquery('tx_dam_mm_ref', 'uid_foreign='.$newsUID.' AND tablenames=\'tt_news\' AND ident=\''.$this->damIdent.'_dam_media\'')));
				}

				// DB: Update category relation
				if (!empty($this->piVars['category'])) {
					// 1. Delete old relation, but not categoryDefault!!
					// TODO: Only one category is taken into account yet ...
					$whereCatDef = !empty($this->categoryDefault)?' AND uid_foreign NOT IN ('.$this->categoryDefault.')':'';
					$GLOBALS['TYPO3_DB']->exec_DELETEquery('tt_news_cat_mm', 'uid_local='.$newsUID.$whereCatDef);
					// 2. Add new one
					$arrMM = array('uid_local' => $newsUID, 'uid_foreign' => intval($arrCat[0]), 'sorting' => 1);
					$GLOBALS['TYPO3_DB']->exec_INSERTquery('tt_news_cat_mm', $arrMM);
				}
			}

			// DB: Insert image DAM relation
			if (!empty($_FILES[$this->prefixId]['name']['image']) && $this->damUse == 1) {
				$arrMM = array('uid_local' => $damUidImg, 'uid_foreign' => $newsUID, 'tablenames' => 'tt_news', 'ident' => $this->damIdent.'_dam_images', 'sorting' => 0, 'sorting_foreign' => 1);
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam_mm_ref', $arrMM);
				if ($this->conf['debug'] == 1) t3lib_div::devLog('saveForm - dam: insert img', 'elemente_fenews', 0, array('sql' => $GLOBALS['TYPO3_DB']->INSERTquery('tx_dam_mm_ref', $arrMM)));
			}

			// DB: Insert file DAM relation
			if (!empty($_FILES[$this->prefixId]['name']['news_files']) && $this->damUse == 1) {
				$arrMM = array('uid_local' => $damUidFile, 'uid_foreign' => $newsUID, 'tablenames' => 'tt_news', 'ident' => $this->damIdent.'_dam_media', 'sorting' => 0, 'sorting_foreign' => 1);
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam_mm_ref', $arrMM);
				if ($this->conf['debug'] == 1) t3lib_div::devLog('saveForm - dam: insert file', 'elemente_fenews', 0, array('sql' => $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam_mm_ref', $arrMM)));
			}

			// Mail: Alert publisher?
			if ($this->queuePublish == 1) {
				$arrBeUser	= $this->getBeUser();
				$arrMail	= $this->setMailContent('queue', $arrBeUser['name']);
				$htmlPart	= $this->mailHTML==1?$arrMail['html']:'';
				$this->sendMail($arrBeUser['email'], $this->pi_getLL('l_mail_subject', '', 1), $arrMail['plain'], $htmlPart, $this->mailFrom, $this->mailFromName);
			}

			// Mail: Feedback to submitter?
			if ($this->mailFeedback == 1) {
				$arrMail	= $this->setMailContent('feed', $arrNews['author']);
				$htmlPart	= $this->mailHTML==1?$arrMail['html']:'';
				$this->sendMail($arrNews['author_email'], $this->pi_getLL('l_mail_subject', '', 1), $arrMail['plain'], $htmlPart, $this->mailFrom, $this->mailFromName);
			}

			// Clear target page cache
			// Adding writelog support to clear_cacheCmd breaks functionality in T3 4.5
##			$this->clearPageCache($redirect);
			$GLOBALS['TSFE']->clearPageCacheContent_pidList($redirect);

			// Redirect to page
			header('Location: '.t3lib_div::locationHeaderUrl($this->pi_getPageLink($redirect)));
			die();
		}


		/**
		 *	Sets selected news record to deleted, clears the page cache of actual news plugin
		 *	and redirects to it after finishing the job.
		 *
		 * @return		void (redirection to defined page)
		 */
		function deleteRecord() {
			// DB: Set record to deleted
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_news', 'uid='.intval($this->piVars['uid']), array($this->delMode => 1));
			if ($this->conf['debug'] == 1) t3lib_div::devLog('deleteRecord - dam: delete', 'elemente_fenews', 0, array('sql' => $GLOBALS['TYPO3_DB']->UPDATEquery('tt_news', 'uid='.intval($this->piVars['uid']), array($this->delMode => 1))));
			
			// DB: If DAM is in use, delete ALL relations
			if ($this->damUse == 1) {
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dam_mm_ref', 'tablenames=\'tt_news\' AND uid_foreign='.intval($this->piVars['uid']));
				if ($this->conf['debug'] == 1) t3lib_div::devLog('deleteRecord - dam: delete', 'elemente_fenews', 0, array('sql' => $GLOBALS['TYPO3_DB']->DELETEquery('tx_dam_mm_ref', 'tablenames=\'tt_news\' AND uid_foreign='.intval($this->piVars['uid']))));
			}

			// The piVars given backPid
			$pid = intval($this->piVars['backPid']);

			// Clear target page cache
			// Adding writelog support to clear_cacheCmd breaks functionality in T3 4.5
##			$this->clearPageCache($redirect);
			$GLOBALS['TSFE']->clearPageCacheContent_pidList($pid);

			// Redirect
			header('Location: '.t3lib_div::locationHeaderUrl($this->pi_getPageLink($pid)));
			die();
		}


		/**
		 *	Helpful function when updating or deleting news records:
		 *	Clears the page cache of page where the news records are related to.
		 *
		 *	@param		integer		Target page uid
		 *	@return		void
		 */
		function clearPageCache($pid) {
			$TCE		= t3lib_div::makeInstance('t3lib_TCEmain');
			$TCE->admin	= 1;
			$TCE->clear_cacheCmd('pages');
			$TCE->clear_cacheCmd($pid);
		}


		/**
		 *	Function to validate and move the uploaded file.
		 *	Checks upload for allowed file extension & mime type and max filesize.
		 *	TODO: Different MIMEs for image and file
		 *
		 * @param		string		$file: Name of the upload field
		 * @return		A unsorted list with user feedback or void
		 */
		function handleUpload($file) {
			// Error handling
			$error	= '';
			// Is a file out there?
			if (!empty($_FILES[$this->prefixId]['name'][$file])) {
				// Set path for DAM or filelist
				$path		= $this->damUse==1?$this->damImgPath:$this->conf['path'];
				$fName		= $_FILES[$this->prefixId]['name'][$file];
				$fTemp		= $_FILES[$this->prefixId]['tmp_name'][$file];
				$fType		= $_FILES[$this->prefixId]['type'][$file];
				$fExt		= strtolower(substr(strrchr($fName, '.'), 1));

				// Get TS configuration
				$arrMime	= t3lib_div::trimExplode(',', $this->conf['mimeInclude']);
				$arrExt		= t3lib_div::trimExplode(',', $this->conf['extInclude']);

				// 1. Check for allowed MIME type
				if (in_array($fType, $arrMime) == true) {
					// 2. Check for allowed file extension
					if (in_array($fExt, $arrExt) == true) {
						$tmpFile = t3lib_div::upload_to_tempfile($fTemp);
						// 3. Check for max. filesize
						if ($tmpFile) {
							if ((filesize($tmpFile)<=$this->conf['maxsize'])) {
								// Rip of file extension form OrgName
								$point								= strrpos($fName, '.');
								$this->arrUploads[$file]['name']	= substr($fName, 0, $point); // Is needed for ALT- and TITLE
								$this->arrUploads[$file]['hash']	= t3lib_div::shortMD5($this->arrUploads[$file]['name'].time()).'.'.$fExt; // Is needed for filelist
								$this->arrUploads[$file]['path']	= PATH_site.$path.'/'.$this->arrUploads[$file]['hash']; // Is needed for DAM
								t3lib_div::upload_copy_move($tmpFile, $this->arrUploads[$file]['path']); // Move file
								t3lib_div::unlink_tempfile($tmpFile); // Unlink temp file
							} else $error = '<li>'.str_replace(array('###FIELD###', '###SIZE###'), array('<strong>'.$this->pi_getLL('l_'.$file, '', 1).'</strong>', '<strong>'.$this->setBytesToHuman($this->conf['maxsize']).'</strong>'), $this->pi_getLL('l_error_file_size', '', 1)).'</li>'; // File size error
						}
					} else $error = '<li>'.str_replace(array('###FIELD###', '###EXT###'), array('<strong>'.$this->pi_getLL('l_'.$file, '', 1).'</strong>', '<strong>'.$this->conf['extInclude'].'</strong>'), $this->pi_getLL('l_error_file_ext', '', 1)).'</li>'; // Extension error
				} else $error = '<li>'.str_replace(array('###FIELD###', '###MIME###'), array('<strong>'.$this->pi_getLL('l_'.$file, '', 1).'</strong>', '<strong>'.$this->conf['mimeInclude'].'</strong>'), $this->pi_getLL('l_error_file_mime', '', 1)).'</li>'; // MIME type error
			}
			// return
			return $error;
		}


		/**
		 *	Extracts the meta data of the given file by creating a simulated Be-User,
		 *	who could "start" the DAM "index_autoProcess". After putting the data in DB,
		 *	the records UID is returned and the Be-User is unset.
		 *
		 * @param		string		$file: Filename with complete path
		 * @return		integer		UID of the DAM record
		 */
		function handleDAM($file) {
			// Simulate BeUser
			$this->setBeUser();

			// For DAM 1.1 ...
			// Only hotfix!!!
			require_once(PATH_txdam.'lib/class.tx_dam_config.php');
			tx_dam_config::init();
			
			// Get meta data
			$index	= t3lib_div::makeInstance('tx_dam');
			$meta	= $index->index_autoProcess($file, true);
			
			// Save meta data
			$damdb	= t3lib_div::makeInstance('tx_dam_db');
			$uid	= $damdb->insertRecordRaw($meta['fields']);
			if ($this->conf['debug'] == 1) t3lib_div::devLog('handleDAM - insertRecordRaw', 'elemente_fenews', 0, array('uid' => $uid));

			// Kill BeUser
			$this->unsetBeUser();

			// return
			return $uid;
		}


		/**
		 *	Creates a Be-User, who could "handle" the DAM actions,
		 *	needed for indexing the uploaded files.
		 *
		 *	@return		void
		 */
		function setBeUser() {
			global $BE_USER;
			unset($BE_USER);

			$BE_USER		= t3lib_div::makeInstance('t3lib_beUserAuth');
			$BE_USER->OS	= TYPO3_OS;
			$BE_USER->setBeUserByUid($this->damBeUser);
			$BE_USER->fetchGroupData();
			$BE_USER->backendSetUC();

			$GLOBALS['BE_USER'] = $BE_USER;
		}


		/**
		 *	Unsets the Be-User, who was created for the DAM actions.
		 *
		 *	@return		void
		 */
		function unsetBeUser() {
			global $BE_USER;
			unset($BE_USER);
		}


		/**
		 *	Method for render the preview/ thumbnail image in edit news mode.
		 *	TODO: Keep care of multiple images ...
		 *
		 *	@param		interger	UID of news
		 *	@return		string		Thumbnail image
		 */
		function getCurrentImage($newsUID) {
			// Config
			$lconf = $this->conf['currentImage.'];

			// DAM reference
			if ($this->damUse==1) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query (
					'tx_dam.file_name, tx_dam.file_path, tx_dam.alt_text',
					'tx_dam', 'tx_dam_mm_ref', 'tt_news',
					'AND tx_dam_mm_ref.tablenames=\'tt_news\' AND tx_dam_mm_ref.sorting_foreign=1 AND tx_dam_mm_ref.ident=\'tx_damnews_dam_images\' AND tx_dam_mm_ref.uid_foreign='.$newsUID,
					'',
					'tx_dam_mm_ref.sorting_foreign ASC'
				);
				if ($this->conf['debug'] == 1) {
					t3lib_div::devLog('getCurrentImage - dam: img', 'elemente_fenews', 0, array('sql' => 'tx_dam.file_name, tx_dam.file_path, tx_dam.alt_text',
						'tx_dam', 'tx_dam_mm_ref', 'tt_news',
						'AND tx_dam_mm_ref.tablenames=\'tt_news\' AND tx_dam_mm_ref.sorting_foreign=1 AND tx_dam_mm_ref.ident=\'tx_damnews_dam_images\' AND tx_dam_mm_ref.uid_foreign='.$newsUID,
						'',
						'tx_dam_mm_ref.sorting_foreign ASC')
					);
				}
				
				// Image
				$arrImg				= $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$lconf['file']		= $arrImg['file_path'].$arrImg['file_name'];
				$lconf['altText']	= $arrImg['alt_text'];

			// File list
			} else {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('image, imagealttext, imagetitletext', 'tt_news', 'tt_news.uid='.$newsUID);
				if ($this->conf['debug'] == 1) t3lib_div::devLog('getCurrentImage - normal: img', 'elemente_fenews', 0, array('sql' => $GLOBALS['TYPO3_DB']->SELECTquery('image, imagealttext, imagetitletext', 'tt_news', 'tt_news.uid='.$newsUID)));
				
				// Image
				$arrImg				= $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$imgTmp				= t3lib_div::trimExplode(',', $arrImg['image']);
				$arrImg['image']	= $imgTmp[0];
				$lconf['file']		= $this->conf['currentImage.']['fileListPath'].$arrImg['image'];
				$lconf['altText']	= $arrImg['imagealttext'];
				$lconf['titleText']	= $arrImg['imagetitletext'];
				if ($this->conf['debug'] == 1) t3lib_div::devLog('getCurrentImage - normal: img conf', 'elemente_fenews', 0, $lconf);
			}

			// return
			return ($this->cObj->IMAGE($lconf));
		}


    	/**
		 *	Method to generate the plain text and HTML part for the mails based on a HTML template.
		 *	Puts automatically submitted piVars into the content and substitutes
		 *	filed names by locallang aliases. Differs between "admin" and "feedback" mode.
		 *
		 *	@param		string		$action: The "mode"
		 *	@return		array		Array with keys 'plain' and 'html' and there contents
		 */
		function setMailContent($action, $name) {
			// Templates
			$tmplPlain	= $this->cObj->getSubpart($this->mainTMPL, '###EMAIL_PLAINTEXT###');
			$tmplHTML	= $this->cObj->getSubpart($this->mainTMPL, '###EMAIL_HTMLTEXT###');

			// Marker
			$markerArray 							= array();
			$markerArray['###USER_NAME###']			= $name;
			$markerArray['###MAIL_SALUTATION###']	= $this->pi_getLL('l_mail_salutation', '', 1);
			$markerArray['###MAIL_INTRODUCTION###']	= $this->pi_getLL('l_mail_intro_'.$action, '', 1);
			$markerArray['###MAIL_REGARDS###']		= $this->pi_getLL('l_mail_regards', '', 1);

			// piVars
			$arrHTML	= array();
			$plainText	= '';
			foreach ($this->piVars as $key => $value) {
				$label			 = $this->pi_getLL('l_'.$key, '', 1)!=''?$this->pi_getLL('l_'.$key, '', 1).':':ucfirst($key).':';
				$arrHTML[$label] = $value;
				$plainText		.= $label.' '.$value.chr(10);
			}

			// Content
			$arrContent = array();
			// HTML content
			$markerArray['###ACCOUNT###']	= str_replace(array('<font face="Verdana,Arial" size="1">', '<font face="Verdana,Arial" size="1" color="red">', '</font>'), array('<strong>', '', ''), t3lib_div::view_array($arrHTML));
			$arrContent['html']				= $this->cObj->substituteMarkerArray($tmplHTML, $markerArray);
			// Plain text
			$markerArray['###ACCOUNT###']	= $plainText;
			$arrContent['plain']			= $this->cObj->substituteMarkerArray($tmplPlain, $markerArray);

			// Return
			return $arrContent;
		}


		/**
		 * Renders a news category select field based upon a subpart in the template.
		 *
		 * @return		string		Select field
		 */
		function getCategories() {
			// Subpart
			$tmpl			 = $this->cObj->getSubpart($this->mainTMPL, '###TEMPLATE_SELECT###');
			// Get categories
			$opts 			 = '<option value="">'.$this->pi_getLL('form_select', '', 1).'</option>';
			$res			 = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('uid, title, shortcut', 'tt_news_cat', 'uid IN ('.$this->categorySelection.')'.$this->cObj->enableFields('tt_news_cat'), '', 'title');
			while (($row	 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				$value		 = $row['shortcut']>0?$row['uid'].'|'.$row['shortcut']:$row['uid']; // if shortcut is set, put it into the value for redirect after saving the news
				$selected	 = $this->piVars['category']==$value?' selected="selected"':'';
				$opts		.= '<option value="'.$value.'"'.$selected.'>'.$row['title'].'</option>'.chr(10);
			}
			// Marker
			$marker							= array();
			$marker['###PREFIX_ID###']		= $this->prefixId;
			$marker['###NAME###']			= 'category';
			$marker['###OPTIONS###']		= $opts;
			// return
			return $this->cObj->substituteMarkerArray($tmpl, $marker);
		}


		/**
		 *	Renders a date select field based upon a subpart in the template.
		 *	Possible outputs are: "adate", "amonth", "ayear". In first line this
		 *	function is needed for output the archivedate field.
		 *	# TODO: Customize output by FF
		 *
		 *	@param		string		$pos: "Date position"
		 *	@return		string		Select field
		 */
		function setArchivedate($pos) {
			// Subpart
			$tmpl = $this->cObj->getSubpart($this->mainTMPL, '###TEMPLATE_SELECT###');
			// Options
			switch($pos) {
				case 'adate':
					$opts = '<option value=""></option>';
					for($x=1; $x<=31; $x++) {
						$selected	 = $this->piVars['adate']==sprintf('%02d', $x)?' selected="selected"':'';
						$opts		.= '<option value="'.sprintf('%02d', $x).'"'.$selected.'>'.sprintf('%02d', $x).'</option>'.chr(10);
					}
				break;
				case 'amonth':
					$opts = '<option value=""></option>';
					for($x=1; $x<=12; $x++) {
						$year	= date('Y', time());
						$month	= mktime(0, 0, 0, $x, 1, $year);
						$selected	 = $this->piVars['amonth']==sprintf('%02d', $x)?' selected="selected"':'';
						$opts		.= '<option value="'.sprintf('%02d', $x).'"'.$selected.'>'.strftime('%B', $month).'</option>'.chr(10);
					}
				break;
				case 'ayear':
					$opts = '<option value=""></option>';
					$year = date('Y', time())-1;
					for($x=1; $x<=3; $x++) {
						$selected	 = $this->piVars['ayear']==($year+$x)?' selected="selected"':'';
						$opts		.= '<option value="'.($year+$x).'"'.$selected.'>'.($year+$x).'</option>'.chr(10);
					}
				break;
			}
			// Marker
			$marker							= array();
			$marker['###PREFIX_ID###']		= $this->prefixId;
			$marker['###NAME###']			= $pos;
			$marker['###OPTIONS###']		= $opts;
			// return
			return $this->cObj->substituteMarkerArray($tmpl, $marker);
		}


		/**
		 *	Returns the path set by a defined filemount in the backend.
		 *	Takes care for absolute and relative "base".
		 *
		 *	@param		integer		$uid: UID of fielmount
		 *	@return		string		Filemount path
		 */
		function getFileMount($uid) {
			$res	= $GLOBALS['TYPO3_DB']->exec_SELECTquery ('path, base', 'sys_filemounts', 'uid='.intval($uid).$this->cObj->enableFields('sys_filemounts'));
			$row	= $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$path	= $row['base']==1?'fileadmin/'.$row['path']:$row['path'];
			// return
			return $path;
		}


		/**
		 *	Returns the real name and mail-address of a given Be-User..
		 *
		 *	@return		array		Array with keys "name" and "email"
		 */
		function getBeUser() {
			$res	= $GLOBALS['TYPO3_DB']->exec_SELECTquery ('realName AS name, email', 'be_users', 'uid='.intval($this->queueBeUser).$this->cObj->enableFields('be_users'));
			$row	= $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			// return
			return $row;
		}


		/**
		 *  Needful method for wrapping labels from locallang.
		 *	Wraps could be done by TAGs <span> and <strong>. If <span> is used,
		 *	a CSS class could be set, too.
		 *
		 *  @param		string		$str: String/Label
		 *	@param		string		$class='': CSS class for <span> => ' class="myClass"'
		 *	@param		string		$strong=0: Use <strong> instead of <span>
		 *	@return		Formated string
		 */
		function spanReplace($str, $class='', $strong=0) {
			if ($strong == 0) {
				return str_replace(array('###SPAN_BEGIN###', '###SPAN_END###'), array('<span'.$class.'>', '</span>'), $str);
			} else {
				return str_replace(array('###STRONG_BEGIN###', '###STRONG_END###'), array('<strong>', '</strong>'), $str);
			}
		}


		/**
		 *	Needful method adapted from extension "tt_products":
		 *	Extended mail method.
		 */
		function sendMail($toEMail, $subject, &$message, &$html, $fromEMail, $fromName, $attachment='') {
##			global $TYPO3_CONF_VARS;

			include_once (PATH_t3lib.'class.t3lib_htmlmail.php');

			$cls=t3lib_div::makeInstanceClassName('t3lib_htmlmail');
			if (class_exists($cls)) {
				$Typo3_htmlmail = t3lib_div::makeInstance('t3lib_htmlmail');
				$Typo3_htmlmail->start();
				$Typo3_htmlmail->subject = $subject;
				$Typo3_htmlmail->from_email = $fromEMail;
				$Typo3_htmlmail->from_name = $fromName;
				$Typo3_htmlmail->replyto_email = $Typo3_htmlmail->from_email;
				$Typo3_htmlmail->replyto_name = $Typo3_htmlmail->from_name;
				// AST: Set return path!
				$Typo3_htmlmail->returnPath = $Typo3_htmlmail->from_email;
				$Typo3_htmlmail->organisation = '';

				if ($attachment != '')
					$Typo3_htmlmail->addAttachment($attachment);

				if ($html)  {
					$Typo3_htmlmail->theParts['html']['content'] = $html; // Fetches the content of the page
					$Typo3_htmlmail->theParts['html']['path'] = t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST') . '/';

					$Typo3_htmlmail->extractMediaLinks();
					$Typo3_htmlmail->extractHyperLinks();
					$Typo3_htmlmail->fetchHTMLMedia();
					$Typo3_htmlmail->substMediaNamesInHTML(0);	// 0 = relative
					$Typo3_htmlmail->substHREFsInHTML();
					$Typo3_htmlmail->setHTML($Typo3_htmlmail->encodeMsg($Typo3_htmlmail->theParts['html']['content']));
					if ($message)	{
						$Typo3_htmlmail->addPlain($message);
					}
				} else {
					$Typo3_htmlmail->addPlain($message);
				}
				$Typo3_htmlmail->setHeaders();
				$Typo3_htmlmail->setContent();
				$Typo3_htmlmail->setRecipient(explode(',', $toEMail));
				$Typo3_htmlmail->sendTheMail();
			}
		}


		/**
		 *	Needful method adapted from extension "w4x_backup":
		 *	Transforms bytes to a human readable measure.
		 *
		 *	@param		integer		Bytes
		 *	@param		integer		Precision of post decimal positions, default: 2
		 *	@return		Calculated human readable measure
		 */
		function setBytesToHuman($bytes, $precision=2) {
			if (!is_numeric($bytes) || $bytes < 0) {
				return false;
			}
			for ($level = 0; $bytes >= 1024; $level++) {
				$bytes /= 1024;
			}
			switch ($level) {
				case 0:
					$suffix = 'Bytes';
				break;
				case 1:
					$suffix = 'KB';
				break;
				case 2:
					$suffix = 'MB';
				break;
				case 3:
					$suffix = 'GB';
				break;
				case 4:
					$suffix = 'TB';
				break;
			}
			// return
			return round($bytes, $precision).' '.$suffix;
		}


		/**
		 *	Needful method adapted from extension "ve_guestbook":
		 *	Preg_match URL validation.
		 *
		 * @param	string		$url: URL to validate
		 * @return	boolean		Success: valid / not valid
		 */
		function isURL($url) {
			if (!preg_match('#^http\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $url)) {
				return false;
			} else {
				return true;
			}
		}


	} // class


	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/elemente_fenews/pi1/class.tx_elementefenews_pi1.php'])	{
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/elemente_fenews/pi1/class.tx_elementefenews_pi1.php']);
	}
?>