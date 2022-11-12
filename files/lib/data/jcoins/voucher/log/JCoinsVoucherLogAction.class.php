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
namespace wcf\data\jcoins\voucher\log;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Executes JCoins Voucher log actions.
 */
class JCoinsVoucherLogAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = JCoinsVoucherLogEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.jcoins.voucher.canManage'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.jcoins.voucher.canManage'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'toggle', 'update', 'clearAll'];

    /**
     * Validates the clearAll action.
     */
    public function validateClearAll()
    {
        // do nothing
    }

    /**
     * Executes the clearAll action.
     */
    public function clearAll()
    {
        $sql = "DELETE FROM    wcf" . WCF_N . "_jcoins_voucher_log";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
    }
}
