<?php 
namespace wcf\data\jcoins\voucher\log;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of JCoins Voucher logs.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherLogList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = JCoinsVoucherLog::class;
}
