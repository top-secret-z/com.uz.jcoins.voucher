<?php

/*
 * Copyright by Udo Zaydowicz.
 * Modified by SoftCreatR.dev.
 *
 * License: http://opensource.org/licenses/lgpl-license.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace wcf\system\bbcode;

use wcf\data\jcoins\voucher\JCoinsVoucher;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

/**
 * Parses the [voucher] bbcode tag.
 */
class JCoinsVoucherBBCode extends AbstractBBCode
{
    /**
     * @inheritDoc
     */
    public function getParsedTag(array $openingTag, $content, array $closingTag, BBCodeParser $parser)
    {
        $voucherIDs = [];
        if (isset($openingTag['attributes'][0])) {
            $voucherIDs = \array_unique(ArrayUtil::toIntegerArray(\explode(',', $openingTag['attributes'][0])));
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
                    'vouchers' => $vouchers,
                ], true);
            }

            $result = '';
            foreach ($vouchers as $voucher) {
                if (!empty($result)) {
                    $result .= ' ';
                }
                $result .= StringUtil::getAnchorTag(LinkHandler::getInstance()->getLink('JCoinsVoucher', [
                    'application' => 'wcf',
                    'object' => $voucher,
                ]));
            }

            return $result;
        }

        if (!empty($voucherIDs)) {
            $result = '';
            foreach ($voucherIDs as $voucherID) {
                if ($voucherID) {
                    if (!empty($result)) {
                        $result .= ' ';
                    }
                    $result .= StringUtil::getAnchorTag(LinkHandler::getInstance()->getLink('JCoinsVoucher', [
                        'application' => 'wcf',
                        'id' => $voucherID,
                    ]));
                }
            }

            return $result;
        }
    }
}
