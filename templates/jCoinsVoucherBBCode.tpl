<div class="voucherBox">
    <ul>
        {foreach from=$vouchers item=voucher}
            <li>
                <a href="{$voucher->getLink()}" class="box128">
                    {if $voucher->getImage() && $voucher->getImage()->hasThumbnail('tiny')}
                        <span class="voucherImg">{@$voucher->getImage()->getThumbnailTag('tiny')}</span>
                    {/if}

                    <div class="voucherDataContainer">
                        <span class="voucherSubject">{@$voucher->getBBCodeSubject()}</span>
                        <span class="voucherDate">{@$voucher->time|time}</span>
                        <span class="voucherContentType">{lang}wcf.jcoins.voucher.item.jcoins{/lang}</span>
                    </div>
                </a>
            </li>
        {/foreach}
    </ul>
</div>
