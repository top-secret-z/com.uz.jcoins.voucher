<?php
namespace wcf\system\user\notification\object\type;
use wcf\data\jcoins\voucher\JCoinsVoucher;
use wcf\data\jcoins\voucher\JCoinsVoucherList;
use wcf\system\user\notification\object\JCoinsVoucherNotificationObject;

/**
 * Notification object type for JCoins vouchers.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherNotificationObjectType extends AbstractUserNotificationObjectType {
	/**
	 * @inheritdoc
	 */
	protected static $decoratorClassName = JCoinsVoucherNotificationObject::class;
	
	/**
	 * @inheritdoc
	 */
	protected static $objectClassName = JCoinsVoucher::class;
	
	/**
	 * @inheritdoc
	 */
	protected static $objectListClassName = JCoinsVoucherList::class;
	
}
