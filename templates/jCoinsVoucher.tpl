{if $category}
	{capture assign='pageTitle'}{lang}wcf.jcoins.voucher.page.categorized{/lang}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}
	{capture assign='contentTitle'}{lang}wcf.jcoins.voucher.page.categorized{/lang}{/capture}
	{capture assign='contentDescription'}{$category->getDescription()}{/capture}
{elseif $voucher}
	{capture assign='pageTitle'}{lang}wcf.jcoins.voucher.page.voucher{/lang}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}
	{capture assign='contentTitle'}{lang}wcf.jcoins.voucher.page.voucher{/lang}{/capture}
{else}
	{capture assign='pageTitle'}{lang}wcf.jcoins.voucher.page{/lang}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}
	{capture assign='contentTitle'}{lang}wcf.jcoins.voucher.page{/lang}{/capture}
{/if}

{capture assign='headContent'}
	{if $pageNo < $pages}
		<link rel="next" href="{link controller='JCoinsVoucher'}pageNo={@$pageNo+1}{/link}">
	{/if}
	{if $pageNo > 1}
		<link rel="prev" href="{link controller='JCoinsVoucher'}{if $pageNo > 2}pageNo={@$pageNo-1}{/if}{/link}">
	{/if}
{/capture}

{capture assign='sidebarRight'}
	<section class="box">
		<form method="post" action="{link controller='JCoinsVoucher'}{/link}">
			<h2 class="boxTitle">{lang}wcf.jcoins.voucher.categories{/lang}</h2>
			
			<div class="boxContent">
				<ol class="boxMenu">
					{foreach from=$categoryList item=categoryItem}
						<li{if $category && $category->categoryID == $categoryItem->categoryID} class="active"{/if} data-category-id="{@$categoryItem->categoryID}">
							<a href="{link controller='JCoinsVoucher'}categoryID={@$categoryItem->categoryID}&sortField={@$sortField}&sortOrder={@$sortOrder}{/link}" class="boxMenuLink">
								<span class="boxMenuLinkTitle">{$categoryItem->getTitle()}</span>
								<span class="badge">{#$categoryItem->getItems()}</span>
							</a>
							
							{if $category && ($category->categoryID == $categoryItem->categoryID || $category->isParentCategory($categoryItem->getDecoratedObject())) && $categoryItem->hasChildren()}
								<ol class="boxMenuDepth1">
									{foreach from=$categoryItem item=subCategoryItem}
										<li{if $category->categoryID == $subCategoryItem->categoryID} class="active"{/if} data-category-id="{@$subCategoryItem->categoryID}">
											<a href="{link controller='JCoinsVoucher'}categoryID={@$subCategoryItem->categoryID}&sortField={@$sortField}&sortOrder={@$sortOrder}{/link}" class="boxMenuLink">
												<span class="boxMenuLinkTitle">{$subCategoryItem->getTitle()}</span>
												<span class="badge">{#$subCategoryItem->getItems()}</span>
											</a>
											
											{if $category && ($category->categoryID == $subCategoryItem->categoryID || $category->parentCategoryID == $subCategoryItem->categoryID) && $subCategoryItem->hasChildren()}
												<ol class="boxMenuDepth2">
													{foreach from=$subCategoryItem item=subSubCategoryItem}
														<li{if $category && $category->categoryID == $subSubCategoryItem->categoryID} class="active"{/if} data-category-id="{@$subSubCategoryItem->categoryID}">
															<a href="{link controller='JCoinsVoucher'}categoryID={@$subSubCategoryItem->categoryID}&sortField={@$sortField}&sortOrder={@$sortOrder}{/link}" class="boxMenuLink">
																<span class="boxMenuLinkTitle">{$subSubCategoryItem->getTitle()}</span>
																<span class="badge">{#$subSubCategoryItem->getItems()}</span>
															</a>
														</li>
													{/foreach}
												</ol>
											{/if}
										</li>
									{/foreach}
								</ol>
							{/if}
						</li>
					{/foreach}
				</ol>
			</div>
			
			{if $items > 1}
				<h2 class="boxTitle">{lang}wcf.jcoins.voucher.displayOptions{/lang}</h2>
				
				<div class="boxContent">
					<dl>
						<dt><label for="sortField">{lang}wcf.jcoins.voucher.sortBy{/lang}</label></dt>
						<dd>
							<select id="sortField" name="sortField">
								<option value="changeTime"{if $sortField == 'changeTime'} selected{/if}>{lang}wcf.jcoins.voucher.sort.changeTime{/lang}</option>
								<option value="jCoins"{if $sortField == 'jCoins'} selected{/if}>{lang}wcf.jcoins.voucher.sort.jCoins{/lang}</option>
								<option value="redeemed"{if $sortField == 'redeemed'} selected{/if}>{lang}wcf.jcoins.voucher.sort.redeemed{/lang}</option>
								
								{event name='sortFields'}
							</select>
							<select name="sortOrder">
								<option value="ASC"{if $sortOrder == 'ASC'} selected{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
								<option value="DESC"{if $sortOrder == 'DESC'} selected{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
							</select>
						</dd>
					</dl>
				</div>
				
				<div class="formSubmit">
					<input type="hidden" name="categoryID" value="{@$categoryID}">
					<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
				</div>
			{/if}
		</form>
	</section>
{/capture}

{assign var='linkParameters' value=''}
{if $category}{capture append='linkParameters'}&categoryID={@$category->categoryID}{/capture}{/if}

{if WCF_VERSION|substr:0:3 >= '5.5'}
	{capture assign='contentInteractionPagination'}
		{pages print=true assign=pagesLinks controller='JCoinsVoucher' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
	{/capture}
	
	{include file='header'}
{else}
	{include file='header'}
	
	{hascontent}
		<div class="paginationTop">
			{content}
				{pages print=true assign=pagesLinks controller='JCoinsVoucher' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
			{/content}
		</div>
	{/hascontent}
{/if}

{if $items}
	<div class="section">
		<ol class="jcoinsVoucherList">
			{include file='jCoinsVoucherItems'}
		</ol>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<footer class="contentFooter">
	{hascontent}
		<div class="paginationBottom">
			{content}
				{@$pagesLinks}
			{/content}
		</div>
	{/hascontent}
	
	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</footer>

{include file="__jCoinsBranding"}

<script data-relocate="true">
	require(['Language', 'UZ/JCoins/Voucher/Redeem'], function(Language, UZJCoinsVoucherRedeem) {
		Language.addObject({
			'wcf.jcoins.voucher.item.redeem': '{jslang}wcf.jcoins.voucher.item.redeem{/jslang}',
			'wcf.jcoins.voucher.success': '{jslang}wcf.jcoins.voucher.success{/jslang}'
		});
		new UZJCoinsVoucherRedeem();
	});
</script>

<script data-relocate="true">
	require(['Language', 'UZ/JCoins/Voucher/RedeemCode'], function(Language, UZJCoinsVoucherRedeemCode) {
		Language.addObject({
			'wcf.jcoins.voucher.item.redeem': '{jslang}wcf.jcoins.voucher.item.redeem{/jslang}',
			'wcf.jcoins.voucher.success': '{jslang}wcf.jcoins.voucher.success{/jslang}',
			'wcf.jcoins.voucher.codeError.codeRedeemLimit': '{jslang}wcf.jcoins.voucher.codeError.codeRedeemLimit{/jslang}',
			'wcf.jcoins.voucher.codeError.expired': '{jslang}wcf.jcoins.voucher.codeError.expired{/jslang}',
			'wcf.jcoins.voucher.codeError.invalid': '{jslang}wcf.jcoins.voucher.codeError.invalid{/jslang}',
			'wcf.jcoins.voucher.codeError.userRedeemLimit': '{jslang}wcf.jcoins.voucher.codeError.userRedeemLimit{/jslang}'
		});
		new UZJCoinsVoucherRedeemCode();
	});
</script>

{include file='footer'}
