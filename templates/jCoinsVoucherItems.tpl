{foreach from=$objects item=voucher}
    <li>
        {if $voucher->typeDes == 'code'}{assign var=redeemed value=$voucher->userRedeemedTimes()}{/if}

        {if $voucher->typeDes == 'code'}
            <div class="jcoinsVoucherDiv{if $redeemed >= $voucher->codeUserLimit} jcoinsVoucherRedeemed{elseif $voucher->isExpired} jcoinsVoucherExpired{/if}">
        {else}
            <div class="jcoinsVoucherDiv{if $voucher->hasRedeemed()} jcoinsVoucherRedeemed{elseif $voucher->isExpired} jcoinsVoucherExpired{/if}">
        {/if}

            {if $voucher->getSubject()}
                <div class="jcoinsVoucherSubject htmlContent">
                    {@$voucher->getSubject()}
                </div>
            {/if}

            <div class="box128">
                {if $voucher->getImage() && $voucher->getImage()->hasThumbnail('tiny')}
                    <div class="jcoinsVoucherImage">{@$voucher->getImage()->getThumbnailTag('tiny')}</div>
                {/if}
                <div class="htmlContent">
                    {@$voucher->getContent()}
                </div>
            </div>

            {if $voucher->getFooter()}
                <div class="jcoinsVoucherFooter htmlContent">
                    {@$voucher->getFooter()}
                </div>
            {/if}

            <div class="jcoinsVoucherAction">
                {if $voucher->typeDes == 'code'}
                    {if $redeemed == $voucher->codeUserLimit}
                        <div class="jcoinsVoucherRedeem"><p>{lang}wcf.jcoins.voucher.item.hasRedeemed{/lang}</p></div>
                    {elseif $voucher->isExpired}
                        <div class="jcoinsVoucherRedeem"><p>{lang}wcf.jcoins.voucher.item.hasExpired{/lang}</p></div>
                    {elseif $redeemed}
                        <div class="jsOnly jcoinsVoucherRedeem"><a class="jsVoucherRedeemCode" data-voucher="{@$voucher->voucherID}">{lang}wcf.jcoins.voucher.item.redeem.again{/lang}</a></div>
                    {else}
                        <div class="jsOnly jcoinsVoucherRedeem"><a class="jsVoucherRedeemCode" data-voucher="{@$voucher->voucherID}">{lang}wcf.jcoins.voucher.item.redeem{/lang}</a></div>
                    {/if}
                {elseif $voucher->hasRedeemed()}
                    <div class="jcoinsVoucherRedeem"><p>{lang}wcf.jcoins.voucher.item.hasRedeemed{/lang}</p></div>
                {elseif $voucher->isExpired}
                    <div class="jcoinsVoucherRedeem"><p>{lang}wcf.jcoins.voucher.item.hasExpired{/lang}</p></div>
                {else}
                    <div class="jsOnly jcoinsVoucherRedeem"><a class="jsVoucherRedeem" data-voucher="{@$voucher->voucherID}">{lang}wcf.jcoins.voucher.item.redeem{/lang}</a></div>
                {/if}

                <ul class="inlineList dotSeparated jcoinsVoucherStats">
                    <li>{lang}wcf.jcoins.voucher.item.jcoins{/lang}</li>
                    <li>{lang}wcf.jcoins.voucher.item.date{/lang}</li>
                    <li>{lang}wcf.jcoins.voucher.item.redeemed{/lang}</li>
                </ul>
            </div>
        </div>
    </li>
{/foreach}
