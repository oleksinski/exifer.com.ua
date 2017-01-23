<div class="page_half fl">

	<h2 class="hleft">RSS: фотографии</h2>

	<div class="clear_big"></div>

	<div class="pdl_tiny">
		{rss_icon}
		<a href="{href target='rss_photo'}" class="mrl_tiny" title="Общая лента фотографий">Общая лента фотографий</a>
	</div>
	<div class="clear_big"></div>

	<table cellpadding="4" cellspacing="2" width="100%" border="0">
		{foreach from=$genres key=g_id item=g_data name=foreach_genre}

			{assign var="__iteration__" value=$smarty.foreach.foreach_genre.iteration%2}

			{if $__iteration__==1}
			<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
			{/if}

			<td width="50%" class="vbot hleft">
				{rss_icon}
				<a href="{href target='rss_photo' genre=$g_id}" class="mrl_tiny" title="{$g_data.name}">{$g_data.name}</a>
			</td>

			{if $__iteration__==0 && !$smarty.foreach.foreach_genre.first}
			</tr>
			{elseif $smarty.foreach.foreach_genre.last}
				<td colspan="{math equation="x-y" x=2 y=$__iteration__}">&nbsp;</td>
			</tr>
			{/if}

		{/foreach}

		{cycle print=false}
	</table>
</div>

<div class="page_half fr">

	<h2 class="hleft">RSS: пользователи</h2>

	<div class="clear_big"></div>

	<div class="pdl_tiny">
		{rss_icon}
		<a href="{href target='rss_user'}" class="mrl_tiny" title="Новые пользователи">Новые пользователи</a>
	</div>

	<div class="clear_big"></div>

	<table cellpadding="4" cellspacing="2" width="100%" border="0">
		{foreach from=$occupation key=o_id item=o_data name=foreach_occupation}

			{assign var="__iteration__" value=$smarty.foreach.foreach_occupation.iteration%2}

			{if $__iteration__==1}
			<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
			{/if}

			<td width="50%" class="vbot hleft">
				{rss_icon}
				<a href="{href target='rss_user' occupation=$o_id}" class="mrl_tiny" title="{$o_data.name}">{$o_data.name}</a>
			</td>

			{if $__iteration__==0 && !$smarty.foreach.foreach_occupation.first}
			</tr>
			{elseif $smarty.foreach.foreach_occupation.last}
				<td colspan="{math equation="x-y" x=2 y=$__iteration__}">&nbsp;</td>
			</tr>
			{/if}

		{/foreach}

	</table>

	<div class="clear_big"></div>

	<h2 class="hleft">RSS: комментарии</h2>

	<div class="clear_big"></div>

	<div class="pdl_tiny">
		{rss_icon}
		<a href="{href target='rss_comment'}" class="mrl_tiny" title="Новые комментарии">Новые комментарии</a>
	</div>

</div>