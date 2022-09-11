<?php
namespace wcf\system\user\notification\object;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\jcoins\voucher\JCoinsVoucher;

/**
 * Notification object for JCoins vouchers.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject {
	/**
	 * @inheritdoc
	 */
	protected static $baseClass = JCoinsVoucher::class;

	/**
	 * @inheritdoc
	 */
	public function getTitle() {
		return '';
	}

	/**
	 * @inheritdoc
	 */
	public function getURL() {
		return $this->getDecoratedObject()->getLink();
	}
	
	/**
	 * @inheritdoc
	 */
	public function getAuthorID() {
		return $this->author;
	}
}
