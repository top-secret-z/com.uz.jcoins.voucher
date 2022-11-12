{include file='header' pageTitle='wcf.acp.menu.link.voucherJCoins.list'}

<script data-relocate="true">
    $(function() {
        new WCF.Action.Delete('wcf\\data\\jcoins\\voucher\\JCoinsVoucherAction', $('.jsItemRow'));
        new WCF.Action.Toggle('wcf\\data\\jcoins\\voucher\\JCoinsVoucherAction', $('.jsItemRow'));
    });
</script>

<script data-relocate="true">
    require(['Language', 'UZ/JCoins/Voucher/Acp/Preview'], function(Language, UZJCoinsVoucherAcpPreview) {
        Language.addObject({
            'wcf.jcoins.voucher.item.redeem': '{jslang}wcf.jcoins.voucher.item.redeem{/jslang}',
            'wcf.acp.jcoinsVoucher.preview': '{jslang}wcf.acp.jcoinsVoucher.preview{/jslang}'
        });
        new UZJCoinsVoucherAcpPreview();
    });
</script>

<script data-relocate="true">
    require(['Language', 'UZ/JCoins/Voucher/Acp/Codes'], function(Language, UZJCoinsVoucherAcpCodes) {
        Language.addObject({
            'wcf.acp.jcoinsVoucher.codes': '{jslang}wcf.acp.jcoinsVoucher.codes{/jslang}'
        });
        new UZJCoinsVoucherAcpCodes();
    });
</script>

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.menu.link.voucherJCoins.list{/lang}{if $items} <span class="badge badgeInverse">{#$items}</span>{/if}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='JCoinsVoucherAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.menu.link.voucherJCoins.add{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{hascontent}
    <div class="paginationTop">
        {content}{pages print=true assign=pagesLinks controller="JCoinsVoucherList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}{/content}
    </div>
{/hascontent}

{if $objects|count}
    <div class="section tabularBox">
        <table class="table">
            <thead>
                <tr>
                    <th class="columnID columnVoucherID{if $sortField == 'voucherID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='JCoinsVoucherList'}pageNo={@$pageNo}&sortField=voucherID&sortOrder={if $sortField == 'voucherID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
                    <th class="columnText columnTitle{if $sortField == 'title'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherList'}pageNo={@$pageNo}&sortField=title&sortOrder={if $sortField == 'title' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.jcoinsVoucher.title{/lang}</a></th>
                    <th class="columnText columnJCoins{if $sortField == 'jCoins'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherList'}pageNo={@$pageNo}&sortField=jCoins&sortOrder={if $sortField == 'jCoins' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.jcoinsVoucher.jcoins{/lang}</a></th>
                    <th class="columnText columnType{if $sortField == 'typeDes'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherList'}pageNo={@$pageNo}&sortField=typeDes&sortOrder={if $sortField == 'typeDes' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.jcoinsVoucher.type{/lang}</a></th>
                    <th class="columnText columnRedeemLimit{if $sortField == 'redeemLimit'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherList'}pageNo={@$pageNo}&sortField=redeemLimit&sortOrder={if $sortField == 'redeemLimit' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.jcoinsVoucher.limits{/lang}</a></th>
                    <th class="columnText columnExpiration{if $sortField == 'expirationDate'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherList'}pageNo={@$pageNo}&sortField=expirationDate&sortOrder={if $sortField == 'expirationDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.jcoinsVoucher.expiration{/lang}</a></th>
                    <th class="columnText columnRedeemed{if $sortField == 'redeemed'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherList'}pageNo={@$pageNo}&sortField=redeemed&sortOrder={if $sortField == 'redeemed' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.jcoinsVoucher.redemption{/lang}</a></th>
                </tr>
            </thead>

            <tbody>
                {foreach from=$objects item=voucherItem}
                    <tr class="jsItemRow">
                        <td class="columnIcon">
                            <span class="icon icon16 fa-{if !$voucherItem->isDisabled}check-{/if}square-o jsToggleButton jsTooltip pointer" title="{lang}wcf.global.button.{if $voucherItem->isDisabled}enable{else}disable{/if}{/lang}" data-object-id="{@$voucherItem->voucherID}" {if $voucherItem->isDisabled && !$voucherItem->isBlocked}data-confirm-message="{lang}wcf.acp.jcoinsVoucher.edit.limited{/lang}{/if}"></span>
                            <a href="{link controller='JCoinsVoucherEdit' object=$voucherItem}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
                            <span class="icon icon16 fa-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$voucherItem->voucherID}" data-confirm-message="{lang}wcf.acp.jcoinsVoucher.delete.sure{/lang}"></span>
                            <span class="icon icon16 fa-eye jsJCoinsVoucherPreview jsTooltip pointer" title="{lang}wcf.global.button.preview{/lang}" data-object-id="{@$voucherItem->voucherID}"></span>
                            {if $voucherItem->typeDes == 'code'}
                                <span class="icon icon16 fa-file-code-o jsJCoinsVoucherCodes jsTooltip pointer" title="{lang}wcf.acp.jcoinsVoucher.codes{/lang}" data-object-id="{@$voucherItem->voucherID}"></span>
                            {/if}
                        </td>
                        <td class="columnID columnVoucherID">{@$voucherItem->voucherID}</td>
                        <td class="columnText columnTitle">{$voucherItem->title}</td>
                        <td class="columnText columnJCoins">{@$voucherItem->jCoins}</td>
                        <td class="columnText columnType">{lang}wcf.acp.jcoinsVoucher.type.{$voucherItem->typeDes}{/lang}</td>
                        <td class="columnText columnRedeemLimit">{if $voucherItem->redeemLimit}{@$voucherItem->redeemLimit|shortUnit}{/if}</td>
                        <td class="columnText columnExpiration">{if $voucherItem->expirationStatus}{@$voucherItem->expirationDate|time}{/if}</td>
                        <td class="columnText columnRedeemed">{@$voucherItem->redeemed|shortUnit}</td>

                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>

    <footer class="contentFooter">
        {hascontent}
            <div class="paginationBottom">
                {content}{@$pagesLinks}{/content}
            </div>
        {/hascontent}

        <nav class="contentFooterNavigation">
            <ul>
                <li><a href="{link controller='JCoinsVoucherAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.jcoinsVoucher.add{/lang}</span></a></li>

                {event name='contentFooterNavigation'}
            </ul>
        </nav>
    </footer>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
