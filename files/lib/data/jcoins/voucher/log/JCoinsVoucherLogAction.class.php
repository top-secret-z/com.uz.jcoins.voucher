<?php 
namespace wcf\data\jcoins\voucher\log;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Executes JCoins Voucher log actions.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherLogAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = JCoinsVoucherLogEditor::class;
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = ['admin.jcoins.voucher.canManage'];
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsUpdate = ['admin.jcoins.voucher.canManage'];
	
	/**
	 * @inheritDoc
	 */
	protected $requireACP = ['create', 'delete', 'toggle', 'update', 'clearAll'];
	
	/**
	 * Validates the clearAll action.
	 */
	public function validateClearAll() {
		// do nothing
	}
	
	/**
	 * Executes the clearAll action.
	 */
	public function clearAll() {
		$sql = "DELETE FROM	wcf".WCF_N."_jcoins_voucher_log";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
	}
}
