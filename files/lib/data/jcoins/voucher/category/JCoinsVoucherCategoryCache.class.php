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
namespace wcf\data\jcoins\voucher\category;

use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Manages the JCoins Voucher category cache.
 */
class JCoinsVoucherCategoryCache extends SingletonFactory
{
    /**
     * number of total vouchers
     */
    protected $vouchers;

    /**
     * Calculates the number of vouchers.
     */
    protected function initItems()
    {
        // get user's voucher
        $sql = "SELECT        COUNT(*) AS count, jcoins_voucher_to_category.categoryID
                FROM        wcf" . WCF_N . "_jcoins_voucher jcoins_voucher
                LEFT JOIN    wcf" . WCF_N . "_jcoins_voucher_to_category jcoins_voucher_to_category
                ON            (jcoins_voucher_to_category.voucherID = jcoins_voucher.voucherID)
                WHERE        (jcoins_voucher.voucherID IN (SELECT voucherID FROM    wcf" . WCF_N . "_jcoins_voucher_to_user WHERE userID = ?) OR
                            jcoins_voucher.voucherID IN (SELECT voucherID FROM    wcf" . WCF_N . "_jcoins_voucher WHERE typeDes = ?)) AND
                            jcoins_voucher.isDisabled = ? AND jcoins_voucher.isPublished = ?
                GROUP BY    jcoins_voucher_to_category.categoryID";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID, 'code', 0, 1]);
        $this->vouchers = $statement->fetchMap('categoryID', 'count');
    }

    /**
     * Return the number of vouchers in the category with the given id.
     */
    public function getItems($categoryID)
    {
        if ($this->vouchers === null) {
            $this->initItems();
        }

        if (isset($this->vouchers[$categoryID])) {
            return $this->vouchers[$categoryID];
        }

        return 0;
    }
}
