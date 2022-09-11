<?php
namespace wcf\system\stat;
use wcf\system\WCF;

/**
 * Stat handler implementation for JCoins voucher redemptions.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherStatDailyHandler extends AbstractStatDailyHandler {
	/**
	 * @see	\wcf\system\stat\IStatDailyHandler::getData()
	 */
	public function getData($date) {
		return [
				'counter' => $this->getCounter($date, 'wcf'.WCF_N.'_jcoins_voucher_redemption', 'time'),
				'total' => $this->getTotal($date, 'wcf'.WCF_N.'_jcoins_voucher_redemption', 'time')
		];
	}
}
