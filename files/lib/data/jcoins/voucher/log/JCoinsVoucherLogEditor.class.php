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

use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;

/**
 * Provides functions to edit JCoins Voucher logs.
 */
class JCoinsVoucherLogEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    public static $baseClass = JCoinsVoucherLog::class;

    /**
     * @log action
     */
    public static function create(array $data = [])
    {
        if (isset($data['username'])) {
            $username = $data['username'];
        } else {
            $username = WCF::getUser()->username;
        }

        if (isset($data['userID'])) {
            $userID = $data['userID'];
        } else {
            $userID = WCF::getUser()->userID;
        }

        if (!$userID) {
            $userID = null;
        }

        $action = $detail = '';
        if (isset($data['detail'])) {
            $detail = $data['detail'];
        }
        if (isset($data['action'])) {
            $action = $data['action'];
        }

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
            'detail' => $detail,
        ];

        parent::create($parameters);
    }
}
