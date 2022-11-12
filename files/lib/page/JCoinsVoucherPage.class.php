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
namespace wcf\page;

use wcf\data\jcoins\voucher\category\JCoinsVoucherCategory;
use wcf\data\jcoins\voucher\category\JCoinsVoucherCategoryNodeTree;
use wcf\data\jcoins\voucher\JCoinsVoucher;
use wcf\data\jcoins\voucher\JCoinsVoucherUserList;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\page\PageLocationManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * JCoins Voucher Page.
 */
class JCoinsVoucherPage extends SortablePage
{
    /**
     * @inheritdoc
     */
    public $loginRequired = true;

    /**
     * @inheritdoc
     */
    public $neededModules = ['MODULE_JCOINS', 'MODULE_JCOINS_VOUCHER'];

    /**
     * @inheritdoc
     */
    public $neededPermissions = ['user.jcoins.voucher.canSee'];

    /**
     * @inheritdoc
     */
    public $itemsPerPage = JCOINS_VOUCHER_ITEMS_PER_PAGE;

    /**
     * @inheritdoc
     */
    public $validSortFields = ['changeTime', 'jCoins', 'redeemed'];

    /**
     * @inheritdoc
     */
    public $defaultSortField = 'changeTime';

    /**
     * @inheritdoc
     */
    public $defaultSortOrder = 'DESC';

    /**
     * @inheritdoc
     */
    public $objectListClassName = JCoinsVoucherUserList::class;

    /**
     * category list
     */
    public $categoryList;

    public $categoryID = 0;

    public $category;

    /**
     * voucher data
     */
    public $voucherID = 0;

    public $voucher;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (!empty($_REQUEST['categoryID'])) {
            $this->categoryID = \intval($_REQUEST['categoryID']);
            $this->category = JCoinsVoucherCategory::getCategory($this->categoryID);
            if ($this->category === null) {
                throw new IllegalLinkException();
            }
            if (!$this->category->isAccessible()) {
                throw new PermissionDeniedException();
            }
        }

        if (!empty($_REQUEST['voucherID'])) {
            $this->voucherID = \intval($_REQUEST['voucherID']);
            $this->voucher = new JCoinsVoucher($this->voucherID);
            if ($this->voucher === null) {
                throw new IllegalLinkException();
            }
        }

        $linkParameters = 'sortField=' . $this->sortField . '&sortOrder=' . $this->sortOrder . '&pageNo=' . $this->pageNo;
        if ($this->category) {
            $linkParameters .= '&categoryID=' . $this->category->categoryID;
        }

        if ($this->voucher) {
            $linkParameters .= '&voucherID=' . $this->voucherID;
        }

        $this->setCanonicalURL($linkParameters);
    }

    /**
     * Sets/enforces the canonical url of the page.
     *
     * @param    string        $linkParameters
     */
    protected function setCanonicalURL($linkParameters)
    {
        if (empty($_POST)) {
            $this->canonicalURL = LinkHandler::getInstance()->getLink('JCoinsVoucher', [
                'application' => 'wcf',
            ], ($this->pageNo ? 'pageNo=' . $this->pageNo : ''));
        } else {
            HeaderUtil::redirect(LinkHandler::getInstance()->getLink('JCoinsVoucher', [
                'application' => 'wcf',
            ], $linkParameters));

            exit;
        }
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        if ($this->category) {
            $this->objectList->getConditionBuilder()->add('jcoins_voucher.voucherID IN (SELECT voucherID FROM wcf' . WCF_N . '_jcoins_voucher_to_category WHERE categoryID = ?)', [$this->category->categoryID]);
        } elseif ($this->voucher) {
            $this->objectList->getConditionBuilder()->add('jcoins_voucher.voucherID = ?', [$this->voucherID]);
        }
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        // get categories
        $categoryTree = new JCoinsVoucherCategoryNodeTree('com.uz.jcoins.voucher.category');
        $this->categoryList = $categoryTree->getIterator();
        $this->categoryList->setMaxDepth(0);

        if ($this->category || $this->voucher) {
            $this->setLocation();
        }
    }

    /**
     * Sets the page location data.
     */
    protected function setLocation()
    {
        if ($this->category || $this->voucher) {
            // `-1` = pseudo object id to have (second) page with identifier `com.uz.jcoins.JCoinsVoucher`
            PageLocationManager::getInstance()->addParentLocation('com.uz.jcoins.JCoinsVoucher', -1);
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'controllerObject' => null,
            'controllerName' => 'JCoinsVoucher',
            'categoryList' => $this->categoryList,
            'category' => $this->category,
            'categoryID' => $this->categoryID,
            'voucher' => $this->voucher,
        ]);
    }
}
