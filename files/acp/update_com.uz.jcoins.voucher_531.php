<?php
use wcf\data\jcoins\voucher\JCoinsVoucherEditor;
use wcf\data\jcoins\voucher\JCoinsVoucherList;
use wcf\system\WCF;

/**
 * Update expired vouchers
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */

// get affected vouchers
$voucherList = new JCoinsVoucherList();
$voucherList->getConditionBuilder()->add('isPublished = ?', [1]);
$voucherList->getConditionBuilder()->add('isExpired = ?', [1]);
$voucherList->getConditionBuilder()->add('(expirationStatus = ? OR expirationDate > ?)', [0, TIME_NOW]);
$voucherList->getConditionBuilder()->add('redeemLimit > ?', [0]);
$voucherList->getConditionBuilder()->add('redeemed < redeemLimit');
$voucherList->getConditionBuilder()->add('typeDes = ?', ['normal']);
$voucherList->readObjects();
$vouchers = $voucherList->getObjects();

if (!empty($vouchers)) {
	foreach ($vouchers as $voucher) {
		$voucherEditor = new JCoinsVoucherEditor($voucher);
		$voucherEditor->update([
				'isExpired' => 0,
				'expirationDate' => 0
		]);
	}
}
