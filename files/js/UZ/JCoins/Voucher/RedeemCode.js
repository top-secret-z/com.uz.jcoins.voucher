/**
 * Dialog to redeem a JCoins code voucher
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
define(['Ajax', 'Language', 'Ui/Dialog', 'Ui/Notification'], function(Ajax, Language, UiDialog, UiNotification) {
	"use strict";
	
	function UZJCoinsVoucherRedeemCode() { this.init(); }
	
	UZJCoinsVoucherRedeemCode.prototype = {
		init: function() {
			this._item = 0;
			this._code = '';
			
			var buttons = elBySelAll('.jsVoucherRedeemCode');
			for (var i = 0, length = buttons.length; i < length; i++) {
				buttons[i].addEventListener(WCF_CLICK_EVENT, this._showDialog.bind(this));
			}
		},
		
		/**
		 * Submits the voucher.
		 */
		_submit: function() {
			this._code = elById('codeInput').value;
			
			Ajax.api(this, {
				actionName:	'redeemCode',
				parameters:	{
					voucher: this._item,
					code: this._code
				}
			});
		},
		
		/**
		 * cancel just closes the dialog
		 */
		_cancel: function() {
			UiDialog.close(this);
		},
		
		/**
		 * Initializes the redeem dialog.
		 */
		_showDialog: function(event) {
			event.preventDefault();
			
			this._item = ~~elData(event.currentTarget, 'voucher');
			
			Ajax.api(this, {
				actionName:	'getRedeemCodeDialog',
				parameters:	{
					voucher:	this._item
				}
			});
		},
		
		_ajaxSuccess: function(data) {
			switch (data.actionName) {
				case 'getRedeemCodeDialog':
					this._render(data);
					break;
				case 'redeemCode':
					var $codeError = elById('errorCode');
					$codeError.classList.remove("innerError");
					
					if (data.returnValues.codeError) {
						$codeError.classList.add("innerError");
						$codeError.innerHTML = Language.get('wcf.jcoins.voucher.codeError.'+data.returnValues.codeError);
						return;
					}
					
					UiNotification.show(Language.get('wcf.jcoins.voucher.success'));
					UiDialog.close(this);
					window.location.reload();
					break;
			}
		},
		
		/**
		 * Opens the redeem dialog.
		 */
		_render: function(data) {
			UiDialog.open(this, data.returnValues.template);
			
			var submitButton = elBySel('.jsSubmitRedeem');
			submitButton.addEventListener(WCF_CLICK_EVENT, this._submit.bind(this));
			
			var cancelButton = elBySel('.jsCancelRedeem');
			cancelButton.addEventListener(WCF_CLICK_EVENT, this._cancel.bind(this));
		},
		
		_ajaxSetup: function() {
			return {
				data: {
					className: 'wcf\\data\\jcoins\\voucher\\JCoinsVoucherAction',
				}
			};
		},
		
		_dialogSetup: function() {
			return {
				id: 		'redeemCodeDialog',
				options: 	{ title: Language.get('wcf.jcoins.voucher.item.redeem') },
				source: 	null
			};
		}
	};
	
	return UZJCoinsVoucherRedeemCode;
});
