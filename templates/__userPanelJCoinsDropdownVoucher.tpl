{if MODULE_JCOINS_VOUCHER && $__wcf->session->getPermission('user.jcoins.voucher.canSee')}
    $panel._createNewLink('panelJCoinsVoucherLink', '{link controller='JCoinsVoucher' encode=false}{/link}', '{capture assign=JCoinsVoucherTitle}{lang}wcf.jcoins.voucher.page{/lang}{/capture}{@$JCoinsVoucherTitle|encodeJS}', 'fa-money');
{/if}
