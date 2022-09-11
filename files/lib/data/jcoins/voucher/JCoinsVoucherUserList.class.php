<?php
namespace wcf\data\jcoins\voucher;
use wcf\system\WCF;

/**
 * Represents a list of users' JCoins vouchers.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherUserList extends JCoinsVoucherList {
	/**
	 * Creates a new voucher list object.
	 */
	public function __construct() {
		parent::__construct();
		
		// get user's code vouchers
		$voucherIDs = JCoinsVoucher::getCodeVouchers();
		$voucherIDs[] = 0;
		
		$this->getConditionBuilder()->add("(jcoins_voucher.voucherID IN (SELECT voucherID FROM wcf".WCF_N."_jcoins_voucher_to_user WHERE userID = ?) OR jcoins_voucher.voucherID IN (?))", [WCF::getUser()->userID, $voucherIDs]);
		$this->getConditionBuilder()->add('jcoins_voucher.isDisabled = ?', [0]);
		$this->getConditionBuilder()->add('jcoins_voucher.isPublished = ?', [1]);
	}
}
