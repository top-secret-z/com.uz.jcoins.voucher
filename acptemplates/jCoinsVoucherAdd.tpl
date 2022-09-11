{include file='header' pageTitle='wcf.acp.jcoinsVoucher.'|concat:$action}

<script data-relocate="true">
	$(function() {
		$('input[type="radio"][name="publicationStatus"]').change(function(event) {
			var $selected = $('input[type="radio"][name="publicationStatus"]:checked');
			if ($selected.length > 0) {
				if ($selected.val() == 1) {
					$('#publicationDateDl').show();
				}
				else {
					$('#publicationDateDl').hide();
				}
			}
		}).trigger('change');
	});
</script>

<script data-relocate="true">
	$(function() {
		$('input[type="radio"][name="expirationStatus"]').change(function(event) {
			var $selected = $('input[type="radio"][name="expirationStatus"]:checked');
			if ($selected.length > 0) {
				if ($selected.val() == 1) {
					$('#expirationDateDl').show();
				}
				else {
					$('#expirationDateDl').hide();
				}
			}
		}).trigger('change');
	});
</script>

{if $__wcf->session->getPermission('admin.content.cms.canUseMedia')}
	<script data-relocate="true">
		{include file='mediaJavaScript'}
		
		require(['WoltLabSuite/Core/Media/Manager/Select'], function(MediaManagerSelect) {
			new MediaManagerSelect({
				dialogTitle: '{lang}wcf.media.chooseImage{/lang}',
				imagesOnly: 1
			});
		});
	</script>
{/if}

