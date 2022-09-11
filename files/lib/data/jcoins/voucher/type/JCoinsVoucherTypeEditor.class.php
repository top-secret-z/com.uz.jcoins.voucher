<?php
namespace wcf\data\jcoins\voucher\type;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit JCoins voucher types.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherTypeEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	public static $baseClass = JCoinsVoucherType::class;
}
