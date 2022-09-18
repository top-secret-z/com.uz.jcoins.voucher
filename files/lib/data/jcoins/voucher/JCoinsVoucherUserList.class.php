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
namespace wcf\data\jcoins\voucher;

use wcf\system\WCF;

/**
 * Represents a list of users' JCoins vouchers.
 */
class JCoinsVoucherUserList extends JCoinsVoucherList
{
    /**
     * Creates a new voucher list object.
     */
    public function __construct()
    {
        parent::__construct();

        // get user's code vouchers
        $voucherIDs = JCoinsVoucher::getCodeVouchers();
        $voucherIDs[] = 0;

        $this->getConditionBuilder()->add("(jcoins_voucher.voucherID IN (SELECT voucherID FROM wcf" . WCF_N . "_jcoins_voucher_to_user WHERE userID = ?) OR jcoins_voucher.voucherID IN (?))", [WCF::getUser()->userID, $voucherIDs]);
        $this->getConditionBuilder()->add('jcoins_voucher.isDisabled = ?', [0]);
        $this->getConditionBuilder()->add('jcoins_voucher.isPublished = ?', [1]);
    }
}
