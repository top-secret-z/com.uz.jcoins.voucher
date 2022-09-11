<?php
namespace wcf\data\jcoins\voucher\content;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit JCoins voucher content.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherContentEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = JCoinsVoucherContent::class;
}
