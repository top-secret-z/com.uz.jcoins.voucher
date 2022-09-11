<?php
namespace wcf\system\event\listener;
use wcf\data\jcoins\voucher\JCoinsVoucherList;
use wcf\data\jcoins\voucher\log\JCoinsVoucherLogEditor;
use wcf\system\user\notification\object\JCoinsVoucherNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;

/**
 * Listen to User creation for JCoins Voucher
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherUserCreationListener implements IParameterizedEventListener {
	/**
	 * @see	wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// check module
		if (!MODULE_JCOINS_VOUCHER) return;
		
		// only action create
		if ($eventObj->getActionName() != 'create') return;
		
		// only if vouchers
		$voucherList = new JCoinsVoucherList();
		$voucherList->getConditionBuilder()->add('isDisabled = ?', [0]);
		$voucherList->getConditionBuilder()->add('isPublished = ?', [1]);
		$voucherList->getConditionBuilder()->add('isExpired = ?', [0]);
		$voucherList->getConditionBuilder()->add('typeDes = ?', ['registration']);
		$voucherList->readObjects();
		$vouchers = $voucherList->getObjects();
		if (empty($vouchers)) return;
		
		$returnValues = $eventObj->getReturnValues();
		$userID = $returnValues['returnValues']->userID;
		
		foreach ($vouchers as $voucher) {
			// update voucher to user
			$sql = "INSERT INTO	wcf".WCF_N."_jcoins_voucher_to_user
							(voucherID, userID)
					VALUES		(?, ?)
					ON DUPLICATE KEY
					UPDATE		redeemDate = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$voucher->voucherID, $userID, 0]);
			
			// send notification
			if ($voucher->notify) {
				UserNotificationHandler::getInstance()->fireEvent('jCoinsVoucher', 'com.uz.jcoins.voucher.notification', new JCoinsVoucherNotificationObject($voucher), [$userID]);
			}
			
			// log
			JCoinsVoucherLogEditor::create([
					'voucher' => $voucher,
					'action' => 'issued',
					'detail' => 1,
					'username' => 'System',
					'userID' => null
			]);
		}
	}
}
