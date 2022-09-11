<?php
use wcf\data\category\CategoryEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\WCF;

/**
 * Installs default category.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */

// add default category
$sql = "SELECT	objectTypeID
		FROM	wcf".WCF_N."_object_type
		WHERE	definitionID = ? AND objectType = ?";
$statement = WCF::getDB()->prepareStatement($sql, 1);
$statement->execute([
		ObjectTypeCache::getInstance()->getDefinitionByName('com.woltlab.wcf.category')->definitionID,
		'com.uz.jcoins.voucher.category'
]);

CategoryEditor::create([
		'objectTypeID' => $statement->fetchColumn(),
		'title' => 'Default Category',
		'time' => TIME_NOW
]);
