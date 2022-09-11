<?php 
namespace wcf\acp\page;
use wcf\data\jcoins\voucher\log\JCoinsVoucherLogList;
use wcf\page\SortablePage;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the JCoins Voucher log list page.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
class JCoinsVoucherLogListPage extends SortablePage {
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
	 * @var	integer
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
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['username'])) $this->username = StringUtil::trim($_REQUEST['username']);
		if (isset($_REQUEST['title'])) $this->title = StringUtil::trim($_REQUEST['title']);
		if (!empty($_REQUEST['action'])) $this->action = $_REQUEST['action'];
	}
	
	/**
	 * @inheritdoc
	 */
	protected function initObjectList() {
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
		$sql = "SELECT	DISTINCT	action
				FROM				wcf".WCF_N."_jcoins_voucher_log";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		while ($row = $statement->fetchArray()) {
			if ($row['action']) $this->availableActions[$row['action']] = WCF::getLanguage()->get('wcf.acp.jcoinsVoucher.action.' . $row['action']);
		}
		ksort($this->availableActions);
	}
	
	/**
	 * @inheritdoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'username' => $this->username,
				'title' => $this->title,
				'action' => $this->action,
				'availableActions' => $this->availableActions
		]);
	}
}
