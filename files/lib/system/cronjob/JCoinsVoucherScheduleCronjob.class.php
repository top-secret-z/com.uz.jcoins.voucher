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
 * Schedules JCoins vouchers.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherScheduleCronjob extends AbstractCronjob {
	/**
	 * @inheritDoc
	 */
	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);
		
		if (!MODULE_JCOINS_VOUCHER) return;
		
		// expire vouchers by time
		$voucherList = new JCoinsVoucherList();
		$voucherList->getConditionBuilder()->add('isExpired = ?', [0]);
		$voucherList->getConditionBuilder()->add('(expirationStatus > ? AND expirationDate < ?)', [0, TIME_NOW]);
		$voucherList->readObjects();
		$vouchers = $voucherList->getObjects();
		if (!empty($vouchers)) {
			foreach ($vouchers as $voucher) {
				$voucherEditor = new JCoinsVoucherEditor($voucher);
				$voucherEditor->update(['isExpired' => 1]);
				
				// log
				JCoinsVoucherLogEditor::create([
						'voucher' => $voucher,
						'action' => 'expired'
				]);
			}
		}
		
		// expire vouchers by redemption
		$voucherList = new JCoinsVoucherList();
		$voucherList->getConditionBuilder()->add('isExpired = ?', [0]);
		$voucherList->getConditionBuilder()->add('redeemLimit > ?', [0]);
		$voucherList->getConditionBuilder()->add('redeemed >= redeemLimit');
		$voucherList->readObjects();
		$vouchers = $voucherList->getObjects();
		
		if (!empty($vouchers)) {
			foreach ($vouchers as $voucher) {
				$voucherEditor = new JCoinsVoucherEditor($voucher);
				$voucherEditor->update(['isExpired' => 1]);
				
				// log
				JCoinsVoucherLogEditor::create([
						'voucher' => $voucher,
						'action' => 'expired'
				]);
			}
		}
		
		// delete log
		if (JCOINS_VOUCHER_LOG_DELETE) {
			$sql = "DELETE FROM wcf".WCF_N."_jcoins_voucher_log
					WHERE	time < ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([TIME_NOW - JCOINS_VOUCHER_LOG_DELETE * 86400]);
		}
		
		// recurring vouchers
		$voucherList = new JCoinsVoucherList();
		$voucherList->getConditionBuilder()->add('isDisabled = ?', [0]);
		$voucherList->getConditionBuilder()->add('isPublished = ?', [1]);
		$voucherList->getConditionBuilder()->add('typeDes = ?', ['recurring']);
		$voucherList->getConditionBuilder()->add('nextDate < ?', [TIME_NOW]);
		$voucherList->readObjects();
		$vouchers = $voucherList->getObjects();
		
		if (empty($vouchers)) return;
		
		foreach ($vouchers as $voucher) {
			$voucherEditor = new JCoinsVoucherEditor($voucher);
			
			// skip expired by time
			if ($voucher->isExpired && $voucher->expirationStatus && $voucher->expirationDate < TIME_NOW) {
				continue;
			}
			
			// calculate nextDate
			$base = $voucher->nextDate;
			switch ($voucher->periodUnit) {
				case 'day':
					$next = $base + $voucher->period * 86400;
					while ($next < TIME_NOW) {
						$next = $next + $voucher->period * 86400;
					}
					break;
				case 'week':
					$next = $base + $voucher->period * 86400 * 7;
					while ($next < TIME_NOW) {
						$next = $next + $voucher->period * 86400 * 7;
					}
					break;
				case 'month':
					$next = strtotime('+' . $voucher->period . ' month', $base);
					while ($next < TIME_NOW) {
						$next = strtotime('+' . $voucher->period . ' month', $next);
					}
					break;
			}
			
			// update voucher
			$voucherEditor->update([
					'isExpired' => 0,
					'nextDate' => $next,
					'redeemLimit' => $voucher->redeemLimit ? $voucher->redeemLimitStart + $voucher->redeemed : 0
			]);
			
			// delete users who did not redeem and set remaining back to reedeemed
			$sql = "DELETE FROM	wcf".WCF_N."_jcoins_voucher_to_user
					WHERE 		voucherID = ? AND lastDate = ? AND redeemDate = ? AND redeemed = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$voucher->voucherID, 0, 0, 0]);
			
			$sql = "UPDATE	wcf".WCF_N."_jcoins_voucher_to_user
					SET		redeemDate = lastDate
					WHERE	voucherID = ? AND redeemDate = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$voucher->voucherID, 0]);
			
			// get users
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
			
			if (empty($userIDs)) return;
			
			// check redeem limit / raffle
			$count = count($userIDs);
			
			if ($voucher->redeemLimitStart > 0 && $voucher->redeemLimitStart < $count && $voucher->raffle) {
				shuffle($userIDs);
				$userIDs = array_slice($userIDs, 0, $voucher->redeemLimitStart);
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
						UPDATE		redeemDate = ?";
				$statement = WCF::getDB()->prepareStatement($sql);
				foreach ($storeIDs as $userID) {
					$statement->execute([$voucher->voucherID, $userID, 0]);
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
			
			// only one at a time
			break;
		}
	}
}
