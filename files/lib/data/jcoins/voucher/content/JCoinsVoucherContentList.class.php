<?php
namespace wcf\data\jcoins\voucher\content;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of JCoins voucher contents.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherContentList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = JCoinsVoucherContent::class;
}
