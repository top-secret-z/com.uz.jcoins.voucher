<?php
namespace wcf\system\bbcode;
use wcf\data\jcoins\voucher\JCoinsVoucher;
use wcf\system\bbcode\AbstractBBCode;
use wcf\system\bbcode\BBCodeParser;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

/**
 * Parses the [voucher] bbcode tag.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherBBCode extends AbstractBBCode {
	/**
	 * @inheritDoc
	 */
	public function getParsedTag(array $openingTag, $content, array $closingTag, BBCodeParser $parser) {
		$voucherIDs = [];
		if (isset($openingTag['attributes'][0])) {
			$voucherIDs = array_unique(ArrayUtil::toIntegerArray(explode(',', $openingTag['attributes'][0])));
		}
		
		$vouchers = [];
		foreach ($voucherIDs as $voucherID) {
			//if (($voucher = MessageEmbeddedObjectManager::getInstance()->getObject('com.uz.jcoins.voucher.content', $voucherID)) !== null) {
			$voucher = new JCoinsVoucher($voucherID);
			if ($voucher->voucherID) {
				$vouchers[] = $voucher;
			}
		}
		
		if (!empty($vouchers)) {
			if ($parser->getOutputType() == 'text/html') {
				return WCF::getTPL()->fetch('jCoinsVoucherBBCode', 'wcf', [
						'vouchers' => $vouchers
				], true);
			}
			
			$result = '';
			foreach ($vouchers as $voucher) {
				if (!empty($result)) $result .= ' ';
				$result .= StringUtil::getAnchorTag(LinkHandler::getInstance()->getLink('JCoinsVoucher', [
						'application' => 'wcf',
						'object' => $voucher
				]));
			}
				
			return $result;
		}
		
		if (!empty($voucherIDs)) {
			$result = '';
			foreach ($voucherIDs as $voucherID) {
				if ($voucherID) {
					if (!empty($result)) $result .= ' ';
					$result .= StringUtil::getAnchorTag(LinkHandler::getInstance()->getLink('JCoinsVoucher', [
							'application' => 'wcf',
							'id' => $voucherID
					]));
				}
			}
			
			return $result;
		}
	}
}
