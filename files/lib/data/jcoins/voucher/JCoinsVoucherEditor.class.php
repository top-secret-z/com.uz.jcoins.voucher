<?php
namespace wcf\data\jcoins\voucher;
use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;

/**
 * Provides functions to edit JCoins Vouchers.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	public static $baseClass = JCoinsVoucher::class;
	
	/**
	 * Updates category ids.
	 */
	public function updateCategoryIDs(array $categoryIDs = []) {
		// remove old assigns
		$sql = "DELETE FROM	wcf".WCF_N."_jcoins_voucher_to_category
				WHERE		voucherID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->voucherID]);
		
		// assign new categories
		if (!empty($categoryIDs)) {
			WCF::getDB()->beginTransaction();
			
			$sql = "INSERT INTO	wcf".WCF_N."_jcoins_voucher_to_category
						(categoryID, voucherID)
				VALUES		(?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			foreach ($categoryIDs as $categoryID) {
				$statement->execute([
						$categoryID,
						$this->voucherID
				]);
			}
			
			WCF::getDB()->commitTransaction();
		}
	}
	
	/**
	 * update user to voucher
	 */
	public function updateItemToUser() {
		$sql = "INSERT INTO	wcf".WCF_N."_jcoins_voucher_to_user
							(voucherID, userID, redeemDate, jCoins)
				VALUES		(?, ?, ?, ?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->voucherID, WCF::getUser()->userID, TIME_NOW, $this->jCoins]);
	}
}
