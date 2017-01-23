{if $__user_collection_length__}

	{math assign="user_td_width" format="%u%%" equation="x/y" x=100 y=$__user_per_row__}

	<table width="100%" cellspacing="0" cellpadding="5" border="0">
	<tbody>
	{foreach from=$__user_collection__ key=user_id item=user name=foreach_user_lenta}

		{assign var="__iteration__" value=$smarty.foreach.foreach_user_lenta.iteration%$__user_per_row__}
		{assign var="user_name" value=$user->getExtraField('name')}
		{if $__iteration__==1 || $__user_per_row__==1}
		<tr>
		{/if}

		<td width="{$user_td_width}" class="vmid hleft {if $smarty.foreach.foreach_user_lenta.iteration>$__user_per_row__}pdt_small{/if}">
			<a href="{href target='user' p1=$user_id var=$user}" title="{$user_name}">
				{userpic_img var=$user u_format='FORMAT_75'|constant:'UserpicModel' alt=$user_name align=left class=mrr_small width=$__userpic_width__ height=$__userpic_height__}
				{if $__highlight__}
					{$user_name|highlight:$__highlight__}
				{else}
					{$user_name}
				{/if}
			</a> {$user|online}
			{if $user->getExtraField('occupation')}
				<div class="gray mrt_tiny">{$user|occupation:true}</div>
			{/if}
			{if $USER->getId()==$user_id || !$user->isBitmaskSet(User::BITMASK_HIDE_LOCATION) || $USER->isModerator()}
			<div class="gray mrt_tiny">Страна/город: {$user|location}</div>
			{/if}
		</td>

		{if ($__iteration__==0 && !$smarty.foreach.foreach_user_lenta.first) || $__user_per_row__==1}
			</tr>
			{if !$smarty.foreach.foreach_user_lenta.last && $__hr_separate__}
			<tr><td height="{$__hr_separate__}" class="vmid" width="100%" colspan="{$__user_per_row__}"><hr class="casper"/></td></tr>
			{/if}
		{elseif $smarty.foreach.foreach_user_lenta.last}
			<td colspan="{$__user_per_row__-$__iteration__}">&nbsp;</td>
		</tr>
		{/if}

	{/foreach}
	</tbody>
	</table>
{/if}
