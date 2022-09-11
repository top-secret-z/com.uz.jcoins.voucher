/**
 * Copies a JCoins voucher.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.voucher
 */
define(['Ajax', 'Language', 'Ui/Dialog'], function(Ajax, Language, UiDialog) {
	"use strict";
	
	function UZJCoinsVoucherAcpPreview() { this.init(); }
	
	UZJCoinsVoucherAcpPreview.prototype = {
			init: function() {
				var buttons = elBySelAll('.jsJCoinsVoucherPreview');
				for (var i = 0, length = buttons.length; i < length; i++) {
					buttons[i].addEventListener(WCF_CLICK_EVENT, this._showDialog.bind(this));
				}
			},
			
			/**
			 * Initializes the dialog.
			 */
			_showDialog: function(event) {
				event.preventDefault();
				
				Ajax.api(this, {
					actionName:	'loadPreview',
					parameters:	{
						voucherID:	~~elData(event.currentTarget, 'object-id')
					}
				});
			},
			
			_ajaxSuccess: function(data) {
				switch (data.actionName) {
					case 'loadPreview':
						this._render(data);
						break;
				}
			},
			
			/**
			 * Opens the configuration dialog.
			 */
			_render: function(data) {
				UiDialog.open(this, data.returnValues.template);
				
				var deleteButton = elBySel('.jsJCoinsVoucherPreviewClose');
				deleteButton.addEventListener(WCF_CLICK_EVENT, this._close.bind(this));
			},
			
			_close: function() {
				UiDialog.close(this);
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
					id: 		'voucherPreviewDialog',
					options: 	{ title: Language.get('wcf.acp.jcoinsVoucher.preview') },
					source: 	null
				};
			}
		};
	return UZJCoinsVoucherAcpPreview;
});