{if $action == 'edit'}
	<script data-relocate="true">
		require(['Language', 'UZ/JCoins/Voucher/Acp/Copy'], function(Language, UZJCoinsVoucherAcpCopy) {
			Language.addObject({
				'wcf.acp.jcoinsVoucher.copy.confirm': '{jslang}wcf.acp.jcoinsVoucher.copy.confirm{/jslang}'
			});
			new UZJCoinsVoucherAcpCopy();
		});
	</script>
{/if}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.jcoinsVoucher.{$action}{/lang}</h1>
		{if $action == 'edit' && $voucher->isBlocked}
			<p class="contentDescription">{lang}wcf.acp.jcoinsVoucher.edit.limited{/lang}</p>
		{/if}
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			{if $action == 'edit'}
				<li><a class="jsButtonCopy button" data-object-id="{@$voucherID}"><span class="icon icon16 fa-files-o"></span> <span>{lang}wcf.acp.jcoinsVoucher.copy{/lang}</span></a></li>
			{/if}
			<li><a href="{link controller='JCoinsVoucherList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.acp.jcoinsVoucher.list{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<form id="formContainer" method="post" action="{if $action == 'add'}{link controller='JCoinsVoucherAdd'}{/link}{else}{link controller='JCoinsVoucherEdit' id=$voucher->voucherID}{/link}{/if}">
	<div class="section tabMenuContainer">
		<nav class="tabMenu">
			<ul>
				<li><a href="{@$__wcf->getAnchor('generalData')}">{lang}wcf.acp.jcoinsVoucher.general{/lang}</a></li>
				{if $action === 'add' || !$voucher->isBlocked}
					<li><a href="{@$__wcf->getAnchor('typeData')}">{lang}wcf.acp.jcoinsVoucher.type{/lang}</a></li>
					<li><a href="{@$__wcf->getAnchor('limitData')}">{lang}wcf.acp.jcoinsVoucher.limits{/lang}</a></li>
				{/if}
				<li><a href="{@$__wcf->getAnchor('textData')}">{lang}wcf.acp.jcoinsVoucher.text{/lang}</a></li>
				{if $action == 'edit' && $voucher->isBlocked}
					<li><a href="{@$__wcf->getAnchor('configData')}">{lang}wcf.acp.jcoinsVoucher.config{/lang}</a></li>
				{/if}
			</ul>
		</nav>
		
		<div id="generalData" class="tabMenuContent hidden">
			<div class="section">
				<dl{if $errorField == 'title'} class="formError"{/if}>
					<dt><label for="title">{lang}wcf.acp.jcoinsVoucher.title{/lang}</label></dt>
					<dd>
						<input type="text" id="title" name="title" value="{$title}" maxlength="80" class="long" />
						<small>{lang}wcf.acp.jcoinsVoucher.title.description{/lang}</small>
						
						{if $errorField == 'title'}
							<small class="innerError">
								{lang}wcf.acp.jcoinsVoucher.title.error.{@$errorType}{/lang}
							</small>
						{/if}
					</dd>
				</dl>
				
				<dl>
					<dt><label for="jCoins">{lang}wcf.acp.jcoinsVoucher.jcoins{/lang}</label></dt>
					<dd>
						<input type="number" name="jCoins" id="jCoins" value="{$jCoins}" min="1" class="small" />
						<small>{lang}wcf.acp.jcoinsVoucher.jcoins.description{/lang}</small>
					</dd>
				</dl>
				
				{if $action === 'add' || !$voucher->isBlocked}
					<dl>
						<dt><label for="publicationStatus">{lang}wcf.acp.jcoinsVoucher.publicationStatus{/lang}</label></dt>
						<dd class="floated">
							<label><input type="radio" name="publicationStatus" value="0"{if $publicationStatus == 0} checked{/if}> {lang}wcf.acp.jcoinsVoucher.publicationStatus.no{/lang}</label>
							<label><input type="radio" name="publicationStatus" value="1"{if $publicationStatus == 1} checked{/if}> {lang}wcf.acp.jcoinsVoucher.publicationStatus.yes{/lang}</label>
						</dd>
					</dl>
					
					<dl id="publicationDateDl"{if $errorField == 'publicationDate'} class="formError"{/if}{if $publicationStatus != 1} style="display: none"{/if}>
						<dt><label for="publicationDate">{lang}wcf.acp.jcoinsVoucher.publicationDate{/lang}</label></dt>
						<dd>
							<input type="datetime" id="publicationDate" name="publicationDate" value="{$publicationDate}" class="medium">
							{if $errorField == 'publicationDate'}
								<small class="innerError">
									{if $errorType == 'empty'}
										{lang}wcf.global.form.error.empty{/lang}
									{else}
										{lang}wcf.acp.jcoinsVoucher.publicationDate.error.{@$errorType}{/lang}
									{/if}
								</small>
							{/if}
						</dd>
					</dl>
				{/if}
			</div>
			
			<div class="section">
				<h2 class="sectionTitle">{lang}wcf.acp.jcoinsVoucher.categories{/lang}</h2>
				
				{include file='jCoinsVoucherFlexibleCategoryList'}
				
				{if $errorField == 'categoryIDs'}
					<small class="innerError">
						{if $errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{else}
							{lang}wcf.acp.jcoinsVoucher.categories.error.{@$errorType}{/lang}
						{/if}
					</small>
				{/if}
			</div>
		</div>
		
		<div id="typeData" class="tabMenuContent hidden">
			<div class="section">
				<dl{if $errorField == 'typeID'} class="formError"{/if}>
					<dt><label for="typeID">{lang}wcf.acp.jcoinsVoucher.type{/lang}</label></dt>
					<dd>
						<select name="typeID" id="typeID">
							<option value="0">{lang}wcf.global.noSelection{/lang}</option>
							{foreach from=$availableTypes item=type}
								<option value="{@$type->typeID}"{if $type->typeID == $typeID} selected="selected"{/if}>{$type->getTitle()}</option>
							{/foreach}
						</select>
						
						<small class="birthdaySetting">{lang}wcf.acp.jcoinsVoucher.type.birthday.description{/lang}</small>
						<small class="membershipSetting">{lang}wcf.acp.jcoinsVoucher.type.membership.description{/lang}</small>
						<small class="normalSetting">{lang}wcf.acp.jcoinsVoucher.type.normal.description{/lang}</small>
						<small class="recurringSetting">{lang}wcf.acp.jcoinsVoucher.type.recurring.description{/lang}</small>
						<small class="registrationSetting">{lang}wcf.acp.jcoinsVoucher.type.registration.description{/lang}</small>
						
						{if $errorField == 'typeID'}
							<small class="innerError">
								{lang}wcf.acp.jcoinsVoucher.type.error.{@$errorType}{/lang}
							</small>
						{/if}
					</dd>
				</dl>
			</div>
			
			<div class="section recurringSetting">
				<dl>
					<dt><label for="period">{lang}wcf.acp.jcoinsVoucher.period{/lang}</label></dt>
					<dd>
						<input type="number" id="period" name="period" value="{$period}" class="tiny" min="1" />
						<select name="periodUnit" id="periodUnit">
							{foreach from=$availablePeriods item=periodType}
								<option value={$periodType}{if $periodType == $periodUnit} selected="selected"{/if}>{lang}wcf.acp.jcoinsVoucher.period.{$periodType}{/lang}</option>
							{/foreach}
						</select>
					</dd>
				</dl>
			</div>
			
			<div class="section codeSetting">
				<dl{if $errorField == 'codePrefix'} class="formError"{/if}>
					<dt><label for="codePrefix">{lang}wcf.acp.jcoinsVoucher.code.prefix{/lang}</label></dt>
					<dd>
						<input type="text" id="codePrefix" name="codePrefix" value="{$codePrefix}" maxlength="5" class="tiny" />
						<small>{lang}wcf.acp.jcoinsVoucher.code.prefix.description{/lang}</small>
						
						{if $errorField == 'codePrefix'}
							<small class="innerError">
								{lang}wcf.acp.jcoinsVoucher.code.prefix.error.{@$errorType}{/lang}
							</small>
						{/if}
					</dd>
				</dl>
				
				<dl>
					<dt><label for="codeNumber">{lang}wcf.acp.jcoinsVoucher.code.codeNumber{/lang}</label></dt>
					<dd>
						<input type="number" name="codeNumber" id="codeNumber" value="{$codeNumber}" min="1" class="tiny" />
						<small>{lang}wcf.acp.jcoinsVoucher.code.codeNumber.description{/lang}</small>
					</dd>
				</dl>
				
				<dl>
					<dt><label for="codeRedeemLimit">{lang}wcf.acp.jcoinsVoucher.code.codeRedeemLimit{/lang}</label></dt>
					<dd>
						<input type="number" name="codeRedeemLimit" id="codeRedeemLimit" value="{$codeRedeemLimit}" min="1" class="tiny" />
						<small>{lang}wcf.acp.jcoinsVoucher.code.codeRedeemLimit.description{/lang}</small>
					</dd>
				</dl>
				
				<dl>
					<dt><label for="codeUserLimit">{lang}wcf.acp.jcoinsVoucher.code.codeUserLimit{/lang}</label></dt>
					<dd>
						<input type="number" name="codeUserLimit" id="codeUserLimit" value="{$codeUserLimit}" min="1" class="tiny" />
						<small>{lang}wcf.acp.jcoinsVoucher.code.codeUserLimit.description{/lang}</small>
					</dd>
				</dl>
			</div>
			
			<div class="section raffleSetting">
				<dl>
					<dt><label for="raffle">{lang}wcf.acp.jcoinsVoucher.raffle{/lang}</label></dt>
					<dd>
						<label><input type="checkbox" name="raffle" id="raffle" value="1"{if $raffle} checked{/if}> {lang}wcf.acp.jcoinsVoucher.raffle.enable{/lang}</label>
						<small>{lang}wcf.acp.jcoinsVoucher.raffle.description{/lang}</small>
					</dd>
				</dl>
			</div>
			
			<div class="section notifySetting">
				<dl>
					<dt><label for="notify">{lang}wcf.acp.jcoinsVoucher.notify{/lang}</label></dt>
					<dd>
						<label><input type="checkbox" name="notify" id="notify" value="1"{if $notify} checked{/if}> {lang}wcf.acp.jcoinsVoucher.notify.enable{/lang}</label>
						<small>{lang}wcf.acp.jcoinsVoucher.notify.description{/lang}</small>
					</dd>
				</dl>
			</div>
		</div>
		
		<div id="limitData" class="tabMenuContent hidden">
			<div class="section redemptionSetting">
				<header class="sectionHeader">
					<h2 class="sectionTitle">{lang}wcf.acp.jcoinsVoucher.redemption{/lang}</h2>
				</header>
				
				<dl{if $errorField == 'redeemLimit'} class="formError"{/if}>
					<dt><label for="redeemLimit">{lang}wcf.acp.jcoinsVoucher.redemption.limit{/lang}</label></dt>
					<dd>
						<input type="number" name="redeemLimit" id="redeemLimit" value="{$redeemLimit}" min="0" class="tiny" />
						<small>{lang}wcf.acp.jcoinsVoucher.redemption.limit.description{/lang}</small>
						
						{if $errorField == 'redeemLimit'}
							<small class="innerError">
								{lang}wcf.acp.jcoinsVoucher.redemption.limit.error.{@$errorType}{/lang}
							</small>
						{/if}
					</dd>
				</dl>
			</div>
			
			<div class="section">
				<header class="sectionHeader">
					<h2 class="sectionTitle">{lang}wcf.acp.jcoinsVoucher.expiration{/lang}</h2>
				</header>
				
				<dl>
					<dt><label for="expirationStatus">{lang}wcf.acp.jcoinsVoucher.expiration.status{/lang}</label></dt>
					<dd class="floated">
						<label><input type="radio" name="expirationStatus" value="0"{if $expirationStatus == 0} checked{/if}> {lang}wcf.acp.jcoinsVoucher.expiration.status.no{/lang}</label>
						<label><input type="radio" name="expirationStatus" value="1"{if $expirationStatus == 1} checked{/if}> {lang}wcf.acp.jcoinsVoucher.expiration.status.date{/lang}</label>
					</dd>
				</dl>
				
				<dl id="expirationDateDl"{if $errorField == 'expirationDate'} class="formError"{/if}{if $expirationStatus != 1} style="display: none"{/if}>
					<dt><label for="expirationDate">{lang}wcf.acp.jcoinsVoucher.expiration.date{/lang}</label></dt>
					<dd>
						<input type="datetime" id="expirationDate" name="expirationDate" value="{$expirationDate}" class="medium">
						{if $errorField == 'expirationDate'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.acp.jcoinsVoucher.expiration.date.error.{@$errorType}{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>
			</div>
			
			<div class="section">
				<header class="sectionHeader">
					<h2 class="sectionTitle">{lang}wcf.acp.jcoinsVoucher.conditions{/lang}</h2>
				</header>
				
				{include file='userConditions'}
			</div>
		</div>
		
		{if $action == 'edit' && $voucher->isBlocked}
			<div id="configData" class="tabMenuContent hidden">
				<div class="section">
					<dl>
						<dt><label>{lang}wcf.acp.jcoinsVoucher.publicationDate{/lang}</label></dt>
						<dd>
							{if $voucher->publicationStatus}{@$voucher->publicationDate|plainTime}{else}{@$voucher->time|plainTime}{/if}
						</dd>
					</dl>
					
					<dl>
						<dt><label>{lang}wcf.acp.jcoinsVoucher.redemption{/lang}</label></dt>
						<dd>
							{if $voucher->typeDes == 'recurring' && $voucher->redeemLimit}
								{lang}wcf.acp.jcoinsVoucher.redemption.limit.recurring{/lang}
							{else}
								{$voucher->redeemLimit}
							{/if}
						</dd>
					</dl>
					
					<dl>
						<dt><label>{lang}wcf.acp.jcoinsVoucher.expiration{/lang}</label></dt>
						<dd>
							{if $voucher->expirationStatus}{@$voucher->expirationDate|plainTime}{else}{lang}wcf.acp.jcoinsVoucher.no{/lang}{/if}
						</dd>
					</dl>
					
					<dl>
						<dt><label>{lang}wcf.acp.jcoinsVoucher.type{/lang}</label></dt>
						<dd>
							{lang}wcf.acp.jcoinsVoucher.type.{$voucher->typeDes}{/lang}
						</dd>
					</dl>
					
					{if $voucher->typeDes == 'normal'}
						<dl>
							<dt><label>{lang}wcf.acp.jcoinsVoucher.raffle{/lang}</label></dt>
							<dd>
								{lang}wcf.acp.jcoinsVoucher.{if $voucher->raffle}yes{else}no{/if}{/lang}
							</dd>
						</dl>
						<dl>
							<dt><label>{lang}wcf.acp.jcoinsVoucher.notify{/lang}</label></dt>
							<dd>
								{lang}wcf.acp.jcoinsVoucher.{if $voucher->notify}yes{else}no{/if}{/lang}
							</dd>
						</dl>
					{/if}
					
					{if $voucher->typeDes == 'recurring'}
						<dl>
							<dt><label>{lang}wcf.acp.jcoinsVoucher.period{/lang}</label></dt>
							<dd>
								{#$voucher->period} {lang}wcf.acp.jcoinsVoucher.period.{$voucher->periodUnit}{/lang}
							</dd>
						</dl>
						<dl>
							<dt><label>{lang}wcf.acp.jcoinsVoucher.raffle{/lang}</label></dt>
							<dd>
								{lang}wcf.acp.jcoinsVoucher.{if $voucher->raffle}yes{else}no{/if}{/lang}
							</dd>
						</dl>
						<dl>
							<dt><label>{lang}wcf.acp.jcoinsVoucher.notify{/lang}</label></dt>
							<dd>
								{lang}wcf.acp.jcoinsVoucher.{if $voucher->notify}yes{else}no{/if}{/lang}
							</dd>
						</dl>
					{/if}
					
					{if $voucher->typeDes == 'birthday'}
						<dl>
							<dt><label>{lang}wcf.acp.jcoinsVoucher.notify{/lang}</label></dt>
							<dd>
								{lang}wcf.acp.jcoinsVoucher.{if $voucher->notify}yes{else}no{/if}{/lang}
							</dd>
						</dl>
					{/if}
					
					{if $voucher->typeDes == 'membership'}
						<dl>
							<dt><label>{lang}wcf.acp.jcoinsVoucher.notify{/lang}</label></dt>
							<dd>
								{lang}wcf.acp.jcoinsVoucher.{if $voucher->notify}yes{else}no{/if}{/lang}
							</dd>
						</dl>
					{/if}
					
					{if $voucher->typeDes == 'registration'}
						<dl>
							<dt><label>{lang}wcf.acp.jcoinsVoucher.notify{/lang}</label></dt>
							<dd>
								{lang}wcf.acp.jcoinsVoucher.{if $voucher->notify}yes{else}no{/if}{/lang}
							</dd>
						</dl>
					{/if}
					
					{if $voucher->typeDes == 'code'}
						<dl>
							<dt><label>{lang}wcf.acp.jcoinsVoucher.code.prefix{/lang}</label></dt>
							<dd>
								{$voucher->codePrefix}
							</dd>
						</dl>
						
						<dl>
							<dt><label>{lang}wcf.acp.jcoinsVoucher.code.codeNumber{/lang}</label></dt>
							<dd>
								{$voucher->codeNumber}
							</dd>
						</dl>
						
						<dl>
							<dt><label>{lang}wcf.acp.jcoinsVoucher.code.codeRedeemLimit{/lang}</label></dt>
							<dd>
								{$voucher->codeRedeemLimit}
							</dd>
						</dl>
						
						<dl>
							<dt><label>{lang}wcf.acp.jcoinsVoucher.code.codeUserLimit{/lang}</label></dt>
							<dd>
								{$voucher->codeUserLimit}
							</dd>
						</dl>
					{/if}
				</div>
			</div>
		{/if}
		
		<div id="textData" class="tabMenuContent hidden">
			<div class="section">
				<div class="section">
					{if !$isMultilingual}
						{if $__wcf->session->getPermission('admin.content.cms.canUseMedia')}
							<dl{if $errorField == 'image'} class="formError"{/if}>
								<dt><label for="image">{lang}wcf.acp.jcoinsVoucher.image{/lang}</label></dt>
								<dd>
									<div id="imageDisplay" class="selectedImagePreview">
										{if $images[0]|isset && $images[0]->hasThumbnail('small')}
											{@$images[0]->getThumbnailTag('small')}
										{/if}
									</div>
									<p class="button jsMediaSelectButton" data-store="imageID0" data-display="imageDisplay">{lang}wcf.media.chooseImage{/lang}</p>
									<small>{lang}wcf.acp.jcoinsVoucher.image.description{/lang}</small>
									<input type="hidden" name="imageID[0]" id="imageID0"{if $imageID[0]|isset} value="{@$imageID[0]}"{/if}>
									{if $errorField == 'image'}
										<small class="innerError">{lang}wcf.acp.jcoinsVoucher.image.error.{@$errorType}{/lang}</small>
									{/if}
								</dd>
							</dl>
						{elseif $action == 'edit' && $images[0]|isset && $images[0]->hasThumbnail('small')}
							<dl>
								<dt>{lang}wcf.acp.jcoinsVoucher.image{/lang}</dt>
								<dd>
									<div id="imageDisplay">{@$images[0]->getThumbnailTag('small')}</div>
								</dd>
							</dl>
						{/if}
						
						<!-- subject -->
						<dl{if $errorField == 'subject'} class="formError"{/if}>
							<dt><label for="subject0">{lang}wcf.acp.jcoinsVoucher.subject{/lang}</label></dt>
							<dd>
								<textarea name="subject[0]" id="subject0" class="wysiwygTextarea">{if !$subject[0]|empty}{$subject[0]}{/if}</textarea>
								{include file='wysiwyg' wysiwygSelector='subject0'}
								
								{if $errorField == 'subject'}
									<small class="innerError">
										{if $errorType == 'empty'}
											{lang}wcf.global.form.error.empty{/lang}
										{else}
											{lang}wcf.acp.jcoinsVoucher.subject.error.{$errorType}{/lang}
										{/if}
									</small>
								{/if}
							</dd>
						</dl>
						
						<dl{if $errorField == 'content'} class="formError"{/if}>
							<dt><label for="content0">{lang}wcf.acp.jcoinsVoucher.content{/lang}</label></dt>
							<dd>
								<textarea name="content[0]" id="content0" class="wysiwygTextarea">{if !$content[0]|empty}{$content[0]}{/if}</textarea>
								{include file='wysiwyg' wysiwygSelector='content0'}
								{if $errorField == 'content'}
									<small class="innerError">
										{if $errorType == 'empty'}
											{lang}wcf.global.form.error.empty{/lang}
										{else}
											{lang}wcf.acp.jcoinsVoucher.content.error.{@$errorType}{/lang}
										{/if}
									</small>
								{/if}
							</dd>
						</dl>
						
						<dl{if $errorField == 'footer'} class="formError "{/if}>
							<dt><label for="footer0">{lang}wcf.acp.jcoinsVoucher.footer{/lang}</label></dt>
							<dd>
								<textarea name="footer[0]" id="footer0" class="wysiwygTextarea">{if !$footer[0]|empty}{$footer[0]}{/if}</textarea>
								{include file='wysiwyg' wysiwygSelector='footer0'}
								
								{if $errorField == 'footer'}
									<small class="innerError">
										{if $errorType == 'empty'}
											{lang}wcf.global.form.error.empty{/lang}
										{else}
											{lang}wcf.acp.jcoinsVoucher.footer.error.{$errorType}{/lang}
										{/if}
									</small>
								{/if}
							</dd>
						</dl>
					{else}
						<div class="section tabMenuContainer">
							<nav class="tabMenu">
								<ul>
									{foreach from=$availableLanguages item=availableLanguage}
										{assign var='containerID' value='language'|concat:$availableLanguage->languageID}
										<li><a href="{@$__wcf->getAnchor($containerID)}">{$availableLanguage->languageName}</a></li>
									{/foreach}
								</ul>
							</nav>
							
							{foreach from=$availableLanguages item=availableLanguage}
								<div id="language{@$availableLanguage->languageID}" class="tabMenuContent">
									<div class="section">
										{if $__wcf->session->getPermission('admin.content.cms.canUseMedia')}
											<dl{if $errorField == 'image'|concat:$availableLanguage->languageID} class="formError"{/if}>
												<dt><label for="image{@$availableLanguage->languageID}">{lang}wcf.acp.jcoinsVoucher.image{/lang}</label></dt>
												<dd>
													<div id="imageDisplay{@$availableLanguage->languageID}">
														{if $images[$availableLanguage->languageID]|isset && $images[$availableLanguage->languageID]->hasThumbnail('small')}
															{@$images[$availableLanguage->languageID]->getThumbnailTag('small')}
														{/if}
													</div>
													<p class="button jsMediaSelectButton" data-store="imageID{@$availableLanguage->languageID}" data-display="imageDisplay{@$availableLanguage->languageID}">{lang}wcf.media.chooseImage{/lang}</p>
													<small>{lang}wcf.acp.jcoinsVoucher.image.description{/lang}</small>
													<input type="hidden" name="imageID[{@$availableLanguage->languageID}]" id="imageID{@$availableLanguage->languageID}"{if $imageID[$availableLanguage->languageID]|isset} value="{@$imageID[$availableLanguage->languageID]}"{/if}>
													{if $errorField == 'image'|concat:$availableLanguage->languageID}
														<small class="innerError">{lang}wcf.acp.jcoinsVoucher.image.error.{@$errorType}{/lang}</small>
													{/if}
												</dd>
											</dl>
										{elseif $action == 'edit' && $images[$availableLanguage->languageID]|isset && $images[$availableLanguage->languageID]->hasThumbnail('small')}
											<dl>
												<dt>{lang}wcf.acp.jcoinsVoucher.image{/lang}</dt>
												<dd>
													<div id="imageDisplay">{@$images[$availableLanguage->languageID]->getThumbnailTag('small')}</div>
												</dd>
											</dl>
										{/if}
										
										<dl{if $errorField == 'subject'|concat:$availableLanguage->languageID} class="formError"{/if}>
											<dt><label for="subject{@$availableLanguage->languageID}">{lang}wcf.acp.jcoinsVoucher.subject{/lang}</label></dt>
											<dd>
												<textarea name="subject[{@$availableLanguage->languageID}]" id="subject{@$availableLanguage->languageID}" class="wysiwygTextarea">{if !$subject[$availableLanguage->languageID]|empty}{$subject[$availableLanguage->languageID]}{/if}</textarea>
												{include file='wysiwyg' wysiwygSelector='subject'|concat:$availableLanguage->languageID}
												
												{if $errorField == 'subject'|concat:$availableLanguage->languageID}
													<small class="innerError">
														{if $errorType == 'empty'}
															{lang}wcf.global.form.error.empty{/lang}
														{else}
															{lang}wcf.acp.jcoinsVoucher.subject.error.{$errorType}{/lang}
														{/if}
													</small>
												{/if}
											</dd>
										</dl>
										
										<dl{if $errorField == 'content'|concat:$availableLanguage->languageID} class="formError"{/if}>
											<dt><label for="content{@$availableLanguage->languageID}">{lang}wcf.acp.jcoinsVoucher.content{/lang}</label></dt>
											<dd>
												<textarea name="content[{@$availableLanguage->languageID}]" id="content{@$availableLanguage->languageID}" class="wysiwygTextarea">{if !$content[$availableLanguage->languageID]|empty}{$content[$availableLanguage->languageID]}{/if}</textarea>
												{include file='wysiwyg' wysiwygSelector='content'|concat:$availableLanguage->languageID}
												
												{if $errorField == 'content'|concat:$availableLanguage->languageID}
													<small class="innerError">
														{if $errorType == 'empty'}
															{lang}wcf.global.form.error.empty{/lang}
														{else}
															{lang}wcf.acp.jcoinsVoucher.content.error.{@$errorType}{/lang}
														{/if}
													</small>
												{/if}
											</dd>
										</dl>
										
										<dl{if $errorField == 'footer'|concat:$availableLanguage->languageID} class="formError"{/if}>
											<dt><label for="footer{@$availableLanguage->languageID}">{lang}wcf.acp.jcoinsVoucher.footer{/lang}</label></dt>
											<dd>
												<textarea name="footer[{@$availableLanguage->languageID}]" id="footer{@$availableLanguage->languageID}" class="wysiwygTextarea">{if !$footer[$availableLanguage->languageID]|empty}{$footer[$availableLanguage->languageID]}{/if}</textarea>
												{include file='wysiwyg' wysiwygSelector='footer'|concat:$availableLanguage->languageID}
												
												{if $errorField == 'footer'|concat:$availableLanguage->languageID}
													<small class="innerError">
														{if $errorType == 'empty'}
															{lang}wcf.global.form.error.empty{/lang}
														{else}
															{lang}wcf.acp.jcoinsVoucher.footer.error.{$errorType}{/lang}
														{/if}
													</small>
												{/if}
											</dd>
										</dl>
									</div>
								</div>
							{/foreach}
						</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{csrfToken}
	</div>
</form>

<script data-relocate="true">
	$(function() {
		var $typeID = $('#typeID').change(function(event) {
			var value = $(event.currentTarget).val();
			
			$('.birthdaySetting, .membershipSetting, .raffleSetting, .normalSetting, .recurringSetting, .registrationSetting, .redemptionSetting, .codeSetting, .notifySetting').hide();
			
			if (value == 1) { $('.normalSetting, .raffleSetting, .redemptionSetting, .notifySetting').show(); }
			if (value == 2) { $('.recurringSetting, .raffleSetting, .redemptionSetting, .notifySetting').show(); }
			if (value == 3) { $('.birthdaySetting, .notifySetting').show(); }
			if (value == 4) { $('.membershipSetting, .notifySetting').show(); }
			if (value == 5) { $('.registrationSetting, .notifySetting').show(); }
			if (value == 6) { $('.codeSetting, .redemptionSetting').show(); }
			
		});
		$typeID.trigger('change');
		
	});
</script>

{include file='footer'}
