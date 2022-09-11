<?php 
namespace wcf\data\jcoins\voucher\log;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a JCoins Voucher log
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherLog extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'jcoins_voucher_log';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'logID';
}
