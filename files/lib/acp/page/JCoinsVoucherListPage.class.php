<?php 
namespace wcf\acp\page;
use wcf\data\jcoins\voucher\JCoinsVoucherList;
use wcf\page\SortablePage;
use wcf\system\WCF;

/**
 * Shows the JCoins Voucher list page.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherListPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.voucherJCoins.list';
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.jcoins.voucher.canManage'];
	
	/**
	 * @inheritDoc
	 */
	public $neededModules = ['MODULE_JCOINS_VOUCHER'];
	
	/**
	 * number of vouchers shown per page
	 */
	public $itemsPerPage = 20;
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortField = 'voucherID';
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = ['voucherID', 'isDisabled', 'title', 'jCoins', 'typeDes', 'redeemLimit', 'expirationDate', 'redeemed'];
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = JCoinsVoucherList::class;
}
