{include file="admin/header.tpl"}

<div class="mrl">

<h3>Управление пользователями</h3>

<div class="clear_big"></div>

<table width="1%" cellpadding="2" cellspacing="2" border="0">
<tbody>
<colgroup>
	<col width="20%" />
	<col width="60%" />
	<col width="20%" />
</colgroup>
<tr>
	<form action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">
		<td class="hright">ID:</td>
		<td class="hleft">
			<input type="text" name="id" class="input_text" value="{$smarty.request.id|default:''|escape:'html'}" />
		</td>
		<td class="hleft">
			<input type="submit" class="button" value="Search" />
		</td>
	</form>
</tr>

<tr>
	<form action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">
		<td class="hright">Email:</td>
		<td class="hleft">
			<input type="text" name="email" class="input_text" value="{$smarty.request.email|default:''|escape:'html'}" />
		</td>
		<td class="hleft">
			<input type="submit" name="" class="button" value="Search" />
		</td>
	</form>
</tr>

<tr>
	<form action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">
		<td class="hright">UrlName:</td>
		<td class="hleft">
			<input type="text" name="urlname" class="input_text" value="{$smarty.request.urlname|default:''|escape:'html'}" />
		</td>
		<td class="hleft">
			<input type="submit" name="" class="button" value="Search" />
		</td>
	</form>
</tr>


</tbody>
</table>

<div class="clear_big"></div>

{if $user->exists()}

<div class="box_in_box box_info pdt_tiny">
	<div class="pdt_small small" style="overflow:visible">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
		<colgroup>
			<col width="20%" />
			<col width="80%" />
		</colgroup>

		{assign var="BITMASK_HIDE_BIRTHDAY" value='BITMASK_HIDE_BIRTHDAY'|constant:'User'}
		{assign var="BITMASK_HIDE_ONLINE" value='BITMASK_HIDE_ONLINE'|constant:'User'}
		{assign var="BITMASK_HIDE_LOCATION" value='BITMASK_HIDE_LOCATION'|constant:'User'}
		{assign var="BITMASK_ADMIN_MODER" value='BITMASK_ADMIN_MODER'|constant:'User'}
		{assign var="BITMASK_ADMIN_ROOT" value='BITMASK_ADMIN_ROOT'|constant:'User'}

		{foreach from=$user->getFields() key=field item=value name=foreach_userinfo}
		<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
			<td class="vmid gray pad5px">{$field}</td>
			<td class="vmid pdl_small pad5px">
				{if $field=='password'}
					<span class="">{$value}</span>
				{elseif $field=='about'}
					{$value|urlify}
				{else}
					{$value}
					{if $value}
						<span class="mrl_small gray">
						{if $field=='id'}
							<a href="{href target='user' p1=$user->getId() var=$user}" title="">{href target='user' p1=$user->getId() var=$user}</a>
						{elseif in_array($field, array('hit_tstamp', 'reg_tstamp', 'login_tstamp', 'update_tstamp', 'upload_tstamp', 'upload_next_tstamp', 'userpic_tstamp', 'view_tstamp'))}
							({$value|datetime})
						{elseif $field=='ban_tstamp' && $value}
							{if $user->isBanned()}
								{assign var="ban_class" value="red"}
							{else}
								{assign var="ban_class" value="green"}
							{/if}
							(<span class="{$ban_class|default:'gray'}">{$value|datetime}</span>)
						{elseif $field=='birthday'}
							({$user|birthday})
						{elseif $field=='country' || $field=='city'}
							({$user|location})
						{elseif $field=='gender'}
							({$user|gender})
						{elseif $field=='bitmask'}
							<div>BITMASK_HIDE_BIRTHDAY = {if $user->isBitmaskSet($BITMASK_HIDE_BIRTHDAY)}1{else}0{/if}</div>
							<div>BITMASK_HIDE_ONLINE = {if $user->isBitmaskSet($BITMASK_HIDE_ONLINE)}1{else}0{/if}</div>
							<div>BITMASK_HIDE_LOCATION = {if $user->isBitmaskSet($BITMASK_HIDE_LOCATION)}1{else}0{/if}</div>
							<div>BITMASK_ADMIN_MODER = {if $user->isBitmaskSet($BITMASK_ADMIN_MODER)}1{else}0{/if}</div>
							<div>BITMASK_ADMIN_ROOT = {if $user->isBitmaskSet($BITMASK_ADMIN_ROOT)}1{else}0{/if}</div>
						{/if}
						</span>
					{/if}
				{/if}
			</td>
		</tr>
		{/foreach}
		</tbody>
		</table>

		<div class="pdt">
			<form action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8" onsubmit="return window.confirm('Realy delete?');">
				<label class="m_mrl_tiny">
					<input type="checkbox" class="vmid" name="delete" value="{$user->getId()}" /> удалить
				</label>
				<span class="gray mrl_tiny mrr_tiny"> &nbsp; </span>
				<label class="m_mrl_tiny">
					<input type="checkbox" class="vmid" name="spamer" value="{$user->getId()}" /> спамер
				</label>
				<input type="hidden" name="id" value="{$user->getId()}" />
				<input type="submit" name="admin_post" value=" GO " class="button small mrl" />
			</form>
		</div>

		<div class="pdt">
			<form action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">
				<select name="ban" id="ban" class="select select_cons">
					<option value="null">--- Ban/Unban ---</option>
					<option value="ban_hour">забанить на час</option>
					<option value="ban_day">забанить на день</option>
					<option value="ban_week">забанить на неделю</option>
					<option value="ban_month">забанить на месяц</option>
					<option value="unban">разбанить</option>
				</select>
				<input type="hidden" name="id" value="{$user->getId()}" />
				<input type="submit" name="admin_post" value=" GO " class="button small mrl" />
			</form>
		</div>

		<div class="pdt">
			<form action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">
				<select name="admin" id="admin" class="select select_cons">
					<option value="user">User</option>
					<option value="moderator" {checked_selected type="option" arg1=true arg2=$user->isBitmaskSet($BITMASK_ADMIN_MODER)}>Moderator</option>
					<option value="admin" {checked_selected type="option" arg1=true arg2=$user->isBitmaskSet($BITMASK_ADMIN_ROOT)}>Admin</option>
				</select>
				<input type="hidden" name="id" value="{$user->getId()}" />
				<input type="submit" name="admin_post" value=" GO " class="button small mrl" />
			</form>
		</div>

		<div class="pdt">
			<form action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">
				<input type="hidden" name="id" value="{$user->getId()}" />
				<input type="submit" name="recalc" value=" Recalc info " class="button small" />
			</form>
		</div>

	</div>
	<div class="clear"></div>
</div>
{elseif isset($smarty.request.id) || isset($smarty.request.email) || isset($smarty.request.urlname)}
	{boxinfo content='Пользователь не найден' type=3}
{/if}

</div>

{include file="admin/footer.tpl"}