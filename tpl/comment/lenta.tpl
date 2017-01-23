<div class="fl bigcol">

	<div class="mrb_tiny">
	{capture assign="capture_lenta"}
		<div class="fl gray">Комментарии к фотографиям</div>
		<div class="fr"><span class="gray">&#8721;</span> {$total_cnt|default:0}</div>
		<div class="clear"></div>
	{/capture}
	{boxinfo content=$capture_lenta type=2}
	</div>

	<div class="clear"></div>

	{if $user->exists()}
	{assign var="user_name" value=$user->getExtraField('name')}
	<div class="mrb_tiny">
		{capture assign="capture_user_filter"}
			<span class="small gray">Автор:</span>
			<span class="mrl_small">
				<a href="{href target='user' p1=$user->getId() var=$user}" class="" title="{$user_name}">{$user_name}</a> {$user|online}
				<a href="{href target='rss_comment' uid=$user->getId() var=$user}" class="mrl_tiny" title="Комментарии от {$user_name}">{rss_icon}</a>
				<span class="gray mrl_tiny mrr_tiny"> | </span>
				<a href="{href target='comment_lenta' query=$request_query uid=null}" title="">все пользователи</a>
			</span>
		{/capture}
		{boxinfo content=$capture_user_filter type=3 scheme=green}
	</div>
	{/if}

	{if isset($request_query.genre) && isset($genre_data)}
	<div class="mrb_tiny mrr_tiny fl">
		{capture assign="capture_genre_filter"}
			<span class="small gray">Жанр:</span>
			<span class="mrl_small">
				<b>{$genre_data.name}</b>
				<span class="gray mrl_tiny mrr_tiny"> | </span>
				<a href="{href target='comment_lenta' query=$request_query genre=null}" title="">все жанры</a>
			</span>
		{/capture}
		{boxinfo content=$capture_genre_filter type=3 scheme=magenta class=bigcol_half2 width=null}
	</div>
	{/if}

	{if isset($request_query.date)}
	<div class="mrb_tiny fl">
		{capture assign="capture_time_filter"}
			<span class="small gray">За период:</span>
			<span class="mrl_small">
				<b>{$filter_date_from|datetime:'d.m.Y'} &ndash; {$filter_date_to|datetime:'d.m.Y'}</b>
				<span class="gray mrl_tiny mrr_tiny"> | </span>
				<a href="{href target='comment_lenta' query=$request_query date=null}" title="">все время</a>
			</span>
		{/capture}
		{boxinfo content=$capture_time_filter type=3 scheme=yellow class=bigcol_half2 width=null}
	</div>
	{/if}

	<div class="clear2"></div>

	{if isset($request_query.q) && $request_query.q}
		<div class="clear2"></div>
		<div class="mrb_tiny">
			{capture assign="capture_user_filter"}
				<span class="small gray">Поиск:</span>
				<span class="mrl_small">
					<form name="q_form" id="q_form" method="get" action="{href target='comment_lenta'}" accept-charset="utf-8">
						<input type="text" name="q" value="{$request_query.q|escape:'html'}" style="width:78%" />
						{foreach from=$request_query key=pname item=pvalue}
							{if $pname!='q' && $pname!='p'}
								<input type="hidden" name="{$pname}" value="{$pvalue|escape:'html'}" />
							{/if}
						{/foreach}
						<input type="submit" class="button mrl_tiny" value="Поиск" />
					</form>
					<span class="gray mrl_tiny mrr_tiny"> | </span>
					<a href="{href target='photo_lenta' query=$request_query q=null}" title="">x</a>
				</span>
			{/capture}
			{boxinfo content=$capture_user_filter type=3 scheme=yellow}
		</div>
	{/if}

</div>

{include file="samples/genre_calendar.tpl" user=$user genre_collection=$genre_collection page_target=comment_lenta}

<div class="clear"></div>

{if $comment_collection->length()}

	{pager var=$pager}

	<div class="clear_medium"></div>

	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">
	<tbody>
	<colgroup>
		<col width="15%" />
	</colgroup>
	{foreach from=$comment_collection key=comment_id item=comment name=comment_lenta}
		{assign var="comment_item" value=$comment->getItemObject()}
		{assign var="comment_user" value=$comment->getUserObject()}
		{assign var="comment_item_name" value=$comment_item->getExtraField('name')}
		{assign var="comment_user_name" value=$comment_user->getExtraField('name')}
		<tr>
			<td class="vtop pdr hcenter">
				<a href="{href target='photo' p1=$comment_item->getId() var=$comment_item}" title="{$comment_item_name}">{photo_img var=$comment_item p_format='THUMB_150'|constant:'ThumbModel' class=shadow}</a>
			</td>
			<td class="vtop hleft">
				<div class="fl" style="width:700px">
					<a title="{$comment_user_name}" class="" href="{href target='user' p1=$comment_user->getId() var=$comment_user}">{$comment_user_name}</a> {$comment_user|online}
					<span class="gray mrl_tiny">{$comment->getField('add_tstamp')|datetime}</span>
					<div class="clear_medium"></div>
					<a href="{$comment->getExtraField('url')}" class="tech_dark" title="">
						{if isset($request_query.q) && $request_query.q}
							{$comment->getExtraField('text')|highlight:$request_query.q}
						{else}
							{$comment->getExtraField('text')}
						{/if}
					</a>
				</div>

				<div class="fr hright" style="width:100px">
					<a href="{$comment->getExtraField('url')}" class="tech mrl" title="" rel="c_anchor">#{$comment->getExtraField('anchor')}</a>
					<div class="clear_medium"></div>
					<a title="{$comment_user_name}" href="{href target='user' p1=$comment_user->getId() var=$comment_user}">{userpic_img var=$comment_user u_format='FORMAT_75'|constant:'UserpicModel' alt=$comment_user_name width=50 height=50}</a>
				</div>

				{assign var="isRemovable" value=$comment->isRemovable()}
				{assign var="isClearable" value=$comment->isClearable()}
				{if $isClearable || $isRemovable}
				<div class="clear_medium"></div>
				<div class="fr">
					{capture assign="comment_moderator"}
						{if $isRemovable}
							<a href="{href target=comment_del p1=$comment_id p2=$comment->getItemType()}" class="mrl_tiny mrr_tiny">Delete</a>
						{/if}
						{if $isClearable}
							<a href="{href target=comment_clr p1=$comment_id p2=$comment->getItemType()}" class="mrl_tiny mrr_tiny">Clear</a>
						{/if}
					{/capture}
					{boxinfo content=$comment_moderator type=3 scheme=yellow}
				</div>
				<div class="clear"></div>
				{/if}
			</td>
		</tr>

		{if $smarty.foreach.comment_lenta.iteration!=$comment_collection->length()}
			<tr style="height:25px"><td class="vmid" width="100%" colspan="2"><hr class="casper"/></td></tr>
		{/if}

	{/foreach}

	</tbody>
	</table>

	<div class="clear_small"></div>

	{pager var=$pager}

{else}
	<div class="mrt_small">Нет комментариев</div>
{/if}