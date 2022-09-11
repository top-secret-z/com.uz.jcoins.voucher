{include file='header' pageTitle='wcf.acp.menu.link.voucherJCoins.log.list'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.voucherJCoins.log.list{/lang}{if $items} <span class="badge badgeInverse">{#$items}</span>{/if}</h1>
	</div>
	
	{hascontent}
		<nav class="contentHeaderNavigation">
			<ul>
				{content}
					{if $objects|count}
						<li><a title="{lang}wcf.acp.jcoinsVoucher.log.clear{/lang}" class="button jsVoucherLogClear"><span class="icon icon16 fa-times"></span> <span>{lang}wcf.acp.jcoinsVoucher.log.clear{/lang}</span></a></li>
					{/if}
					
					{event name='contentHeaderNavigation'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</header>

{if $objects|count}
	<form method="post" action="{link controller='JCoinsVoucherLogList'}{/link}">
		<section class="section">
			<h2 class="sectionTitle">{lang}wcf.global.filter{/lang}</h2>
			
			<div class="row rowColGap formGrid">
				<dl class="col-xs-12 col-md-4">
					<dt></dt>
					<dd>
						<input type="text" id="username" name="username" value="{$username}" placeholder="{lang}wcf.user.username{/lang}" class="long">
					</dd>
				</dl>
				
				<dl class="col-xs-12 col-md-4">
					<dt></dt>
					<dd>
						<input type="text" id="title" name="title" value="{$title}" placeholder="{lang}wcf.acp.jcoinsVoucher.title{/lang}" class="long">
					</dd>
				</dl>
				
				{if $availableActions|count > 1}
					<dl class="col-xs-12 col-md-4">
						<dt></dt>
						<dd>
							<select name="action" id="action">
								<option value="">{lang}wcf.acp.jcoinsVoucher.action{/lang}</option>
								{htmlOptions options=$availableActions selected=$action}
							</select>
						</dd>
					</dl>
				{/if}
			</div>
			
			<div class="formSubmit">
				<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
				{csrfToken}
			</div>
		</section>
	</form>
{/if}

{hascontent}
	<div class="paginationTop">
		{content}
			{assign var='linkParameters' value=''}
			{if $title}{capture append=linkParameters}&title={@$title|rawurlencode}{/capture}{/if}
			{if $username}{capture append=linkParameters}&username={@$username|rawurlencode}{/capture}{/if}
			{if $action}{capture append=linkParameters}&action={@$action|rawurlencode}{/capture}{/if}
			
			{pages print=true assign=pagesLinks controller="JCoinsVoucherLogList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
		{/content}
	</div>
{/hascontent}

{if $objects|count}
	<div class="section tabularBox">
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnLogID{if $sortField == 'logID'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherLogList'}pageNo={@$pageNo}&sortField=logID&sortOrder={if $sortField == 'logID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnText columnTime{if $sortField == 'time'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherLogList'}pageNo={@$pageNo}&sortField=time&sortOrder={if $sortField == 'time' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsVoucher.time{/lang}</a></th>
					<th class="columnText columnUsername{if $sortField == 'username'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherLogList'}pageNo={@$pageNo}&sortField=username&sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsVoucher.user{/lang}</a></th>
					<th class="columnText columnTitle{if $sortField == 'title'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherLogList'}pageNo={@$pageNo}&sortField=title&sortOrder={if $sortField == 'title' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsVoucher.title{/lang}</a></th>
					<th class="columnText columnType{if $sortField == 'typeDes'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherLogList'}pageNo={@$pageNo}&sortField=typeDes&sortOrder={if $sortField == 'typeDes' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsVoucher.type{/lang}</a></th>
					<th class="columnText columnJCoins{if $sortField == 'jCoins'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherLogList'}pageNo={@$pageNo}&sortField=jCoins&sortOrder={if $sortField == 'jCoins' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsVoucher.jcoins{/lang}</a></th>
					<th class="columnText columnAction{if $sortField == 'action'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherLogList'}pageNo={@$pageNo}&sortField=action&sortOrder={if $sortField == 'action' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsVoucher.action{/lang}</a></th>
					<th class="columnText columnDetail{if $sortField == 'detail'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsVoucherLogList'}pageNo={@$pageNo}&sortField=detail&sortOrder={if $sortField == 'detail' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsVoucher.detail{/lang}</a></th>
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=voucher}
					<tr class="jsItemRow">
						<td class="columnID columnLogID">{@$voucher->logID}</td>
						<td class="columnText columnTime">{@$voucher->time|time}</td>
						<td class="columnText columnUsername">{$voucher->username}</td>
						{if $voucher->action == 'deleted'}
							<td class="columnText columnTitle">{$voucher->title}</a></td>
						{else}
							<td class="columnText columnTitle"><a href="{link controller='JCoinsVoucherEdit' id=$voucher->voucherID}{/link}" title="{lang}wcf.global.button.edit{/lang}">{$voucher->title}</a></td>
						{/if}
						<td class="columnText columnType">{lang}wcf.acp.jcoinsVoucher.type.{$voucher->typeDes}{/lang}</td>
						<td class="columnText columnJCoins">{@$voucher->jCoins}</td>
						<td class="columnText columnAction">{lang}wcf.acp.jcoinsVoucher.action.{$voucher->action}{/lang}</td>
						<td class="columnText columnDetail">{lang}{$voucher->detail}{/lang}</td>
						
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
				
				{event name='contentFooterNavigation'}
			</ul>
		</nav>
	</footer>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<script data-relocate="true">
	require(['Language', 'UZ/JCoins/Voucher/Acp/LogClear'], function (Language, UZJCoinsVoucherAcpLogClear) {
		Language.addObject({
			'wcf.acp.jcoinsVoucher.log.clear.confirm': '{jslang}wcf.acp.jcoinsVoucher.log.clear.confirm{/jslang}'
		});
		
		new UZJCoinsVoucherAcpLogClear();
	});
</script>

{include file='footer'}
