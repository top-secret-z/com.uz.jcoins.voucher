<?php 
namespace wcf\data\jcoins\voucher\log;
use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;

/**
 * Provides functions to edit JCoins Voucher logs.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherLogEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	public static $baseClass = JCoinsVoucherLog::class;
	
	/**
	 * @log action
	 */
	public static function create(array $data = []) {
		if (isset($data['username'])) $username = $data['username'];
		else $username = WCF::getUser()->username;
		
		if (isset($data['userID'])) $userID = $data['userID'];
		else $userID = WCF::getUser()->userID;
		
		if (!$userID) $userID = null;
		
		$action = $detail = '';
		if (isset($data['detail'])) $detail = $data['detail'];
		if (isset($data['action'])) $action = $data['action'];
		
		$voucher = $data['voucher'];
		
		$parameters = [
				'time' => TIME_NOW,
				'voucherID' => $voucher->voucherID,
				'title' => $voucher->title,
				'typeDes' => $voucher->typeDes,
				'jCoins' => $voucher->jCoins,
				'userID' => $userID,
				'username' => $username,
				'action' => $action,
				'detail' => $detail
		];
		
		parent::create($parameters);
	}
}
