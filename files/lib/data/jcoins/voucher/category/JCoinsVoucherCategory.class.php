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

use wcf\data\category\AbstractDecoratedCategory;
use wcf\data\ITitledLinkObject;
use wcf\data\user\User;
use wcf\system\category\CategoryHandler;
use wcf\system\request\LinkHandler;

/**
 * Represents a JCoins Voucher category
 */
class JCoinsVoucherCategory extends AbstractDecoratedCategory implements ITitledLinkObject
{
    /**
     * object type name of the categories
     */
    const OBJECT_TYPE_NAME = 'com.uz.jcoins.voucher.category';

    /**
     * @inheritDoc
     */
    public function getLink()
    {
        return LinkHandler::getInstance()->getLink('CategoryJCoinsVoucherList', [
            'application' => 'wcf',
            'forceFrontend' => true,
            'object' => $this->getDecoratedObject(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getDecoratedObject()->getTitle();
    }

    /**
     * Returns true if the category is accessible for the given user. If no
     * user is given, the active user is checked.
     */
    public function isAccessible(?User $user = null)
    {
        if ($this->getObjectType()->objectType != self::OBJECT_TYPE_NAME) {
            return false;
        }

        // check permissions
        return $this->getPermission('canViewCategory', $user);
    }

    /**
     * Returns a list with ids of accessible categories.
     */
    public static function getAccessibleCategoryIDs(array $permissions = ['canViewCategory'])
    {
        $categoryIDs = [];
        foreach (CategoryHandler::getInstance()->getCategories(self::OBJECT_TYPE_NAME) as $category) {
            $result = true;
            $category = new self($category);
            foreach ($permissions as $permission) {
                $result = $result && $category->getPermission($permission);
            }

            if ($result) {
                $categoryIDs[] = $category->categoryID;
            }
        }

        return $categoryIDs;
    }
}
