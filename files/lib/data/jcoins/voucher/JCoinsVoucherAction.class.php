<?php
namespace wcf\data\jcoins\voucher;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\condition\ConditionList;
use wcf\data\IToggleAction;
use wcf\data\jcoins\voucher\JCoinsVoucher;
use wcf\data\jcoins\voucher\JCoinsVoucherEditor;
use wcf\data\jcoins\voucher\content\JCoinsVoucherContent;
use wcf\data\jcoins\voucher\content\JCoinsVoucherContentEditor;
use wcf\data\jcoins\voucher\content\JCoinsVoucherContentList;
use wcf\data\jcoins\voucher\log\JCoinsVoucherLogEditor;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\system\cache\builder\ConditionCacheBuilder;
use wcf\system\condition\ConditionHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\request\LinkHandler;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Executes JCoins Voucher actions.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherAction extends AbstractDatabaseObjectAction implements IToggleAction {
	/**
	 * @inheritDoc
	 */
	protected $className = JCoinsVoucherEditor::class;
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = ['admin.jcoins.voucher.canManage'];
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsUpdate = ['admin.jcoins.voucher.canManage'];
	
	/**
	 * @inheritDoc
	 */
	protected $requireACP = ['create', 'delete', 'toggle', 'update'];
	
	public $voucher = null;
	
	/**
	 * @inheritDoc
	 */
	public function create() {
		$voucher = parent::create();
		
		// save voucher content
		if (!empty($this->parameters['content'])) {
			foreach ($this->parameters['content'] as $languageID => $content) {
				if (!empty($content['htmlInputProcessor'])) {
					$content['content'] = $content['htmlInputProcessor']->getHtml();
				}
				
				$voucherContent = JCoinsVoucherContentEditor::create([
						'voucherID' => $voucher->voucherID,
						'languageID' => $languageID ?: null,
						'subject' => $content['subject'],
						'footer' => $content['footer'],
						'content' => $content['content'],
						'imageID' => $content['imageID']
				]);
				$voucherContentEditor = new JCoinsVoucherContentEditor($voucherContent);
				
				// save embedded objects
				if (!empty($content['htmlInputProcessor'])) {
					$content['htmlInputProcessor']->setObjectID($voucherContent->contentID);
					if (MessageEmbeddedObjectManager::getInstance()->registerObjects($content['htmlInputProcessor'])) {
						$voucherContentEditor->update(['hasEmbeddedObjects' => 1]);
					}
				}
			}
		}
		
		// save categories
		$voucherEditor = new JCoinsVoucherEditor($voucher);
		$voucherEditor->updateCategoryIDs($this->parameters['categoryIDs']);
		$voucherEditor->setCategoryIDs($this->parameters['categoryIDs']);
		
		// log
		JCoinsVoucherLogEditor::create([
				'voucher' => $voucher,
				'action' => 'created'
		]);
		
		return $voucher;
	}
	
	/**
	 * @inheritDoc
	 */
	public function delete() {
		// delete any conditions
		ConditionHandler::getInstance()->deleteConditions('com.uz.jcoins.voucher.condition', $this->objectIDs);
		
		// log
		foreach ($this->objects as $voucher) {
			JCoinsVoucherLogEditor::create([
					'voucher' => $voucher,
					'action' => 'deleted'
			]);
		}
		
		return parent::delete();
	}
	
	/**
	 * @inheritDoc
	 */
	public function update() {
		parent::update();
		
		foreach ($this->getObjects() as $voucher) {
			// handle categories
			if (isset($this->parameters['categoryIDs'])) {
				$voucher->updateCategoryIDs($this->parameters['categoryIDs']);
			}
			
			// update voucher content
			if (!empty($this->parameters['content'])) {
				foreach ($this->parameters['content'] as $languageID => $content) {
					if (!empty($content['htmlInputProcessor'])) {
						$content['content'] = $content['htmlInputProcessor']->getHtml();
					}
					
					$voucherContent = JCoinsVoucherContent::getItemContent($voucher->voucherID, ($languageID ?: null));
					$voucherContentEditor = null;
					if ($voucherContent !== null) {
						// update
						$voucherContentEditor = new JCoinsVoucherContentEditor($voucherContent);
						$voucherContentEditor->update([
								'content' => $content['content'],
								'subject' => $content['subject'],
								'footer' => $content['footer'],
								'imageID' => $content['imageID']
						]);
					}
					else {
						$voucherContent = JCoinsVoucherContentEditor::create([
								'voucherID' => $voucher->voucherID,
								'languageID' => $languageID ?: null,
								'content' => $content['content'],
								'subject' => $content['subject'],
								'footer' => $content['footer'],
								'imageID' => $content['imageID']
						]);
						$voucherContentEditor = new JCoinsVoucherContentEditor($voucherContent);
					}
					
					// save embedded objects
					if (!empty($content['htmlInputProcessor'])) {
						$content['htmlInputProcessor']->setObjectID($voucherContent->contentID);
						if ($voucherContent->hasEmbeddedObjects != MessageEmbeddedObjectManager::getInstance()->registerObjects($content['htmlInputProcessor'])) {
							$voucherContentEditor->update(['hasEmbeddedObjects' => $voucherContent->hasEmbeddedObjects ? 0 : 1]);
						}
					}
				}
			}
			
			// log
			JCoinsVoucherLogEditor::create([
					'voucher' => $voucher,
					'action' => 'updated'
			]);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function validateToggle() {
		parent::validateUpdate();
	}
	
	/**
	 * @inheritDoc
	 */
	public function toggle() {
		foreach ($this->objects as $voucher) {
			if ($voucher->isDisabled) {
				$voucher->update([
						'isDisabled' => 0,
						'isBlocked' => 1
				]);
			}
			else {
				$voucher->update([
						'isDisabled' => 1,
						'isBlocked' => $voucher->isPublished ? 1 : 0
				]);
			}
		}
	}
	
	/**
	 * Validates the copy action.
	 */
	public function validateCopy() {
		$this->voucher = new JCoinsVoucher($this->parameters['objectID']);
		if (!$this->voucher->voucherID) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * Executes the copy action.
	 */
	public function copy() {
		$data = $this->voucher->getData();
		$oldVoucherID = $data['voucherID'];
		unset($data['voucherID']);
		
		// copy voucher, set to disable, set time
		$data['isBlocked'] = 0;
		$data['isDisabled'] = 1;
		$data['time'] = TIME_NOW;
		$data['changeTime'] = TIME_NOW;
		$data['isExpired'] = 0;
		$data['isPublished'] = 0;
		$data['redeemed'] = 0;
		$data['redeemLimitStart'] = $data['redeemLimit'];
		$data['title'] = substr($data['title'], 0, 75) . ' (2)';
		$this->parameters['data'] = $data;
		
		// copy categories
		$categoryIDs = [];
		$temp = $this->voucher->getCategories();
		if (count($temp)) {
			foreach ($temp as $category) {
				$categoryIDs[] = $category->categoryID;
			}
		}
		$this->parameters['categoryIDs'] = $categoryIDs;
		
		$voucher = $this->create();
		
		// copy conditions
		$definitionIDs = [];
		$sql = "SELECT		definitionID
				FROM		wcf".WCF_N."_object_type_definition
				WHERE		definitionName LIKE ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(['com.uz.jcoins.voucher.condition%']);
		while ($row = $statement->fetchArray()) {
			$definitionIDs[] = $row['definitionID'];
		}
		
		foreach($definitionIDs as $definitionID) {
			$objectTypeIDs = [];
			$sql = "SELECT		objectTypeID
					FROM		wcf".WCF_N."_object_type
					WHERE		definitionID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$definitionID]);
			while ($row = $statement->fetchArray()) {
				$objectTypeIDs[] = $row['objectTypeID'];
			}
			
			$conditionList = new ConditionList();
			$conditionList->getConditionBuilder()->add('objectTypeID IN (?)', [$objectTypeIDs]);
			$conditionList->getConditionBuilder()->add('objectID = ?', [$oldVoucherID]);
			$conditionList->readObjects();
			$conditions = $conditionList->getObjects();
				
			if (count($conditions)) {
				WCF::getDB()->beginTransaction();
				$sql = "INSERT INTO wcf".WCF_N."_condition
								(objectID, objectTypeID, conditionData)
						VALUES	(?, ?, ?)";
				$statement = WCF::getDB()->prepareStatement($sql);
				
				foreach($conditions as $condition) {
					$statement->execute([$voucher->voucherID, $condition->objectTypeID, serialize($condition->conditionData)]);
				}
				WCF::getDB()->commitTransaction();
			}
		}
		
		ConditionCacheBuilder::getInstance()->reset();
		
		// copy content
		$contentList = new JCoinsVoucherContentList();
		$contentList->getConditionBuilder()->add('voucherID = ?', [$oldVoucherID]);
		$contentList->readObjects();
		$contents = $contentList->getObjects();
		
		WCF::getDB()->beginTransaction();
		$sql = "INSERT INTO wcf".WCF_N."_jcoins_voucher_content
							(voucherID, languageID, content, subject, footer, imageID, hasEmbeddedObjects)
				VALUES	(?, ?, ?, ?, ?, ?, ?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		
		foreach($contents as $content) {
			$statement->execute([$voucher->voucherID, $content->languageID, $content->content, $content->subject, $content->footer, $content->imageID, $content->hasEmbeddedObjects]);
		}
		WCF::getDB()->commitTransaction();
		
		// log
		JCoinsVoucherLogEditor::create([
				'voucher' => $voucher,
				'action' => 'created'
		]);
		
		return [
				'redirectURL' => LinkHandler::getInstance()->getLink('JCoinsVoucherEdit', [
						'id' => $voucher->voucherID
				])
		];
	}
	
	/**
	 * Validates the get redeem dialog action.
	 */
	public function validateGetRedeemDialog() {
		$id = $this->parameters['voucher'];
		$this->voucher = new JCoinsVoucher($id);
		if (!$this->voucher->voucherID) throw new IllegalLinkException();
		if (!$this->voucher->canRedeem()) throw new PermissionDeniedException();
	}
	
	/**
	 * Executes the get redeem dialog action.
	 */
	public function getRedeemDialog() {
		
		return [
				'template' => WCF::getTPL()->fetch('jCoinsVoucherDialog')
		];
	}
	
	/**
	 * Validates the redeem action.
	 */
	public function validateRedeem() {
		$id = $this->parameters['voucher'];
		$this->voucher = new JCoinsVoucher($id);
		if (!$this->voucher->voucherID) throw new IllegalLinkException();
		if (!$this->voucher->canRedeem()) throw new PermissionDeniedException();
	}
	
	/**
	 * Executes the redeem action.
	 */
	public function redeem() {
		$redeemed = $this->voucher->redeemed + 1;
		
		// update voucher
		$voucherEditor = new JCoinsVoucherEditor($this->voucher);
		$voucherEditor->updateCounters(['redeemed' => 1]);
		
		// update voucher to user
		$user = WCF::getUser();
		
		$sql = "UPDATE	wcf".WCF_N."_jcoins_voucher_to_user
				SET		username = ?, lastDate = ?, redeemDate = ?, redeemed = redeemed + 1, jCoins = ?
				WHERE	voucherID = ? AND userID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$user->username, TIME_NOW, TIME_NOW, $this->voucher->jCoins, $this->voucher->voucherID, $user->userID]);
		
		// update voucher count for the user
		$userAction = new UserAction([$user->userID], 'update', [
				'counters' => [
						'jCoinsVouchers' => 1
				]
		]);
		$userAction->executeAction();
		
		// statistics
		$sql = "INSERT INTO wcf".WCF_N."_jcoins_voucher_redemption
					(time)
				VALUES	(?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([TIME_NOW]);
		
		// expire voucher if required
		$expired = 0;
		if ($this->voucher->redeemLimit > 0 && $this->voucher->redeemLimit <= $redeemed) {
			$expired = 1;
			$voucherEditor->update([
					'isExpired' => 1,
					'expirationDate' => TIME_NOW
			]);
		}
		
		// get JCoins
		UserJCoinsStatementHandler::getInstance()->create('com.uz.jcoins.voucher.statement.voucher', null, [
				'amount' => $this->voucher->jCoins,
				'userID' => $user->userID,
				'voucherID' => $this->voucher->voucherID
		]);
		
		// log
		JCoinsVoucherLogEditor::create([
				'voucher' => $this->voucher,
				'action' => 'redeemed',
				'detail' => $this->voucher->redeemed + 1
		]);
		
		if ($expired) {
			JCoinsVoucherLogEditor::create([
					'voucher' => $this->voucher,
					'action' => 'expired'
			]);
		}
	}
	
	/**
	 * Validates the loadPreview action.
	 */
	public function validateLoadPreview() {
		$id = $this->parameters['voucherID'];
		$this->voucher = new JCoinsVoucher($id);
		if (!$this->voucher->voucherID) throw new IllegalLinkException();
	}
	
	/**
	 * Executes the loadPreview action.
	 */
	public function loadPreview() {
		$acp = 1;
		
		$content = $this->voucher->getContent($acp);
		$footer = $this->voucher->getFooter($acp);
		$subject = $this->voucher->getSubject($acp);
		
		WCF::getTPL()->assign([
				'voucher' => $this->voucher,
				'content' => $content,
				'footer' => $footer,
				'subject' => $subject
		]);
		
		return [
				'template' => WCF::getTPL()->fetch('jCoinsVoucherPreview'),
				'voucher' => $this->voucher
		];
	}
	
	/**
	 * Validates the get redeem dialog action.
	 */
	public function validateGetRedeemCodeDialog() {
		$id = $this->parameters['voucher'];
		$this->voucher = new JCoinsVoucher($id);
		if (!$this->voucher->voucherID) throw new IllegalLinkException();
		if (!$this->voucher->canRedeem()) throw new PermissionDeniedException();
	}
	
	/**
	 * Executes the get redeem code dialog action.
	 */
	public function getRedeemCodeDialog() {
		
		return [
				'template' => WCF::getTPL()->fetch('jCoinsVoucherCodeDialog')
		];
	}
	
	/**
	 * Validates the redeem code action.
	 */
	public function validateRedeemCode() {
		$id = $this->parameters['voucher'];
		$this->voucher = new JCoinsVoucher($id);
		
		if (!$this->voucher->voucherID) {
			throw new IllegalLinkException();
		}
		if (!$this->voucher->canRedeem()) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * Executes the redeem code action.
	 */
	public function redeemCode() {
		$code = $this->parameters['code'];
		
		$response = '';
		$codeCheck = $this->voucher->codeExists($code);
		$codeRedemptions = $this->voucher->codeRedeemedTimes($code);
		$userRedemptions = $this->voucher->userRedeemedTimes();
		
		if ($codeCheck === null) {
			return ['codeError' => 'invalid'];
		}
		
		if ($codeRedemptions >= $this->voucher->codeRedeemLimit) {
			return ['codeError' => 'codeRedeemLimit'];
		}
		if ($userRedemptions >= $this->voucher->codeUserLimit) {
			return ['codeError' => 'userRedeemLimit'];
		}
		if ($this->voucher->codeNumber * $this->voucher->codeRedeemLimit <= $this->voucher->redeemed) {
			return ['codeError' => 'expired'];
		}
		if ($this->voucher->redeemLimit > 0 && $this->voucher->redeemLimit <= $this->voucher->redeemed) {
			return ['codeError' => 'expired'];
		}
		
		// all ok,  redeem voucher
		$userRedeemed = $userRedemptions + 1;
		
		// update voucher
		$voucherEditor = new JCoinsVoucherEditor($this->voucher);
		$voucherEditor->updateCounters(['redeemed' => 1]);
		
		// update voucher to user
		$user = WCF::getUser();
		
		$sql = "UPDATE	wcf".WCF_N."_jcoins_voucher_to_user
				SET		username = ?, lastDate = ?, redeemDate = ?, redeemed = ?, jCoins = ?
				WHERE	voucherID = ? AND userID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$user->username, TIME_NOW, TIME_NOW, $userRedeemed, $this->voucher->jCoins * $userRedeemed, $this->voucher->voucherID, $user->userID]);
		
		// statistics
		$sql = "INSERT INTO wcf".WCF_N."_jcoins_voucher_redemption
					(time)
				VALUES	(?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([TIME_NOW]);
		
		// code
		$sql = "UPDATE	wcf".WCF_N."_jcoins_voucher_to_code
				SET		redeemed = redeemed + 1
				WHERE	voucherID = ? AND code LIKE ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->voucher->voucherID, $code]);
		
		// expire voucher if required
		$expired = 0;
		if ($this->voucher->redeemed + 1 >= $this->voucher->codeNumber * $this->voucher->codeRedeemLimit) {
			$expired = 1;
		}
		if ($this->voucher->redeemLimit && $this->voucher->redeemed + 1 >= $this->voucher->redeemLimit) {
			$expired = 1;
		}
		
		if ($expired) {
			$voucherEditor->update([
					'isExpired' => 1,
					'expirationDate' => TIME_NOW
			]);
		}
		
		// get JCoins
		UserJCoinsStatementHandler::getInstance()->create('com.uz.jcoins.voucher.statement.voucher', null, [
				'amount' => $this->voucher->jCoins,
				'userID' => $user->userID,
				'voucherID' => $this->voucher->voucherID
		]);
		
		// log
		JCoinsVoucherLogEditor::create([
				'voucher' => $this->voucher,
				'action' => 'redeemed',
				'detail' => $this->voucher->redeemed + 1
		]);
		
		if ($expired) {
			JCoinsVoucherLogEditor::create([
					'voucher' => $this->voucher,
					'action' => 'expired'
			]);
		}
		
		return ['success' => 1];
	}
	
	/**
	 * Validates the load codes action.
	 */
	public function validateLoadCodes() {
		$id = $this->parameters['voucherID'];
		$this->voucher = new JCoinsVoucher($id);
		
		if (!$this->voucher->voucherID) throw new IllegalLinkException();
	}
	
	/**
	 * Executes the load codes action.
	 */
	public function loadCodes() {
		$codes = [];
		$total = 0;
		$used = 0;
		
		$sql = "SELECT	code, redeemed
				FROM 	wcf".WCF_N."_jcoins_voucher_to_code
				WHERE	voucherID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->voucher->voucherID]);
		while ($row = $statement->fetchArray()) {
			$total ++;
			if ($row['redeemed'] >= $this->voucher->codeRedeemLimit) $used ++;
			$codes[] = [
					'code' => $row['code'],
					'redeemed' => $row['redeemed'] >= $this->voucher->codeRedeemLimit ? 1 : 0
			];
		}
		
		WCF::getTPL()->assign([
				'codes' => $codes,
				'total' => $total,
				'used' => $used,
				'link' => $this->voucher->getLink()
		]);
		
		return [
				'template' => WCF::getTPL()->fetch('jCoinsVoucherCodes')
		];
	}
}
