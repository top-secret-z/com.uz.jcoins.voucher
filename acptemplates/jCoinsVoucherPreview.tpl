<div class="section">
	<ol class="jcoinsVoucherAcpList">
		<li>
			<div class="jcoinsVoucherAcpDiv">
				{if $subject}
					<div class="jcoinsVoucherAcpSubject htmlContent">
						{@$subject}
					</div>
				{/if}
				
				<div class="box128">
					{if $voucher->getImage() && $voucher->getImage()->hasThumbnail('tiny')}
						<div class="jcoinsVoucherAcpImage">{@$voucher->getImage()->getThumbnailTag('tiny')}</div>
					{/if}
					<div class="htmlContent">
						{@$content}
					</div>
				</div>
				
				{if $footer}
					<div class="jcoinsVoucherAcpFooter htmlContent">
						{@$footer}
					</div>
				{/if}
				
				<div class="jcoinsVoucherAcpAction">
					<div class="jsOnly jcoinsVoucherAcpRedeem">
						<a class="jsVoucherRedeem" data-voucher="{@$voucher->voucherID}">{lang}wcf.jcoins.voucher.item.redeem{/lang}</a>
					</div>
					
					<ul class="inlineList dotSeparated jcoinsVoucherAcpStats">
						<li>{lang}wcf.jcoins.voucher.item.jcoins{/lang}</li>
						<li>{lang}wcf.jcoins.voucher.item.date{/lang}</li>
						<li>{lang}wcf.jcoins.voucher.item.redeemed{/lang}</li>
					</ul>
				</div>
			</div>
		</li>
		<li>
			<div class="jcoinsVoucherAcpDiv jcoinsVoucherAcpRedeemed">
				{if $subject}
					<div class="jcoinsVoucherAcpSubject htmlContent">
						{@$subject}
					</div>
				{/if}
				
				<div class="box128">
					{if $voucher->getImage() && $voucher->getImage()->hasThumbnail('tiny')}
						<div class="jcoinsVoucherAcpImage">{@$voucher->getImage()->getThumbnailTag('tiny')}</div>
					{/if}
					<div class="htmlContent">
						{@$content}
					</div>
				</div>
				
				{if $footer}
					<div class="jcoinsVoucherAcpFooter htmlContent">
						{@$footer}
					</div>
				{/if}
				
				<div class="jcoinsVoucherAcpAction">
					<div class="jsOnly jcoinsVoucherAcpRedeem">
						<a class="jsVoucherRedeem" data-voucher="{@$voucher->voucherID}">{lang}wcf.acp.jcoinsVoucher.hasRedeemed{/lang}</a>
					</div>
					
					<ul class="inlineList dotSeparated jcoinsVoucherAcpStats">
						<li>{lang}wcf.jcoins.voucher.item.jcoins{/lang}</li>
						<li>{lang}wcf.jcoins.voucher.item.date{/lang}</li>
						<li>{lang}wcf.jcoins.voucher.item.redeemed{/lang}</li>
					</ul>
				</div>
			</div>
		</li>
	</ol>
</div>
<div class="formSubmit">
	<button class="jsJCoinsVoucherPreviewClose buttonPrimary" accesskey="s">{lang}wcf.global.button.cancel{/lang}</button>
</div>
