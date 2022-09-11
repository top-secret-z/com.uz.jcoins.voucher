<?php
namespace wcf\acp\form;
use wcf\data\jcoins\voucher\JCoinsVoucher;
use wcf\data\jcoins\voucher\JCoinsVoucherAction;
use wcf\form\AbstractForm;
use wcf\system\condition\ConditionHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\DateUtil;

/**
 * Shows the JCoins Voucher edit form.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherEditForm extends JCoinsVoucherAddForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.voucherJCoins.list';
	
	// voucher data
	public $voucherID = 0;
	public $voucher = null;
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		if (!empty($_POST) && !WCF::getSession()->getPermission('admin.content.cms.canUseMedia')) {
			foreach ($this->voucher->getItemContents() as $languageID => $content) {
				$this->imageID[$languageID] = $content->imageID;
			}
			
			$this->readImages();
		}
		
		parent::readData();
		
		if (empty($_POST)) {
			$this->title = $this->voucher->title;
			$this->isDisabled = $this->voucher->isDisabled;
			
			$this->typeID = $this->voucher->typeID;
			
			$this->jCoins = $this->voucher->jCoins;
			$this->period = $this->voucher->period;
			$this->periodUnit = $this->voucher->periodUnit;
			$this->redeemLimit = $this->voucher->redeemLimit;
			$this->codeRedeemLimit = $this->voucher->codeRedeemLimit;
			$this->codeUserLimit = $this->voucher->codeUserLimit;
			$this->codePrefix = $this->voucher->codePrefix;
			$this->codeNumber = $this->voucher->codeNumber;
			
			$this->raffle = $this->voucher->raffle;
			
			$this->expirationStatus = $this->voucher->expirationStatus;
			if ($this->voucher->expirationDate) {
				$dateTime = DateUtil::getDateTimeByTimestamp($this->voucher->expirationDate);
				$dateTime->setTimezone(WCF::getUser()->getTimeZone());
				$this->expirationDate = $dateTime->format('c');
			}
			
			$this->publicationStatus = $this->voucher->publicationStatus;
			if ($this->voucher->publicationDate) {
				$dateTime = DateUtil::getDateTimeByTimestamp($this->voucher->publicationDate);
				$dateTime->setTimezone(WCF::getUser()->getTimeZone());
				$this->publicationDate = $dateTime->format('c');
			}
			$this->notify = $this->voucher->notify;
			
			foreach ($this->voucher->getCategories() as $category) {
				$this->categoryIDs[] = $category->categoryID;
			}
			
			foreach ($this->voucher->getItemContents() as $languageID => $content) {
				$this->content[$languageID] = $content->content;
				$this->subject[$languageID] = $content->subject;
				$this->footer[$languageID] = $content->footer;
				$this->imageID[$languageID] = $content->imageID;
			}
			
			$this->readImages();
			
			// conditions
			$conditions = $this->voucher->getConditions();
			foreach ($conditions as $condition) {
				$this->conditions[$condition->getObjectType()->conditiongroup][$condition->objectTypeID]->getProcessor()->setData($condition);
			}
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['categoryIDs']) && is_array($_REQUEST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray($_REQUEST['categoryIDs']);
		
		if (isset($_REQUEST['id'])) $this->voucherID = intval($_REQUEST['id']);
		$this->voucher = new JCoinsVoucher($this->voucherID);
		if (!$this->voucher->voucherID) {
			throw new IllegalLinkException();
		}
		
		if ($this->voucher->isMultilingual) $this->isMultilingual = 1;
		
		if (!WCF::getSession()->getPermission('admin.jcoins.voucher.canManage')) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'action' => 'edit',
				'voucher' => $this->voucher,
				'voucherID' => $this->voucher->voucherID
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		AbstractForm::save();
		
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
		// check whether it was blocked in the meantime
		$voucher = new JCoinsVoucher($this->voucher->voucherID);
		$blocked = 0;
		if ($voucher->isBlocked || $this->voucher->isBlocked) $blocked = 1;
		
		if ($blocked) {
			$data = [
					'title' => $this->title,
					'changeTime' => TIME_NOW,
					
					'userID' => WCF::getUser()->userID,
					'username' => WCF::getUser()->username,
			];
		}
		else {
			$data = [
					'title' => $this->title,
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
		}
		
		$this->objectAction = new JCoinsVoucherAction([$this->voucher], 'update', [
				'data' => array_merge($this->additionalFields, $data), 
				'content' => $content,
				'categoryIDs' => $this->categoryIDs
		]);
		$this->objectAction->executeAction();
		
		// transform conditions array into one-dimensional array
		if (!$blocked) {
			$conditions = [];
			foreach ($this->conditions as $groupedObjectTypes) {
				$conditions = array_merge($conditions, $groupedObjectTypes);
			}
			ConditionHandler::getInstance()->updateConditions($this->voucher->voucherID, $this->voucher->getConditions(), $conditions);
		}
		
		// delete / create codes
		if (!$blocked) {
			$sql = "DELETE FROM wcf".WCF_N."_jcoins_voucher_to_code
					WHERE		voucherID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$this->voucher->voucherID]);
			
			if ($this->type->typeTitle == 'code') {
				WCF::getDB()->beginTransaction();
				$sql = "INSERT INTO	wcf".WCF_N."_jcoins_voucher_to_code
							(voucherID, code, redeemed)
						VALUES		(?, ?, ?)";
				$statement = WCF::getDB()->prepareStatement($sql);
				for ($i = 0; $i < $this->codeNumber; $i++) {
					$code = substr(md5(uniqid(rand(), true)),0,6);
					if ($this->codePrefix != '') $code = $this->codePrefix . '-' . $code;
					$statement->execute([$this->voucherID, $code, 0]);
				}
				WCF::getDB()->commitTransaction();
			}
		}
		
		$this->saved();
		
		// show success
		WCF::getTPL()->assign([
				'success' => true
		]);
	}
}
