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

use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;

/**
 * Provides functions to edit JCoins Vouchers.
 */
class JCoinsVoucherEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    public static $baseClass = JCoinsVoucher::class;

    /**
     * Updates category ids.
     */
    public function updateCategoryIDs(array $categoryIDs = [])
    {
        // remove old assigns
        $sql = "DELETE FROM    wcf" . WCF_N . "_jcoins_voucher_to_category
                WHERE        voucherID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$this->voucherID]);

        // assign new categories
        if (!empty($categoryIDs)) {
            WCF::getDB()->beginTransaction();

            $sql = "INSERT INTO    wcf" . WCF_N . "_jcoins_voucher_to_category
                        (categoryID, voucherID)
                VALUES        (?, ?)";
            $statement = WCF::getDB()->prepareStatement($sql);
            foreach ($categoryIDs as $categoryID) {
                $statement->execute([
                    $categoryID,
                    $this->voucherID,
                ]);
            }

            WCF::getDB()->commitTransaction();
        }
    }

    /**
     * update user to voucher
     */
    public function updateItemToUser()
    {
        $sql = "INSERT INTO    wcf" . WCF_N . "_jcoins_voucher_to_user
                            (voucherID, userID, redeemDate, jCoins)
                VALUES        (?, ?, ?, ?)";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$this->voucherID, WCF::getUser()->userID, TIME_NOW, $this->jCoins]);
    }
}
