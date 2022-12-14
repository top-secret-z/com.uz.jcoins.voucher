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
namespace wcf\data\jcoins\voucher\content;

use wcf\data\DatabaseObject;
use wcf\data\language\Language;
use wcf\data\media\ViewableMedia;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\language\LanguageFactory;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\WCF;

/**
 * Represents a JCoins voucher content.
 */
class JCoinsVoucherContent extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'jcoins_voucher_content';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'contentID';

    /**
     * Returns the voucher's formatted content.
     */
    public function getFormattedContent()
    {
        $processor = new HtmlOutputProcessor();
        if ($this->hasEmbeddedObjects) {
            MessageEmbeddedObjectManager::getInstance()->loadObjects('com.uz.jcoins.voucher.content', [$this->contentID]);
        }
        $processor->process($this->content, 'com.uz.jcoins.voucher.content', $this->contentID);

        return $processor->getHtml();
    }

    /**
     * Returns the language of this voucher content or `null` if no language has been specified.
     */
    public function getLanguage()
    {
        if ($this->languageID) {
            return LanguageFactory::getInstance()->getLanguage($this->languageID);
        }

        return null;
    }

    /**
     * Returns the voucher's image if the active user can access it or `null`.
     */
    public function getImage()
    {
        if ($this->image === null) {
            if ($this->imageID) {
                $this->image = ViewableMedia::getMedia($this->imageID);
            }
        }

        if ($this->image === null || !$this->image->isAccessible()) {
            return null;
        }

        return $this->image;
    }

    /**
     * Sets the voucher's image.
     */
    public function setImage(ViewableMedia $image)
    {
        $this->image = $image;
    }

    /**
     * Returns a certain voucher content or `null` if it does not exist.
     */
    public static function getItemContent($voucherID, $languageID)
    {
        if ($languageID !== null) {
            $sql = "SELECT    *
                    FROM    wcf" . WCF_N . "_jcoins_voucher_content
                    WHERE    voucherID = ? AND languageID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$voucherID, $languageID]);
        } else {
            $sql = "SELECT    *
                    FROM    wcf" . WCF_N . "_jcoins_voucher_content
                    WHERE    voucherID = ? AND languageID IS NULL";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$voucherID]);
        }

        if (($row = $statement->fetchSingleRow()) !== false) {
            return new self(null, $row);
        }

        return null;
    }

    /**
     * Returns the voucher's subject.
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Returns the voucher's content.
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Returns the voucher's teaser.
     */
    public function getFooter()
    {
        return $this->footer;
    }
}
