<div class="fl bigcol">

	<div class="mrb_tiny">

	{capture assign="capture_sort"}

		<div class="fl">

			<span class="small gray">Сортировать по:</span>

			{capture assign="capture_orderby_date"}дате{/capture}
			<!--{*capture assign="capture_orderby_rating"}рейтингу{/capture*}-->
			{capture assign="capture_orderby_views"}просмотрам{/capture}
			{capture assign="capture_orderby_comments"}комментариям{/capture}
			{capture assign="capture_orderby_votes"}оценкам{/capture}

			<span class="mrl_small">

				{if $request_query.orderby=='id'}
					<b>{$capture_orderby_date}</b>
				{else}
					<a href="{href target='photo_lenta' query=$request_query orderby='id'}" title="">{$capture_orderby_date}</a>
				{/if}

				<!--{*
				<span class="gray mrl_tiny mrr_tiny"> | </span>

				{if $request_query.orderby=='rating'}
					<b>{$capture_orderby_rating}</b>
				{else}
					<a href="{href target='photo_lenta' query=$request_query orderby='rating'}" title="">{$capture_orderby_rating}</a>
				{/if}
				*}-->

				<span class="gray mrl_tiny mrr_tiny"> | </span>

				{if $request_query.orderby=='views'}
					<b>{$capture_orderby_views}</b>
				{else}
					<a href="{href target='photo_lenta' query=$request_query orderby='views'}" title="">{$capture_orderby_views}</a>
				{/if}

				<span class="gray mrl_tiny mrr_tiny"> | </span>

				{if $request_query.orderby=='comments'}
					<b>{$capture_orderby_comments}</b>
				{else}
					<a href="{href target='photo_lenta' query=$request_query orderby='comments'}" title="">{$capture_orderby_comments}</a>
				{/if}

				<span class="gray mrl_tiny mrr_tiny"> | </span>

				{if $request_query.orderby=='votes'}
					<b>{$capture_orderby_votes}</b>
				{else}
					<a href="{href target='photo_lenta' query=$request_query orderby='votes'}" title="">{$capture_orderby_votes}</a>
				{/if}
			</span>

			<!--{*
			<div class="clear_small"></div>

			<span class="small gray" style="visibility:hidden">Сортировать по:</span>

			<span class="mrl_small">
				<a href="" title="">день</a>
				<span class="gray mrl_tiny mrr_tiny"> | </span>
				<a href="" title="">3 дня</a>
				<span class="gray mrl_tiny mrr_tiny"> | </span>
				<a href="" title="">неделя</a>
				<span class="gray mrl_tiny mrr_tiny"> | </span>
				<a href="" title="">месяц</a>
				<span class="gray mrl_tiny mrr_tiny"> | </span>
				<a href="" title="">все время</a>
			</span>
			*}-->
		</div>

		<div class="fr">

			<span class="mrr_big">
				<span class="gray">&#8721;</span> {$total_cnt|default:0}
			</span>

			<span class="small gray">Вид:</span>

			{capture assign="v_asis_caption"}как есть{/capture}
			{capture assign="v_square_caption"}квадратные{/capture}

			{capture assign="v_full_ico"}
				<img src="{$S_URL}img/icon/%s" width="17" height="17" class="vmid" border="0" alt="{$v_asis_caption}" />
			{/capture}

			{capture assign="v_brief_ico"}
				<img src="{$S_URL}img/icon/%s" width="17" height="17" class="vmid" border="0" alt="{$v_square_caption}" />
			{/capture}

			<span class="mrl_small">
				{if $request_query.viewmode=='square'}
					<b alt="{$v_asis_caption}" title="{$v_square_caption}">{$v_brief_ico|sprintf:'v_thumb_selected.gif'}</b>
				{else}
					<a href="{href target=photo_lenta query=$request_query viewmode=square}" title="{$v_square_caption}">{$v_brief_ico|sprintf:'v_thumb_inactive.gif'}</a>
				{/if}

				<span class="gray mrl_tiny mrr_tiny"> | </span>

				{if $request_query.viewmode=='asis'}
					<b alt="{$v_asis_caption}" title="{$v_asis_caption}">{$v_full_ico|sprintf:'v_full_selected.gif'}</b>
				{else}
					<a href="{href target=photo_lenta query=$request_query viewmode=asis}" title="{$v_asis_caption}">{$v_full_ico|sprintf:'v_full_inactive.gif'}</a>
				{/if}
			</span>

		</div>

		<div class="clear2"></div>
	{/capture}
	{boxinfo content=$capture_sort type=2}
	</div>

	{if $user->exists()}
		{assign var="user_name" value=$user->getExtraField('name')}
		<div class="mrb_tiny">
			{capture assign="capture_user_filter"}
				<span class="small gray">Автор:</span>
				<span class="mrl_small">
					<a href="{href target='user' p1=$user->getId() var=$user}" title="{$user_name}">{$user_name}</a> {$user|online} <a href="{href target='rss_photo' uid=$user->getId() var=$user}" class="mrl_tiny" title="Фотографии {$user_name}">{rss_icon}</a>
					<span class="gray mrl_tiny mrr_tiny"> | </span>
					<a href="{href target='photo_lenta' query=$request_query uid=null}" title="">все пользователи</a>
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
				<b>{$genre_data.name}</b> <a href="{href target='rss_photo' genre=$genre_data.id}" class="mrl_tiny" title="{$genre_data.name}">{rss_icon}</a>
				<span class="gray mrl_tiny mrr_tiny"> | </span>
				<a href="{href target='photo_lenta' query=$request_query genre=null}" title="">все жанры</a>
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
				<a href="{href target='photo_lenta' query=$request_query date=null}" title="">все время</a>
			</span>
		{/capture}
		{boxinfo content=$capture_time_filter type=3 scheme=yellow class=bigcol_half2 width=null}
	</div>
	{/if}

	{if isset($request_query.q) && $request_query.q}
		<div class="clear2"></div>
		<div class="mrb_tiny">
			{capture assign="capture_user_filter"}
				<span class="small gray">Поиск:</span>
				<span class="mrl_small">
					<form name="q_form" id="q_form" method="get" action="{href target='photo_lenta'}" accept-charset="utf-8">
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

	<div class="clear"></div>

</div>

{include file="samples/genre_calendar.tpl" user=$user genre_collection=$genre_collection page_target=photo_lenta}

<div class="clear"></div>

{if $photo_collection->length()}

	{pager var=$pager}

	<div class="clear_medium"></div>

	{if $request_query.viewmode=='square'}
		{assign var="photo_per_row" value=4}
		{assign var="p_format" value='THUMB_240'|constant:'ThumbModel'}
	{else}
		{assign var="photo_per_row" value=3}
		{assign var="p_format" value='THUMB_301'|constant:'ThumbModel'}
	{/if}
	{photo_lenta photo_per_row=$photo_per_row column=page collection=$photo_collection p_format=$p_format p_info=true td_class=vtop highlight=$request_query.q|default:null}

	<div class="clear_small"></div>

	{pager var=$pager}

{else}
	<div class="mrt_small">Нет фотографий</div>
{/if}

