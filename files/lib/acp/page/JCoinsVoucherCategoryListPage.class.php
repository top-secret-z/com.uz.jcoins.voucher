<?php
namespace wcf\acp\page;
use wcf\acp\page\AbstractCategoryListPage;

/**
 * Shows the JCoins Voucher category list page
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherCategoryListPage extends AbstractCategoryListPage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.voucherJCoins.category.list';
	
	/**
	 * @inheritDoc
	 */
	public $objectTypeName = 'com.uz.jcoins.voucher.category';
}
