<?php
namespace wcf\system\package\plugin;
use wcf\data\jcoins\voucher\type\JCoinsVoucherTypeEditor;
use wcf\system\WCF;

/**
 * Installs, updates and deletes additional JCoins voucher types.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherTypePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	/**
	 * @inheritDoc
	 */
	public $className = JCoinsVoucherTypeEditor::class;
	
	/**
	 * @inheritDoc
	 */
	public $tableName = 'jcoins_voucher_type';
	
	/**
	 * @inheritDoc
	 */
	public $tagName = 'jCoinsVoucher';
	
	/**
	 * @inheritDoc
	 */
	protected function handleDelete(array $items) {
		$sql = "DELETE FROM	wcf".WCF_N."_".$this->tableName."
				WHERE		typeTitle = ?
							AND packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		foreach ($items as $item) {
			$statement->execute([$item['attributes']['name'], $this->installation->getPackageID()
			]);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	protected function prepareImport(array $data) {
		return [
				'typeTitle' => $data['attributes']['name'],
				'typeID' => $data['elements']['typeID'],
				'period' => $data['elements']['period'],
				'raffle' => $data['elements']['raffle'],
				'redeemLimit' => $data['elements']['redeemLimit'],
				'sortOrder' => $data['elements']['sortOrder']
		];
	}
	
	/**
	 * @inheritDoc
	 */
	protected function findExistingItem(array $data) {
		$sql = "SELECT	*
				FROM	wcf".WCF_N."_".$this->tableName."
				WHERE	typeTitle = ?
						AND packageID = ?";
		$parameters = [
				$data['typeTitle'],
				$this->installation->getPackageID()
		];
		
		return [
				'sql' => $sql,
				'parameters' => $parameters
		];
	}
}
