<?php 
namespace wcf\data\jcoins\voucher\type;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a JCoins voucher type.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherType extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'jcoins_voucher_type';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'id';
	
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return WCF::getLanguage()->get('wcf.acp.jcoinsVoucher.type.' . $this->typeTitle);
	}
	
	/**
	 * return type with given typeID
	 */
	public static function getTypeByID($typeID) {
		$sql = "SELECT	*
				FROM 	wcf".WCF_N."_jcoins_voucher_type
				WHERE	typeID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$typeID]);
		$row = $statement->fetchArray();
		if (!$row) $row = [];
		return new JCoinsVoucherType(null, $row);
	}
}
