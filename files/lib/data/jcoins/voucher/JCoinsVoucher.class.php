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

use wcf\data\DatabaseObject;
use wcf\data\jcoins\voucher\category\JCoinsVoucherCategory;
use wcf\data\jcoins\voucher\content\JCoinsVoucherContent;
use wcf\data\TMultiCategoryObject;
use wcf\data\user\UserList;
use wcf\system\condition\ConditionHandler;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a JCoins Voucher.
 */
class JCoinsVoucher extends DatabaseObject implements IRouteController
{
    use TMultiCategoryObject;

    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'jcoins_voucher';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'voucherID';

    /**
     * voucher content grouped by language id
     */
    public $voucherContents;

    /**
     * list of redeemed vouchers / last date + times
     */
    protected static $redeemCache;

    protected static $redeemTimesCache;

    /**
     * @inheritDoc
     */
    public function getLink()
    {
        return LinkHandler::getInstance()->getLink('JCoinsVoucher', ['forceFrontend' => true], 'voucherID=' . $this->voucherID);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return '';
    }

    /**
     * Returns the active content version.
     */
    public function getItemContent()
    {
        $this->getItemContents();

        if ($this->isMultilingual) {
            if (isset($this->voucherContents[WCF::getLanguage()->languageID])) {
                return $this->voucherContents[WCF::getLanguage()->languageID];
            }
        } else {
            if (!empty($this->voucherContents[0])) {
                return $this->voucherContents[0];
            }
        }

        return null;
    }

