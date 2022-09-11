<?php
namespace wcf\acp\form;
use wcf\data\jcoins\voucher\category\JCoinsVoucherCategory;
use wcf\data\jcoins\voucher\category\JCoinsVoucherCategoryNodeTree;
use wcf\data\jcoins\voucher\JCoinsVoucherAction;
use wcf\data\jcoins\voucher\type\JCoinsVoucherType;
use wcf\data\jcoins\voucher\type\JCoinsVoucherTypeList;
use wcf\data\media\ViewableMediaList;
use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\form\AbstractForm;
use wcf\system\category\CategoryHandler;
use wcf\system\condition\ConditionHandler;
use wcf\system\exception\UserInputException;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

/**
 * Shows the JCoins voucher add form.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherAddForm extends AbstractForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.voucherJCoins.add';
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.jcoins.voucher.canManage'];
	
	/**
	 * general data
	 */
	public $availableLanguages = [];
	public $availablePeriods = [];
	public $availableTypes = [];
	public $htmlInputProcessors = [];
	
	/**
	 * voucher data
	 */
	
	public $voucherID = 0;
	public $title = '';
	public $isDisabled = 1;
	public $type = null;
	public $typeID = 0;
	
	public $isMultilingual = 0;
	public $content = [];
	public $subject = [];
	public $footer = [];
	
	public $jCoins = 1;
	public $period = 1;
	public $periodUnit = 'day';
	public $redeemLimit = 0;
	public $raffle = 0;
	
	public $codeRedeemLimit = 1;
	public $codeUserLimit = 1;
	public $codePrefix = '';
	public $codeNumber = 1;
	
	public $expirationStatus = 0;
	public $expirationDate = '';
	public $expirationDateObj;
	
	public $publicationStatus = 0;
	public $publicationDate = '';
	public $publicationDateObj;
	public $notify = 1;
	
	public $conditions = [];
	
	public $imageID = [];
	public $images = [];
	
	public $categoryIDs = [];
	public $categoryList;
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		$this->availablePeriods['day'] = 'day';
		$this->availablePeriods['week'] = 'week';
		$this->availablePeriods['month'] = 'month';
		
		$this->availableTypes = new JCoinsVoucherTypeList();
		$this->availableTypes->readObjects();
		
		// read categories
		$excludedCategoryIDs = array_diff(JCoinsVoucherCategory::getAccessibleCategoryIDs(), JCoinsVoucherCategory::getAccessibleCategoryIDs(['canUseCategory']));
		$categoryTree = new JCoinsVoucherCategoryNodeTree('com.uz.jcoins.voucher.category', 0, false, $excludedCategoryIDs);
		$this->categoryList = $categoryTree->getIterator();
		$this->categoryList->setMaxDepth(0);
		
		// check pre-selected categories and add parent categories
		foreach ($this->categoryIDs as $categoryID) {
			$category = JCoinsVoucherCategory::getCategory($categoryID);
			if ($category) {
				$this->categoryIDs[] = $category->categoryID;
				
				if ($category->parentCategoryID) {
					$this->categoryIDs[] = $category->parentCategoryID;
				}
			}
		}
		$this->categoryIDs = array_unique($this->categoryIDs);
		
		// conditions
		$objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('com.uz.jcoins.voucher.condition');
		foreach ($objectTypes as $objectType) {
			if (!$objectType->conditiongroup) continue;
			
			if (!isset($groupedObjectTypes[$objectType->conditiongroup])) {
				$groupedObjectTypes[$objectType->conditiongroup] = [];
			}
			
			$groupedObjectTypes[$objectType->conditiongroup][$objectType->objectTypeID] = $objectType;
		}
		$this->conditions = $groupedObjectTypes;
		
		parent::readData();
	}
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		// languages
		$this->isMultilingual = 0;
		$this->availableLanguages = LanguageFactory::getInstance()->getLanguages();
		if (count($this->availableLanguages) > 1) $this->isMultilingual = 1;
		
		// categories
		if (isset($_REQUEST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray($_REQUEST['categoryIDs']);
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'action' => 'add',
				
				'availableLanguages' => $this->availableLanguages,
				'availablePeriods' => $this->availablePeriods,
				'availableTypes' => $this->availableTypes,
				
				'title' => $this->title,
				
				'isMultilingual' => $this->isMultilingual,
				'content' => $this->content,
				'subject' => $this->subject,
				'footer' => $this->footer,
				'imageID' => $this->imageID,
				'images' => $this->images,
				
				'typeID' => $this->typeID,
				
				'jCoins' => $this->jCoins,
				'period' => $this->period,
				'periodUnit' => $this->periodUnit,
				'redeemLimit' => $this->redeemLimit,
				'codeRedeemLimit' => $this->codeRedeemLimit,
				'codeUserLimit' => $this->codeUserLimit,
				'codePrefix' => $this->codePrefix,
				'codeNumber' => $this->codeNumber,
				
				'raffle' => $this->raffle,
				
				'expirationStatus' => $this->expirationStatus,
				'expirationDate' => $this->expirationDate,
				
				'publicationStatus' => $this->publicationStatus,
				'publicationDate' => $this->publicationDate,
				'notify' => $this->notify,
				
				'groupedObjectTypes' => $this->conditions,
				
				'categoryIDs' => $this->categoryIDs,
				'categoryList' => $this->categoryList
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// general
		$this->raffle = 0;
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		
		if (isset($_POST['typeID'])) $this->typeID = intval($_POST['typeID']);
		
		if (isset($_POST['jCoins'])) $this->jCoins = intval($_POST['jCoins']);
		if (isset($_POST['period'])) $this->period = intval($_POST['period']);
		if (isset($_POST['periodUnit'])) $this->periodUnit = StringUtil::trim($_POST['periodUnit']);
		
		if (isset($_POST['redeemLimit'])) $this->redeemLimit = intval($_POST['redeemLimit']);
		if (isset($_POST['codeRedeemLimit'])) $this->codeRedeemLimit = intval($_POST['codeRedeemLimit']);
		if (isset($_POST['codeUserLimit'])) $this->codeUserLimit = intval($_POST['codeUserLimit']);
		if (isset($_POST['codePrefix'])) $this->codePrefix = StringUtil::trim($_POST['codePrefix']);
		if (isset($_POST['codeNumber'])) $this->codeNumber = intval($_POST['codeNumber']);
		
		if (isset($_POST['raffle'])) $this->raffle = 1;
		
		if (isset($_POST['content']) && is_array($_POST['content'])) $this->content = ArrayUtil::trim($_POST['content']);
		if (isset($_POST['subject']) && is_array($_POST['subject'])) $this->subject = ArrayUtil::trim($_POST['subject']);
		if (isset($_POST['footer']) && is_array($_POST['footer'])) $this->footer = ArrayUtil::trim($_POST['footer']);
		
		if (WCF::getSession()->getPermission('admin.content.cms.canUseMedia')) {
			if (isset($_POST['imageID']) && is_array($_POST['imageID'])) $this->imageID = ArrayUtil::toIntegerArray($_POST['imageID']);
			
			$this->readImages();
		}
		
		$this->expirationStatus = 0;
		if (isset($_POST['expirationStatus'])) $this->expirationStatus = intval($_POST['expirationStatus']);
		
		if ($this->expirationStatus == 1 && isset($_POST['expirationDate'])) {
			$this->expirationDate = $_POST['expirationDate'];
			$this->expirationDateObj = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $this->expirationDate);
		}
		
		$this->publicationStatus = $this->notify = 0;
		if (isset($_POST['publicationStatus'])) $this->publicationStatus = intval($_POST['publicationStatus']);
		if (isset($_POST['notify'])) $this->notify = intval($_POST['notify']);
		
		if ($this->publicationStatus == 1 && isset($_POST['publicationDate'])) {
			$this->publicationDate = $_POST['publicationDate'];
			$this->publicationDateObj = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $this->publicationDate);
		}
		
		// conditions
		foreach ($this->conditions as $conditions) {
			foreach ($conditions as $condition) {
				$condition->getProcessor()->readFormParameters();
			}
		}
	}
	
	/**
	 * Reads the box images.
	 */
	protected function readImages() {
		if (!empty($this->imageID)) {
			$mediaList = new ViewableMediaList();
			$mediaList->setObjectIDs($this->imageID);
			$mediaList->readObjects();
				
			foreach ($this->imageID as $languageID => $imageID) {
				$image = $mediaList->search($imageID);
				if ($image !== null && $image->isImage) {
					$this->images[$languageID] = $image;
				}
			}
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();
		
		// texts
		$content = [];
		if ($this->isMultilingual) {
			foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
				$content[$language->languageID] = [
						'content' => !empty($this->content[$language->languageID]) ? $this->content[$language->languageID] : '',
						'subject' => !empty($this->subject[$language->languageID]) ? $this->subject[$language->languageID] : '',
						'footer' => !empty($this->footer[$language->languageID]) ? $this->footer[$language->languageID] : '',
						'htmlInputProcessor' => isset($this->htmlInputProcessors[$language->languageID]) ? $this->htmlInputProcessors[$language->languageID] : null,
						'imageID' => !empty($this->imageID[$language->languageID]) ? $this->imageID[$language->languageID] : null
				];
			}
		}
		else {
			$content[0] = [
					'content' => !empty($this->content[0]) ? $this->content[0] : '',
					'subject' => !empty($this->subject[0]) ? $this->subject[0] : '',
					'footer' => !empty($this->footer[0]) ? $this->footer[0] : '',
					'htmlInputProcessor' => isset($this->htmlInputProcessors[0]) ? $this->htmlInputProcessors[0] : null,
					'imageID' => !empty($this->imageID[0]) ? $this->imageID[0] : null
			];
		}
		
		// data
		$data = [
				'title' => $this->title,
				'isDisabled' => $this->isDisabled,
				'isMultilingual' => $this->isMultilingual,
				'time' => TIME_NOW,
				'changeTime' => TIME_NOW,
				
				'userID' => WCF::getUser()->userID,
				'username' => WCF::getUser()->username,
				
				'typeID' => $this->typeID,
				'typeDes' => $this->typeID ? $this->type->typeTitle : '',
				
				'jCoins' => $this->jCoins,
				'period' => $this->period,
				'periodUnit' => $this->periodUnit,
				'redeemLimit' => $this->redeemLimit,
				'redeemLimitStart' => $this->redeemLimit,
				'codeRedeemLimit' => $this->codeRedeemLimit,
				'codeUserLimit' => $this->codeUserLimit,
				'codePrefix' => $this->codePrefix,
				'codeNumber' => $this->codeNumber,
				
				'raffle' => $this->raffle,
				
				'expirationStatus' => $this->expirationStatus,
				'expirationDate' => $this->expirationStatus == 1 ? $this->expirationDateObj->getTimestamp() : 0,
				
				'publicationStatus' => $this->publicationStatus,
				'publicationDate' => $this->publicationStatus == 1 ? $this->publicationDateObj->getTimestamp() : 0,
				'notify' => $this->notify
		];
		
		// save
		$objectAction = new JCoinsVoucherAction([], 'create', [
				'data' => array_merge($this->additionalFields, $data),
				'content' => $content,
				'categoryIDs' => $this->categoryIDs
		]);
		$objectAction->executeAction();
		
		$returnValues = $objectAction->getReturnValues();
		$this->voucherID = $returnValues['returnValues']->voucherID;
		$voucher = $returnValues['returnValues'];
		
		// transform conditions array into one-dimensional array
		$conditions = [];
		foreach ($this->conditions as $groupedObjectTypes) {
			$conditions = array_merge($conditions, $groupedObjectTypes);
		}
		ConditionHandler::getInstance()->createConditions($this->voucherID, $conditions);
		
		// Reset values
		$this->title = '';
		$this->isDisabled = 1;
		
		$this->content = [];
		$this->subject = [];
		$this->footer = [];
		$this->images = [];
		$this->imageID = [];
		
		$this->type = null;
		$this->typeID = 0;
		
		$this->jCoins = 1;
		$this->period = 1;
		$this->periodUnit = 'day';
		$this->redeemLimit = 0;
		$this->codeRedeemLimit = 1;
		$this->codeUserLimit = 1;
		$this->codePrefix = '';
		$this->codeNumber = 1;
		
		$this->raffle = 0;
		
		$this->expirationDate = '';
		$this->expirationStatus = 0;
		
		$this->publicationDate = '';
		$this->publicationStatus = 0;
		$this->notify = 1;
		
		// reset conditions
		foreach ($this->conditions as $conditions) {
			foreach ($conditions as $condition) {
				$condition->getProcessor()->reset();
			}
		}
		
		// create codes
		if ($voucher->typeDes == 'code') {
			WCF::getDB()->beginTransaction();
			$sql = "INSERT INTO	wcf".WCF_N."_jcoins_voucher_to_code
						(voucherID, code, redeemed)
					VALUES		(?, ?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			for ($i = 0; $i < $voucher->codeNumber; $i++) {
				$code = substr(md5(uniqid(rand(), true)), 0, 6);
				if ($voucher->codePrefix != '') $code = $voucher->codePrefix . '-' . $code;
				$statement->execute([$voucher->voucherID, $code, 0]);
			}
			WCF::getDB()->commitTransaction();
		}
		
		$this->saved();
		
		// Show success message
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();
		
		// General
		// title required, max 80 chars
		if (empty($this->title)) throw new UserInputException('title', 'required');
		if (mb_strlen($this->title) > 80) throw new UserInputException('title', 'tooLong');
		
		// publication status
		if ($this->publicationStatus != 0 && $this->publicationStatus != 1) {
			throw new UserInputException('publicationStatus');
		}
		if ($this->publicationStatus == 1) {
			if (empty($this->publicationDate)) {
				throw new UserInputException('publicationDate');
			}
			
			if (!$this->publicationDateObj || $this->publicationDateObj->getTimestamp() < TIME_NOW) {
				throw new UserInputException('publicationDate', 'invalid');
			}
		}
		
		// validate category ids
		if (empty($this->categoryIDs)) {
			throw new UserInputException('categoryIDs');
		}
		$categories = [];
		foreach ($this->categoryIDs as $categoryID) {
			$category = CategoryHandler::getInstance()->getCategory($categoryID);
			if ($category === null) throw new UserInputException('categoryIDs');
			
			$category = new JCoinsVoucherCategory($category);
			if (!$category->isAccessible() || !$category->getPermission('canUseCategory')) throw new UserInputException('categoryIDs');
			$categories[] = $category;
		}
		
		// type must exist
		if (!$this->typeID) throw new UserInputException('typeID', 'missing');
		$this->type = JCoinsVoucherType::getTypeByID($this->typeID);
		
		// expiration status
		if ($this->expirationStatus != 0 && $this->expirationStatus != 1) {
			throw new UserInputException('expirationStatus');
		}
		if ($this->expirationStatus == 1) {
			if (empty($this->expirationDate)) {
				throw new UserInputException('expirationDate');
			}
			
			if (!$this->expirationDateObj || $this->expirationDateObj->getTimestamp() < TIME_NOW) {
				throw new UserInputException('expirationDate', 'invalid');
			}
		}
		
		// cleanup type-related data
		if ($this->type->typeTitle == 'birthday' || $this->type->typeTitle == 'membership' || $this->type->typeTitle == 'registration') {
			$this->redeemLimit = 0;
			$this->raffle = 0;
			$this->period = 1;
			$this->periodUnit = 'day';
		}
		
		if ($this->type->typeTitle == 'normal') {
			$this->period = 1;
			$this->periodUnit = 'day';
		}
		
		if ($this->type->typeTitle == 'code') {
			$this->notify = 0;
			$this->raffle = 0;
			$this->period = 1;
			$this->periodUnit = 'day';
			
			// max length
			if (mb_strlen($this->codePrefix) > 5) throw new UserInputException('codePrefix', 'tooLong');
		}
		
		// conditions
		foreach ($this->conditions as $conditions) {
			foreach ($conditions as $condition) {
				$condition->getProcessor()->validate();
			}
		}
		
		// some texts must exist
		if ($this->isMultilingual) {
			foreach ($this->availableLanguages as $language) {
			//	if (empty($this->subject[$language->languageID])) throw new UserInputException('subject'.$language->languageID);
			//	if (empty($this->footer[$language->languageID])) throw new UserInputException('footer'.$language->languageID);
				if (empty($this->content[$language->languageID])) throw new UserInputException('content'.$language->languageID);
				
				$this->htmlInputProcessors[$language->languageID] = new HtmlInputProcessor();
				$this->htmlInputProcessors[$language->languageID]->process($this->content[$language->languageID], 'com.uz.jcoins.voucher.content', 0);
			}
		}
		else {
			if (empty($this->content[0])) throw new UserInputException('content');
			
			$this->htmlInputProcessors[0] = new HtmlInputProcessor();
			$this->htmlInputProcessors[0]->process($this->content[0], 'com.uz.jcoins.voucher.content', 0);
		}
	}
}
