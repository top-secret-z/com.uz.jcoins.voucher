<?php
namespace wcf\system\user\notification\event;

/**
 * Notification event for JCoins vouchers.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @inheritdoc
	 */
	public function getTitle() {
		return $this->getLanguage()->get('wcf.user.notification.jcoins.voucher.title');
	}
	
	/**
	 * @inheritDoc
	 */
	public function getLink() {
		return $this->getUserNotificationObject()->getURL();
	}
	
	/**
	 * @inheritdoc
	 */
	public function getMessage() {
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.jcoins.voucher.message', [
				'userNotificationObject' => $this->getUserNotificationObject(),
				'author' => $this->author
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getEmailMessage($notificationType = 'instant') {
		return [
			'message-id' => 'com.uz.jcoins.voucher/'.$this->getUserNotificationObject()->voucherID,
			'template' => 'email_notification_jCoinsVoucher',
			'application' => 'wcf'
		];
	}
}
