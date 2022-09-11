<?php
namespace wcf\data\jcoins\voucher\content;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes JCoins voucher content related actions.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherContentAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = JCoinsVoucherContentEditor::class;
}
