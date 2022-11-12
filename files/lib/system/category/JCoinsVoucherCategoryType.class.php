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
namespace wcf\system\category;

use wcf\data\category\CategoryEditor;
use wcf\data\jcoins\voucher\JCoinsVoucherAction;
use wcf\data\jcoins\voucher\JCoinsVoucherList;
use wcf\system\WCF;

/**
 * Category type implementation for JCoins voucher categories.
 */
class JCoinsVoucherCategoryType extends AbstractCategoryType
{
    /**
     * @inheritDoc
     */
    protected $langVarPrefix = 'jcoins.voucher.category';

    /**
     * @inheritDoc
     */
    protected $forceDescription = false;

    /**
     * @inheritDoc
     */
    protected $maximumNestingLevel = 2;

    /**
     * @inheritDoc
     */
    public function afterDeletion(CategoryEditor $categoryEditor)
    {
        // delete vouchers with no categories
        $voucherList = new JCoinsVoucherList();
        $voucherList->enableCategoryLoading(false);
        $voucherList->sqlJoins = "LEFT JOIN wcf" . WCF_N . "_jcoins_voucher_to_category jcoins_voucher_to_category ON (jcoins_voucher_to_category.voucherID = jcoins_voucher.voucherID)";
        $voucherList->getConditionBuilder()->add("jcoins_voucher_to_category.categoryID IS NULL");
        $voucherList->readObjects();

        if (\count($voucherList)) {
            $action = new JCoinsVoucherAction($voucherList->getObjects(), 'delete');
            $action->executeAction();
        }

        parent::afterDeletion($categoryEditor);
    }

    /**
     * @inheritDoc
     */
    public function canAddCategory()
    {
        return $this->canEditCategory();
    }

    /**
     * @inheritDoc
     */
    public function canDeleteCategory()
    {
        return $this->canEditCategory();
    }

    /**
     * @inheritDoc
     */
    public function canEditCategory()
    {
        return WCF::getSession()->getPermission('admin.jcoins.voucher.canManage');
    }

    /**
     * @inheritDoc
     */
    public function changedParentCategories(array $categoryData)
    {
        // if category is moved to a new parent category, the vouchers in
        // the moved category need to be also assigned to this new parent
        // category
        $sql = "INSERT IGNORE INTO    wcf" . WCF_N . "_jcoins_voucher_to_category
                        (categoryID, voucherID)
                SELECT            ?, voucherID
                FROM            wcf" . WCF_N . "_jcoins_voucher_to_category
                WHERE            categoryID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();
        foreach ($categoryData as $categoryID => $parentCategoryData) {
            if ($parentCategoryData['newParentCategoryID']) {
                $statement->execute([
                    $parentCategoryData['newParentCategoryID'],
                    $categoryID,
                ]);
            }
        }
        WCF::getDB()->commitTransaction();
    }
}
