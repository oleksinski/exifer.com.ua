<h3 class="fl">
	<a href="{href target='photo_lenta'}" title="Новые фотографии">Новые фотографии</a>
	<a href="{href target='rss_photo'}" class="mrl_tiny" title="Новые фотографии">{rss_icon}</a>
</h3>

<div class="fr">
	{include file="samples/genre.tpl" genre_collection=$genreUpdates page_target=photo_lenta filter_date=$smarty.now|date_format:"%d-%m-%Y"}
</div>

<div class="clear_medium"></div>

{photo_lenta photo_per_row=6 column=bigcol collection=$photo_collection_new_1 p_format='THUMB_150'|constant:'ThumbModel'}
{*
<div class="clear_medium"></div>

{photo_lenta photo_per_row=6 column=bigcol collection=$photo_collection_new_2 p_format='THUMB_150'|constant:'ThumbModel'}
*}
<div class="clear_big"></div>

<div class="bigcol fl">
{*
	{photo_lenta photo_per_row=6 column=bigcol collection=$photo_collection_new_2 p_format='THUMB_150'|constant:'ThumbModel'}

	<div class="clear_big"></div>
*}
	{if $comment_collection->length()}
	<div class="fl bigcol_half mrr">
		<h3>
			<a href="{href target='comment_lenta'}" title="Последние комментарии">Последние комментарии</a>
			<a href="{href target='rss_comment'}" class="mrl_tiny" title="Последние комментарии">{rss_icon}</a>
		</h3>
		<div class="clear_big"></div>
		{foreach from=$comment_collection key=c_id item=comment name=foreach_comment}
			{assign var="item" value=$comment->getItemObject()}
			{assign var="user" value=$comment->getUserObject()}
			{assign var="user_name" value=$user->getExtraField('name')}
				{if !$smarty.foreach.foreach_comment.first}
				<div class="clear_medium"></div>
				{/if}
				<a href="{$comment->getExtraField('url')}" title="">{photo_img var=$item p_format='THUMB_75'|constant:'ThumbModel' align=right class=mrl_small width=35 height=35 class=shadow}</a>
				<a href="{href target='user' p1=$user->getId() var=$user}" title="{$user_name}" class="tech">{userpic_img var=$user u_format='FORMAT_75'|constant:'UserpicModel' alt=$user_name class=mrr_small align=left width=30 height=30} {$user_name}</a>:
				<a href="{$comment->getExtraField('url')}" class="mrl_small tech_dark" title="">{$comment->getExtraField('text')|truncate:200}</a>
				<div class="clear_medium"{if $smarty.foreach.foreach_comment.iteration!=$comment_collection->length()} style="border-bottom:1px dashed #ccc"{/if}></div>
		{/foreach}
	</div>
	{/if}

	<div class="fl bigcol_half">
	{if $user_collection_new->length()}
		<div class="mrl">
			<h3 class="hnill">
				<a href="{href target='user_lenta'}" title="Новые пользователи">Новые пользователи</a>
				<a href="{href target='rss_user'}" class="mrl_tiny" title="Новые пользователи">{rss_icon}</a>
			</h3>
			<div class="clear_medium"></div>
			<div class="small">
			{user_lenta collection=$user_collection_new user_per_row=1 userpic_width=50 userpic_height=50 hr_separate=2}
			</div>
		</div>
		<div class="clear_medium"></div>
	{/if}
	</div>

	<div class="clear2"></div>

</div>

<div class="fr smallcol">
{*
	<div class="clear_medium"></div><div class="clear_medium"></div>

	{capture assign="capture_reformal"}
	<div>
		<span class="xlarge hcenter">Оставьте ваш отзыв!</span>
		<div class="clear_small"></div>
		Расскажите нам все, что вы думаете о сайте.<br />
		Что нравится и не нравится, что хотелось бы улучшить!<br />
		<div class="clear_small"></div>
		<a href="{href target='reformal'}" class="large" title="">Вас внимательно выслушают!</a>
	</div>
	{/capture}
	{boxinfo content=$capture_reformal type=2 scheme=yellow}
*}
	{if $photo_hit->exists()}
{*
		<div class="clear_medium"></div><div class="clear_medium"></div>
*}
		{assign var="photohit_name" value=$photo_hit->getExtraField('name')}
		{assign var="photohit_genre" value=$photo_hit->getExtraField('genre')}
		{assign var="photohit_user" value=$photo_hit->getUserObject()}
		{assign var="photohit_user_name" value=$photohit_user->getExtraField('name')}

		{capture assign="capture_photo_hit"}

			<h3 class="hnill hleft">Фотохит дня</h3>
			<div class="clear_small"></div>

			<div class="hcenter">
				<a href="{href target='photo' p1=$photo_hit->getId() var=$photo_hit}" title="{$photohit_name}">{photo_img var=$photo_hit p_format='THUMB_301'|constant:'ThumbModel' alt=$photohit_name class=shadow}</a>
			</div>

			<div class="clear_medium"></div>

			{if $photohit_name}
				<div class="pdt_tiny pdb_tiny">{$photohit_name}</div>
			{/if}

			<div class="fl small gray mrr_tiny">Автор:</div>
			<div class="fl small"><a href="{href target='user' p1=$photohit_user->getId() var=$photohit_user}" title="{$photohit_user_name}">{$photohit_user_name}</a> {$photohit_user|online}</div>
			<div class="clear"></div>

			<div class="fl small gray mrr_tiny">Жанр:</div>
			<div class="fl small"><a href="{href target='photo_lenta' genre=$photohit_genre.id}">{$photohit_genre.name}</a></div>
			<div class="clear"></div>

		{/capture}
		{boxinfo content=$capture_photo_hit type=3 scheme=yellow}

	{/if}

	<div class="clear"></div>

</div>

{if $photo_collection_top->length()}
	<div class="clear"></div>
	<h3><a href="{href target='photo_lenta' orderby='views'}" title="Популярные фотографии">Популярные фотографии</a></h3>
	<div class="clear_medium"></div>
	{photo_lenta photo_per_row=6 column=page collection=$photo_collection_top p_format='THUMB_150'|constant:'ThumbModel'}
	<div class="clear_medium"></div>
{/if}

{if $user_collection_online->length()}
	<div class="clear_medium"></div>
	{capture assign="capture_online"}
	<div class="box_in_box box_info pdt_tiny">
		<h3 class="hnill"><a href="{href target='user_lenta' online='ONLINE_ON'|constant:'User'}" title="Сейчас на сайте">Сейчас на сайте</a></h3>
		<div class="clear_medium"></div>
		<div class="small">
			{foreach from=$user_collection_online key=user_id item=user_online name=foreach_user_online}
				{assign var="user" value=$user_online->getUserObject()}
				{assign var="user_name" value=$user->getExtraField('name')}
				<span class="mrr">
					<a href="{href target='user' p1=$user->getId() var=$user}" title="{$user_name}">{$user_name}</a> {$user|online}
				</span>
			{/foreach}
		</div>
	</div>
	{/capture}
	{boxinfo content=$capture_online scheme=yellow type=3}
{/if}

<div class="clear_big"></div>
