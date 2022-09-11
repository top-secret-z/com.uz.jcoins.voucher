<?php
namespace wcf\data\jcoins\voucher\type;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of JCoins voucher types
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherTypeList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = JCoinsVoucherType::class;
	
	/**
	 * sql order
	 */
	public $sqlOrderBy = 'sortOrder ASC';
}
