<?php
namespace wcf\system\cronjob;
use wcf\data\jcoins\voucher\JCoinsVoucherEditor;
use wcf\data\jcoins\voucher\JCoinsVoucherList;
use wcf\data\jcoins\voucher\log\JCoinsVoucherLogEditor;
use wcf\data\user\UserList;
use wcf\data\cronjob\Cronjob;
use wcf\system\user\notification\object\JCoinsVoucherNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;

/**
 * Publishes JCoins vouchers.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherPublicationCronjob extends AbstractCronjob {
	/**
	 * @inheritDoc
	 */
	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);
		
		if (!MODULE_JCOINS_VOUCHER) return;
		
		// get voucher to be published
		// anniversaries
		$voucherList = new JCoinsVoucherList();
		$voucherList->getConditionBuilder()->add('isDisabled = ?', [0]);
		$voucherList->getConditionBuilder()->add('isPublished = ?', [0]);
		$voucherList->getConditionBuilder()->add('isExpired = ?', [0]);
		$voucherList->getConditionBuilder()->add('(publicationStatus = ? OR publicationDate < ?)', [0, TIME_NOW]);
		$voucherList->getConditionBuilder()->add('(typeDes = ? OR typeDes = ? OR typeDes = ?)', ['birthday', 'membership', 'registration']);
		$voucherList->readObjects();
		$vouchers = $voucherList->getObjects();
		if (!empty($vouchers)) {
			foreach ($vouchers as $voucher) {
				$voucherEditor = new JCoinsVoucherEditor($voucher);
				$voucherEditor->update(['isPublished' => 1]);
				
				// log
				JCoinsVoucherLogEditor::create([
						'voucher' => $voucher,
						'action' => 'published'
				]);
			}
		}
		
		// all others
		$voucherList = new JCoinsVoucherList();
		$voucherList->getConditionBuilder()->add('isDisabled = ?', [0]);
		$voucherList->getConditionBuilder()->add('isPublished = ?', [0]);
		$voucherList->getConditionBuilder()->add('isExpired = ?', [0]);
		$voucherList->getConditionBuilder()->add('(publicationStatus = ? OR publicationDate < ?)', [0, TIME_NOW]);
		$voucherList->sqlLimit = 1;
		$voucherList->readObjects();
		$vouchers = $voucherList->getObjects();
		
		if (empty($vouchers)) return;
		
		$voucher = reset($vouchers);
		$voucherEditor = new JCoinsVoucherEditor($voucher);
		
		// calculate and save nextDate
		if ($voucher->typeDes == 'recurring') {
			if ($voucher->publicationStatus) {
				$base = $voucher->publicationDate;
			}
			else {
				$base = TIME_NOW;
			}
			
			switch ($voucher->periodUnit) {
				case 'day':
					$next = $base + $voucher->period * 86400;
					break;
				case 'week':
					$next = $base + $voucher->period * 86400 * 7;
					break;
				case 'month':
					$next = strtotime('+'.$voucher->period.' month', $base);
					break;
			}
			$voucherEditor->update(['nextDate' => $next]);
		}
		
		// get users, unless code
		$userList = new UserList();
		$conditions = $voucher->getConditions();
		foreach ($conditions as $condition) {
			$condition->getObjectType()->getProcessor()->addUserCondition($condition, $userList);
		}
		$userList->readObjects();
		$users = $userList->getObjects();
		$userIDs = [];
		
		if (!empty($users)) {
			foreach ($users as $user) {
				$userIDs[] = $user->userID;
			}
		}
		
		// update voucher
		$voucherEditor->update(['isPublished' => 1]);
		
		// log
		JCoinsVoucherLogEditor::create([
				'voucher' => $voucher,
				'action' => 'published'
		]);
		
		if (empty($userIDs)) return;
		
		// check redeem limit / raffle
		$count = count($userIDs);
		
		if ($voucher->typeDes !== 'code') {
			if ($voucher->redeemLimit > 0 && $voucher->redeemLimit < $count && $voucher->raffle) {
				shuffle($userIDs);
				$userIDs = array_slice($userIDs, 0, $voucher->redeemLimit);
			}
		}
		
		// update voucher to user
		$count = 0;
		while (1) {
			$storeIDs = array_slice($userIDs, $count * 500, 500);
			
			if (empty($storeIDs)) break;
			$count ++;
			
			WCF::getDB()->beginTransaction();
			$sql = "INSERT INTO	wcf".WCF_N."_jcoins_voucher_to_user
						(voucherID, userID)
					VALUES		(?, ?)
					ON DUPLICATE KEY
					UPDATE		redeemDate = 0";
			$statement = WCF::getDB()->prepareStatement($sql);
			foreach ($storeIDs as $userID) {
				$statement->execute([$voucher->voucherID, $userID]);
			}
			WCF::getDB()->commitTransaction();
		}
		
		// send notification
		if ($voucher->notify) {
			foreach ($userIDs as $userID) {
				UserNotificationHandler::getInstance()->fireEvent('jCoinsVoucher', 'com.uz.jcoins.voucher.notification', new JCoinsVoucherNotificationObject($voucher), [$userID]);
			}
		}
		
		// log
		JCoinsVoucherLogEditor::create([
				'voucher' => $voucher,
				'action' => 'issued',
				'detail' => count($userIDs)
		]);
	}
}
