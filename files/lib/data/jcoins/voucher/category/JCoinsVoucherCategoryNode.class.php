<?php
namespace wcf\data\jcoins\voucher\category;
use wcf\data\category\CategoryNode;

/**
 * Represents a JCoins voucher category node.
 *
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherCategoryNode extends CategoryNode {
	/**
	 * number of vouchers in the category
	 */
	protected $vouchers;
	
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = JCoinsVoucherCategory::class;
	
	/**
	 * Returns number of vouchers in the category.
	 */
	public function getItems() {
		if ($this->vouchers === null) {
			$this->vouchers = JCoinsVoucherCategoryCache::getInstance()->getItems($this->categoryID);
		}
		
		return $this->vouchers;
	}
}
