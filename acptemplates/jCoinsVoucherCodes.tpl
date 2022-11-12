<section class="section">
    <header class="sectionHeader">
        <h2 class="sectionTitle">{lang}wcf.acp.jcoinsVoucher.code.data{/lang}</h2>
    </header>

    <p>{lang}wcf.acp.jcoinsVoucher.codes.link{/lang}<br>{$link}</p>
    <br>

    <p>{lang}wcf.acp.jcoinsVoucher.codes.total{/lang} {#$total}</p>
    <p>{lang}wcf.acp.jcoinsVoucher.codes.used{/lang} {#$used}</p>
</section>

<section class="section">
    <header class="sectionHeader">
        <h2 class="sectionTitle">{lang}wcf.acp.jcoinsVoucher.codes{/lang}</h2>
    </header>

    <ul>
        {foreach from=$codes item=code}
            <li>{if $code.redeemed}<del>{$code.code}</del>{else}{$code.code}{/if}</li>
        {/foreach}
    </ul>
</section>

<div class="formSubmit">
    <button class="jsJCoinsVoucherCodeButton buttonPrimary" accesskey="s">{lang}wcf.global.button.close{/lang}</button>
</div>
