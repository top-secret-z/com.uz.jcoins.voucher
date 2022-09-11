<?php
namespace wcf\acp\form;
use wcf\acp\form\AbstractCategoryAddForm;

/**
 * Shows the JCoins voucher category add form.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherCategoryAddForm extends AbstractCategoryAddForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.voucherJCoins.category.add';
	
	/**
	 * @inheritDoc
	 */
	public $objectTypeName = 'com.uz.jcoins.voucher.category';
}
