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
namespace wcf\system\event\listener;

use wcf\data\jcoins\voucher\JCoinsVoucherList;
use wcf\data\jcoins\voucher\log\JCoinsVoucherLogEditor;
use wcf\system\user\notification\object\JCoinsVoucherNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;

/**
 * Listen to User creation for JCoins Voucher
 */
class JCoinsVoucherUserCreationListener implements IParameterizedEventListener
{
    /**
     * @see    wcf\system\event\IEventListener::execute()
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        // check module
        if (!MODULE_JCOINS_VOUCHER) {
            return;
        }

        // only action create
        if ($eventObj->getActionName() != 'create') {
            return;
        }

        // only if vouchers
        $voucherList = new JCoinsVoucherList();
        $voucherList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $voucherList->getConditionBuilder()->add('isPublished = ?', [1]);
        $voucherList->getConditionBuilder()->add('isExpired = ?', [0]);
        $voucherList->getConditionBuilder()->add('typeDes = ?', ['registration']);
        $voucherList->readObjects();
        $vouchers = $voucherList->getObjects();
        if (empty($vouchers)) {
            return;
        }

        $returnValues = $eventObj->getReturnValues();
        $userID = $returnValues['returnValues']->userID;

        foreach ($vouchers as $voucher) {
            // update voucher to user
            $sql = "INSERT INTO    wcf" . WCF_N . "_jcoins_voucher_to_user
                            (voucherID, userID)
                    VALUES        (?, ?)
                    ON DUPLICATE KEY
                    UPDATE        redeemDate = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$voucher->voucherID, $userID, 0]);

            // send notification
            if ($voucher->notify) {
                UserNotificationHandler::getInstance()->fireEvent('jCoinsVoucher', 'com.uz.jcoins.voucher.notification', new JCoinsVoucherNotificationObject($voucher), [$userID]);
            }

            // log
            JCoinsVoucherLogEditor::create([
                'voucher' => $voucher,
                'action' => 'issued',
                'detail' => 1,
                'username' => 'System',
                'userID' => null,
            ]);
        }
    }
}
