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
namespace wcf\acp\page;

use wcf\data\jcoins\voucher\log\JCoinsVoucherLogList;
use wcf\page\SortablePage;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the JCoins Voucher log list page.
 */
class JCoinsVoucherLogListPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.voucherJCoins.log.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.jcoins.voucher.canManage'];

    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_JCOINS_VOUCHER'];

    /**
     * number of vouchers shown per page
     * @var    integer
     */
    public $itemsPerPage = 20;

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'time';

    public $defaultSortOrder = 'DESC';

    /**
     * @inheritDoc
     */
    public $validSortFields = ['logID', 'time', 'username', 'title', 'typeDes', 'jCoins', 'action', 'detail'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = JCoinsVoucherLogList::class;

    /**
     * filter
     */
    public $username = '';

    public $title = '';

    public $action = '';

    public $availableTypes = [];

    /**
     * @inheritdoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['username'])) {
            $this->username = StringUtil::trim($_REQUEST['username']);
        }
        if (isset($_REQUEST['title'])) {
            $this->title = StringUtil::trim($_REQUEST['title']);
        }
        if (!empty($_REQUEST['action'])) {
            $this->action = $_REQUEST['action'];
        }
    }

    /**
     * @inheritdoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        if ($this->username) {
            $this->objectList->getConditionBuilder()->add('jcoins_voucher_log.username LIKE ?', ['%' . $this->username . '%']);
        }
        if ($this->title) {
            $this->objectList->getConditionBuilder()->add('jcoins_voucher_log.title LIKE ?', ['%' . $this->title . '%']);
        }

        if ($this->action) {
            $this->objectList->getConditionBuilder()->add('jcoins_voucher_log.action LIKE ?', ['%' . $this->action . '%']);
        }

        // available actions
        $this->availableActions = [];
        $sql = "SELECT    DISTINCT    action
                FROM                wcf" . WCF_N . "_jcoins_voucher_log";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        while ($row = $statement->fetchArray()) {
            if ($row['action']) {
                $this->availableActions[$row['action']] = WCF::getLanguage()->get('wcf.acp.jcoinsVoucher.action.' . $row['action']);
            }
        }
        \ksort($this->availableActions);
    }

    /**
     * @inheritdoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'username' => $this->username,
            'title' => $this->title,
            'action' => $this->action,
            'availableActions' => $this->availableActions,
        ]);
    }
}
