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
namespace wcf\system\bbcode;

use wcf\data\media\Media;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Parses the [jcvwsm] bbcode tag.
 */
/**
 * Parses the [wsm] bbcode tag.
 */
class JCoinsVoucherMediaBBCode extends AbstractBBCode
{
    /**
     * @inheritDoc
     */
    public function getParsedTag(array $openingTag, $content, array $closingTag, BBCodeParser $parser)
    {
        $mediaID = (!empty($openingTag['attributes'][0])) ? \intval($openingTag['attributes'][0]) : 0;
        if (!$mediaID) {
            return '';
        }

        /** @var Media $media */
        $media = MessageEmbeddedObjectManager::getInstance()->getObject('com.woltlab.wcf.media', $mediaID);

        if ($media !== null && $media->isAccessible()) {
            if ($media->isImage) {
                $thumbnailSize = (!empty($openingTag['attributes'][1])) ? $openingTag['attributes'][1] : 'original';
                $float = (!empty($openingTag['attributes'][2])) ? $openingTag['attributes'][2] : 'none';

                WCF::getTPL()->assign([
                    'float' => $float,
                    'media' => $media,
                    'thumbnailSize' => $thumbnailSize,
                ]);

                return WCF::getTPL()->fetch('jCoinsVoucherMediaBBCodeTag', 'wcf');
            }

            return StringUtil::getAnchorTag($media->getLink(), $media->getTitle());
        }

        return '';
    }
}