    /**
     * Returns the voucher's contents.
     */
    public function getItemContents()
    {
        if ($this->voucherContents === null) {
            $this->voucherContents = [];

            $sql = "SELECT    *
                    FROM    wcf" . WCF_N . "_jcoins_voucher_content
                    WHERE    voucherID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$this->voucherID]);
            while ($row = $statement->fetchArray()) {
                $this->voucherContents[$row['languageID'] ?: 0] = new JCoinsVoucherContent(null, $row);
            }
        }

        return $this->voucherContents;
    }

    /**
     * Returns the voucher's subject.
     */
    public function getSubject($acp = 0)
    {
        if ($this->getItemContent() !== null) {
            if ($acp) {
                $content = $this->getItemContent();
                $content->content = \str_replace('data-name="wsm"', 'data-name="jcvwsm"', $content->subject);

                return $this->getItemContent()->getSubject();
            }

            return $this->getItemContent()->getSubject();
        }

        return '';
    }

    /**
     * Returns the voucher's bbcode title.
     */
    public function getBBCodeSubject()
    {
        if ($this->getItemContent() !== null) {
            if (!empty($this->getItemContent()->getSubject())) {
                $test = $this->getItemContent()->getSubject();
            } else {
                $test = $this->getItemContent()->getFormattedContent();
            }

            return StringUtil::stripHTML($test);
        }

        return '';
    }

    /**
     * Returns the voucher's content.
     */
    public function getContent($acp = 0)
    {
        if ($this->getItemContent() !== null) {
            if ($acp) {
                $content = $this->getItemContent();
                $content->content = \str_replace('data-name="wsm"', 'data-name="jcvwsm"', $content->content);

                return $this->getItemContent()->getFormattedContent();
            }

            return $this->getItemContent()->getFormattedContent();
        }

        return '';
    }

    /**
     * Returns the voucher's footer.
     */
    public function getFooter($acp = 0)
    {
        if ($this->getItemContent() !== null) {
            if ($acp) {
                $content = $this->getItemContent();
                $content->content = \str_replace('data-name="wsm"', 'data-name="jcvwsm"', $content->footer);

                return $this->getItemContent()->getFooter();
            }

            return $this->getItemContent()->getFooter();
        }

        return '';
    }

    /**
     * Returns the voucher's image.
     */
    public function getImage()
    {
        if ($this->getItemContent() !== null) {
            return $this->getItemContent()->getImage();
        }

        return null;
    }

    /**
     * Returns last date the active user has redeemed vouchers.
     */
    public function hasRedeemed()
    {
        if (self::$redeemCache === null) {
            self::loadRedeemCache();
        }

        return self::$redeemCache[$this->voucherID] ?? 0;
    }

    /**
     * Loads the last date of redeemed vouchers.
     */
    protected static function loadRedeemCache()
    {
        self::$redeemCache = [];
        if (!WCF::getUser()->userID) {
            return;
        }

        $sql = "SELECT    voucherID, lastDate
                FROM    wcf" . WCF_N . "_jcoins_voucher_to_user
                WHERE    userID = ? AND redeemDate > ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID, 0]);
        while ($row = $statement->fetchArray()) {
            if (!isset(self::$redeemCache[$row['voucherID']])) {
                self::$redeemCache[$row['voucherID']] = $row['lastDate'];
            }
        }
    }

    /**
     * Returns how often the active user has redeemed vouchers.
     */
    public function hasRedeemedTimes()
    {
        if (self::$redeemTimesCache === null) {
            self::loadRedeemTimesCache();
        }

        return self::$redeemTimesCache[$this->voucherID] ?? 0;
    }

    /**
     * Loads the redemption number of redeemed vouchers.
     */
    protected static function loadRedeemTimesCache()
    {
        self::$redeemTimesCache = [];
        if (!WCF::getUser()->userID) {
            return;
        }

        $sql = "SELECT    voucherID, redeemed
                FROM    wcf" . WCF_N . "_jcoins_voucher_to_user
                WHERE    userID = ? AND redeemed > ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID, 0]);
        while ($row = $statement->fetchArray()) {
            if (!isset(self::$redeemTimesCache[$row['voucherID']])) {
                self::$redeemTimesCache[$row['voucherID']] = $row['redeemed'];
            }
        }
    }

    /**
     * Returns the conditions for the voucher.
     */
    public function getConditions()
    {
        return ConditionHandler::getInstance()->getConditions('com.uz.jcoins.voucher.condition', $this->voucherID);
    }

    /**
     * @inheritDoc
     */
    public static function getCategoryClassName()
    {
        return JCoinsVoucherCategory::class;
    }

    /**
     * @inheritDoc
     */
    public static function getCategoryMappingDatabaseTableName()
    {
        return 'wcf' . WCF_N . '_jcoins_voucher_to_category';
    }

    /**
     * Returns true if the active user can redeem this voucher.
     */
    public function canRedeem()
    {
        if ($this->isDisabled) {
            return false;
        }
        if (!WCF::getSession()->getPermission('user.jcoins.voucher.canSee')) {
            return false;
        }

        // limits
        if ($this->expirationStatus && $this->expirationDate < TIME_NOW) {
            return false;
        }
        if ($this->isExpired) {
            return false;
        }
        if ($this->redeemLimit > 0 && $this->redeemed >= $this->redeemLimit) {
            return false;
        }

        if ($this->typeDes == 'code') {
            if ($this->codeNumber * $this->codeRedeemLimit <= $this->redeemed) {
                return false;
            }
            if ($this->userRedeemedTimes() >= $this->codeUserLimit) {
                return false;
            }
        } else {
            if ($this->hasRedeemed()) {
                return false;
            }
        }

        // elegible
        $sql = "SELECT    COUNT(*)
                FROM    wcf" . WCF_N . "_jcoins_voucher_to_user
                WHERE    userID = ? AND voucherID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID, $this->voucherID]);
        if (!$statement->fetchColumn()) {
            return false;
        }

        return true;
    }

    /**
     * Returns the voucher's category.
     */
    public function getCategory()
    {
        $sql = "SELECT    categoryID
                FROM    wcf" . WCF_N . "_jcoins_voucher_to_category
                WHERE    voucherID = ?";
        $statement = WCF::getDB()->prepareStatement($sql, 1);
        $statement->execute([$this->voucherID]);

        return $statement->fetchColumn();
    }

    /**
     * Loads the list of vouchers for the active user.
     */
    public static function getVouchers()
    {
        if (!WCF::getUser()->userID) {
            return [];
        }

        $voucherIDs = [];
        $sql = "SELECT    voucherID
                FROM    wcf" . WCF_N . "_jcoins_voucher_to_user
                WHERE    userID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID]);
        while ($row = $statement->fetchArray()) {
            $voucherIDs[] = $row['voucherID'];
        }

        // include code vouchers
        return \array_merge($voucherIDs, self::getCodeVouchers());
    }

    /**
     * Returns the current user's code vouchers.
     */
    public static function getCodeVouchers()
    {
        $voucherList = new JCoinsVoucherList();
        $voucherList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $voucherList->getConditionBuilder()->add('isPublished = ?', [1]);
        $voucherList->getConditionBuilder()->add('isExpired = ?', [0]);
        $voucherList->getConditionBuilder()->add('(typeDes = ?)', ['code']);
        $voucherList->readObjects();
        $vouchers = $voucherList->getObjects();

        $voucherIDs = [];

        if (!empty($vouchers)) {
            foreach ($vouchers as $voucher) {
                $userList = new UserList();
                $conditions = $voucher->getConditions();

                $voucherList->getConditionBuilder()->add('user_table.userID = ?', [WCF::getUser()->userID]);
                foreach ($conditions as $condition) {
                    $condition->getObjectType()->getProcessor()->addUserCondition($condition, $userList);
                }
                $userList->readObjects();
                $users = $userList->getObjects();

                if (!empty($users)) {
                    $voucherIDs[] = $voucher->voucherID;
                }
            }
        }

        return $voucherIDs;
    }

    /**
     * Returns the code used count for this code voucher.
     */
    public function codeRedeemedTimes($code)
    {
        $sql = "SELECT    redeemed
                FROM    wcf" . WCF_N . "_jcoins_voucher_to_code
                WHERE    code = ? AND voucherID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$code, $this->voucherID]);

        return $statement->fetchColumn();
    }

    /**
     * Returns how often the current user has redeemed this voucher.
     */
    public function userRedeemedTimes()
    {
        $sql = "SELECT    redeemed
                FROM    wcf" . WCF_N . "_jcoins_voucher_to_user
                WHERE    userID = ? AND voucherID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID, $this->voucherID]);

        return $statement->fetchColumn();
    }

    /**
     * Returns the redemption count of a code for this voucher.
     */
    public function codeExists($code)
    {
        $sql = "SELECT    code, redeemed
                FROM    wcf" . WCF_N . "_jcoins_voucher_to_code
                WHERE    code LIKE ? AND voucherID = ?";
        $statement = WCF::getDB()->prepareStatement($sql, 1);
        $statement->execute([$code, $this->voucherID]);
        if ($row = $statement->fetchArray()) {
            return $row['redeemed'];
        }

        return null;
    }
}
