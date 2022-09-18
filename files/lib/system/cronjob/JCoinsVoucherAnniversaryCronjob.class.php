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
namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\data\jcoins\voucher\JCoinsVoucherEditor;
use wcf\data\jcoins\voucher\JCoinsVoucherList;
use wcf\data\jcoins\voucher\log\JCoinsVoucherLogEditor;
use wcf\data\user\UserList;
use wcf\system\user\notification\object\JCoinsVoucherNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\user\UserBirthdayCache;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Publishes JCoins vouchers on anniversaries.
 */
class JCoinsVoucherAnniversaryCronjob extends AbstractCronjob
{
    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob)
    {
        parent::execute($cronjob);

        if (!MODULE_JCOINS_VOUCHER) {
            return;
        }

        // get birthday vouchers
        $voucherList = new JCoinsVoucherList();
        $voucherList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $voucherList->getConditionBuilder()->add('isPublished = ?', [1]);
        $voucherList->getConditionBuilder()->add('isExpired = ?', [0]);
        $voucherList->getConditionBuilder()->add('typeDes = ?', ['birthday']);
        $voucherList->readObjects();
        $vouchers = $voucherList->getObjects();

        if (!empty($vouchers)) {
            // get today's birthdays
            $currentDay = DateUtil::format(null, 'm-d');
            $date = \explode('-', DateUtil::format(null, 'Y-n-j'));
            $userIDs = UserBirthdayCache::getInstance()->getBirthdays($date[1], $date[2]);

            if (!empty($userIDs)) {
                foreach ($vouchers as $voucher) {
                    $voucherEditor = new JCoinsVoucherEditor($voucher);

                    // get users
                    $userList = new UserList();
                    $userList->getConditionBuilder()->add('user_table.userID IN (?)', [$userIDs]);
                    $conditions = $voucher->getConditions();
                    foreach ($conditions as $condition) {
                        $condition->getObjectType()->getProcessor()->addUserCondition($condition, $userList);
                    }
                    $userList->readObjects();

                    $users = $userList->getObjects();
                    $affectedIDs = [];

                    if (!empty($users)) {
                        foreach ($userList as $user) {
                            $affectedIDs[] = $user->userID;
                        }
                    }
                    if (empty($affectedIDs)) {
                        continue;
                    }

                    // update voucher to user
                    WCF::getDB()->beginTransaction();
                    $sql = "INSERT INTO    wcf" . WCF_N . "_jcoins_voucher_to_user
                                (voucherID, userID)
                            VALUES        (?, ?)
                            ON DUPLICATE KEY
                            UPDATE        redeemDate = ?";
                    $statement = WCF::getDB()->prepareStatement($sql);
                    foreach ($affectedIDs as $userID) {
                        $statement->execute([$voucher->voucherID, $userID, 0]);
                    }
                    WCF::getDB()->commitTransaction();

                    // send notification
                    if ($voucher->notify) {
                        foreach ($affectedIDs as $userID) {
                            UserNotificationHandler::getInstance()->fireEvent('jCoinsVoucher', 'com.uz.jcoins.voucher.notification', new JCoinsVoucherNotificationObject($voucher), [$userID]);
                        }
                    }

                    // log
                    JCoinsVoucherLogEditor::create([
                        'voucher' => $voucher,
                        'action' => 'issued',
                        'detail' => \count($affectedIDs),
                    ]);
                }
            }
        }

        // get membership vouchers
        $voucherList = new JCoinsVoucherList();
        $voucherList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $voucherList->getConditionBuilder()->add('isPublished = ?', [1]);
        $voucherList->getConditionBuilder()->add('isExpired = ?', [0]);
        $voucherList->getConditionBuilder()->add('typeDes = ?', ['membership']);
        $voucherList->readObjects();
        $vouchers = $voucherList->getObjects();

        if (!empty($vouchers)) {
            foreach ($vouchers as $voucher) {
                $voucherEditor = new JCoinsVoucherEditor($voucher);

                // get users
                $userList = new UserList();
                $userList->getConditionBuilder()->add("DATE_FORMAT(FROM_UNIXTIME(user_table.registrationDate),'%m-%d') = DATE_FORMAT(NOW(),'%m-%d')");

                $conditions = $voucher->getConditions();
                foreach ($conditions as $condition) {
                    $condition->getObjectType()->getProcessor()->addUserCondition($condition, $userList);
                }
                $userList->readObjects();
                $users = $userList->getObjects();
                $affectedIDs = [];

                if (!empty($users)) {
                    foreach ($userList as $user) {
                        $affectedIDs[] = $user->userID;
                    }
                }
                if (empty($affectedIDs)) {
                    continue;
                }

                // update voucher to user
                WCF::getDB()->beginTransaction();
                $sql = "INSERT INTO    wcf" . WCF_N . "_jcoins_voucher_to_user
                            (voucherID, userID)
                        VALUES        (?, ?)
                        ON DUPLICATE KEY
                        UPDATE        redeemDate = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                foreach ($affectedIDs as $userID) {
                    $statement->execute([$voucher->voucherID, $userID, 0]);
                }
                WCF::getDB()->commitTransaction();

                // send notification
                if ($voucher->notify) {
                    foreach ($affectedIDs as $userID) {
                        UserNotificationHandler::getInstance()->fireEvent('jCoinsVoucher', 'com.uz.jcoins.voucher.notification', new JCoinsVoucherNotificationObject($voucher), [$userID]);
                    }
                }

                // log
                JCoinsVoucherLogEditor::create([
                    'voucher' => $voucher,
                    'action' => 'issued',
                    'detail' => \count($affectedIDs),
                ]);
            }
        }
    }
}
