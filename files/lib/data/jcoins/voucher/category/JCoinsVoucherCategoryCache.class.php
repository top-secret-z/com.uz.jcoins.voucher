<?php
namespace wcf\data\jcoins\voucher\category;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Manages the JCoins Voucher category cache.
 *
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherCategoryCache extends SingletonFactory {
	/**
	 * number of total vouchers
	 */
	protected $vouchers;
	
	/**
	 * Calculates the number of vouchers.
	 */
	protected function initItems() {
		// get user's voucher
		$sql = "SELECT		COUNT(*) AS count, jcoins_voucher_to_category.categoryID
				FROM		wcf".WCF_N."_jcoins_voucher jcoins_voucher
				LEFT JOIN	wcf".WCF_N."_jcoins_voucher_to_category jcoins_voucher_to_category
				ON			(jcoins_voucher_to_category.voucherID = jcoins_voucher.voucherID)
				WHERE		(jcoins_voucher.voucherID IN (SELECT voucherID FROM	wcf".WCF_N."_jcoins_voucher_to_user WHERE userID = ?) OR
							jcoins_voucher.voucherID IN (SELECT voucherID FROM	wcf".WCF_N."_jcoins_voucher WHERE typeDes = ?)) AND
							jcoins_voucher.isDisabled = ? AND jcoins_voucher.isPublished = ?
				GROUP BY	jcoins_voucher_to_category.categoryID";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([WCF::getUser()->userID, 'code', 0, 1]);
		$this->vouchers = $statement->fetchMap('categoryID', 'count');
	}
	
	/**
	 * Return the number of vouchers in the category with the given id.
	 */
	public function getItems($categoryID) {
		if ($this->vouchers === null) {
			$this->initItems();
		}
		
		if (isset($this->vouchers[$categoryID])) return $this->vouchers[$categoryID];
		return 0;
	}
}
