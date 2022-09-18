/**
 * Copies a JCoins voucher.
 * 
 * @author        2016-2022 Zaydowicz
 * @license        GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package        com.uz.jcoins.voucher
 */
define(['Ajax', 'Language', 'Ui/Confirmation', 'Ui/Notification'], function(Ajax, Language, UiConfirmation, UiNotification) {
    "use strict";

    function UZJCoinsVoucherAcpCopy() { this.init(); }

    UZJCoinsVoucherAcpCopy.prototype = {
        init: function() {
            var button = elBySel('.jsButtonCopy');

            button.addEventListener(WCF_CLICK_EVENT, this._click.bind(this));
        },

        _click: function(event) {
            event.preventDefault();
            var objectID = ~~elData(event.currentTarget, 'object-id');

            UiConfirmation.show({
                confirm: function() {
                    Ajax.apiOnce({
                        data: {
                            actionName: 'copy',
                            className: 'wcf\\data\\jcoins\\voucher\\JCoinsVoucherAction',
                            parameters: {
                                objectID: objectID
                            }
                        },
                        success: function(data) {
                            UiNotification.show();

                            window.location = data.returnValues.redirectURL;
                        }
                    });
                },
                message: Language.get('wcf.acp.jcoinsVoucher.copy.confirm')
            });
        }
    };
    return UZJCoinsVoucherAcpCopy;
});
