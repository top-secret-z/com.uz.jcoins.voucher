<?php
namespace wcf\data\jcoins\voucher;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of JCoins Vouchers.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherList extends DatabaseObjectList {
	/**
	 * enables/disables the loading of categories
	 */
	protected $categoryLoading = true;
	
	/**
	 * @inheritDoc
	 */
	public $className = JCoinsVoucher::class;
	
	/**
	 * Enables/disables the loading of categories.
	 */
	public function enableCategoryLoading($enable = true) {
		$this->categoryLoading = $enable;
	}
}
