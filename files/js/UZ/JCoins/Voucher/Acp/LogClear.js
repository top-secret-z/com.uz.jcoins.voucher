/**
 * Clears the voucher log.
 * 
 * @author        2016-2022 Zaydowicz
 * @license        GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package        com.uz.jcoins.voucher
 */
define(['Ajax', 'Language', 'Ui/Confirmation'], function(Ajax, Language, UiConfirmation) {
    "use strict";

    function UZJCoinsVoucherAcpLogClear() { this.init(); }

    UZJCoinsVoucherAcpLogClear.prototype = {
        init: function() {
            var button = elBySel('.jsVoucherLogClear');
            button.addEventListener(WCF_CLICK_EVENT, this._click.bind(this));
        },

        _click: function(event) {
            UiConfirmation.show({
                confirm: function() {
                    Ajax.apiOnce({
                        data: {
                            actionName: 'clearAll',
                            className: 'wcf\\data\\jcoins\\voucher\\log\\JCoinsVoucherLogAction'
                        },
                        success: function() {
                            window.location.reload();
                        }
                    });
                },
                message: Language.get('wcf.acp.jcoinsVoucher.log.clear.confirm')
            });    
        }
    };
    return UZJCoinsVoucherAcpLogClear;
});
