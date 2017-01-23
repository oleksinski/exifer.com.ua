
{assign var="photo_genre" value=$photo->getExtraField('genre')}
{assign var="photo_user" value=$photo->getUserObject()}
{assign var="photo_exif" value=$photo->getExtraField('exif')}
{assign var="photo_user_name" value=$photo_user->getExtraField('name')}

<div id="start_photo_block"></div>

{if $photo->getField('name')}
	<h3 class="hcenter pad5px">{$photo->getExtraField('name')}</h3>
{/if}

</div> <!-- close page -->

<div class="photo_preview" style="background-color:rgb({$photo->getField('rgb')});" id="p_cont">
	<div id="preview_placeholder" style="width:{$photo->getCustomField('preview_width')}px; margin:0 auto;">
		{photo_img var=$photo p_format='THUMBNAIL_ORIGINAL'|constant:'ThumbModel' id='preview' rel=$photo->getId()}
	</div>
	<div class="page mrt_small">
		<table id="rgb_cont" width="1%" cellspacing="0" cellpadding="0" border="0" align="center">
		<tbody>
		<tr>
			<td>
				<div class="mrr_big hright" style="width:160px">
					{if $USER->exists()}
						<a id="crop2comment" href="javascript://" title="" class="hidden button btn_cons mrr_small">Комментировать</a>
					{/if}
					<img src="{$S_URL}img/crop.gif" width="16" height="16" id="tool_crop" class="mrr_tiny" style="cursor:pointer" alt="Кадрирование" title="Кадрирование" border="0" />
					<img src="{$S_URL}img/grayscale.gif" width="16" height="16" id="tool_grayscale" class="mrr_tiny" style="cursor:pointer" alt="Оттенки серого" title="Оттенки серого" border="0" />
					<div id="grayscaling"></div>
				</div>
			</td>
			{foreach from=$rgb_colors key=index item=color_rgb name=foreach_rgb_color}
			<td><div style="width:35px; height:16px; margin-left:3px; background-color:rgb({$color_rgb});" rel="rgb_cell" ></div></td>
			{/foreach}
			<td><div class="hright" style="width:150px">{$photo|share}</div></td>
		</tr>
		</tbody>
		</table>
		{literal}
		<script type="text/javascript">
			(function(){
				__$('rgb_cont div[rel=rgb_cell]').bind('click',function(){
					__$('p_cont').css('background-color', $(this).css('background-color'));
				});
				if(window.location.hash==='') {
					js_util.scrollto(__$('start_photo_block'), function(){}, 1);
				}
				initJcrop();
				initGrayscale();
			})();
		</script>
		{/literal}

	</div>
</div>

<div class="page"> <!-- open page -->

{if $photo->getField('description')}
	<h4 class="hcenter pad5px">{$photo->getField('description')|urlify:150}</h4>
	<div class="clear_small"></div>
{else}
	<div class="clear_medium"></div>
{/if}

{if $gallery_collection->length()}
	{capture assign="caption_gallery"}
		{photo_lenta photo_per_row=$gallery_collection->length() column=page collection=$gallery_collection photo_cur_id=$photo->getId() p_format='THUMB_75'|constant:'ThumbModel' td_class=opaque}
	{/capture}
	{boxinfo content=$caption_gallery type=2}
	<div class="clear_big"></div>
{/if}

<div class="fl bigcol">
	{include file="comment/form.tpl" comment=$comment comment_collection=$comment_collection}
</div>

<div class="fr smallcol">

	<a name="v"></a>

	<!--{*
	<div class="likes_google">
		<g:plusone size="medium"></g:plusone>
	</div>
	<div class="likes_facebook">
		<iframe src="http://www.facebook.com/plugins/like.php?{href target='photo' p1=$photo->getId() var=$photo}&amp;locale=en_US&amp;layout=button_count&amp;show_faces=true&amp;width=85&amp;action=like&amp;font&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:85px; height:21px;" allowTransparency="true"></iframe>
	</div>
	<div class="likes_vkontakte">
		<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?24"></script>
		{literal}
		<script type="text/javascript">
			VK.init({apiId: 0, onlyWidgets: true});
		</script>
		{/literal}
		<div id="vk_like"></div>
		{literal}
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", pageTitle: "В Украину возвращается жара"});
		</script>
		{/literal}
	</div>
	<div class="clear_medium"></div>
	*}-->

	{include file="vote/form.tpl" item=$photo}

	{capture assign="caption_info"}
		<div class="small box_in_box box_info">
			<a href="{href target='user' p1=$photo_user->getId() var=$photo_user}" class="" title="{$photo_user_name}">{userpic_img var=$photo_user u_format='FORMAT_75'|constant:'UserpicModel' alt=$photo_user_name align=left class=mrr_small width=50 height=50} {$photo_user_name}</a> {$photo_user|online}
			<div class="mrt_tiny"><a href="{href target='photo_lenta' uid=$photo_user->getId()}" title="Фотографии пользователя {$photo_user_name}">Фотографий:</a><span class="mrl_tiny">{item_count item=photos var=$photo_user type=user}</span></div>
			{if $photo_user->getExtraField('occupation')}
			<div class="mrt_tiny">{$photo_user|occupation:true}</div>
			{/if}
			<div class="clear2"></div>
		</div>

		<div class="clear_medium"></div>

		{if $photo->isEditable() || $photo->isRemovable()}
			<div class="box_in_box box_info">
				{if $photo->isEditable()}
					<a href="{href target='photo_edit' p1=$photo->getId()}" title="">Редактировать фото</a>
					<span class="gray mrl_big mrr_big"> | </span>
				{/if}
				{if $photo->isRemovable()}
					<a href="{href target='photo_remove' p1=$photo->getId()}" title="" class="small">Удалить фото</a>
				{/if}
			</div>

			<div class="clear_medium"></div>
		{/if}

		<div class="box_in_box box_info">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">
			<tbody>
				<colgroup>
					<col width="75" />
				</colgroup>
				<tr>
					<td class="vtop gray">Загружено</td>
					<td class="vtop pdl_small">{$photo->getField('add_tstamp')|datetime}</td>
				</tr>
				<tr>
					<td class="vtop gray pdt_tiny">Жанр</td>
					<td class="vtop pdl_small pdt_tiny">
						<a href="{href target='photo_lenta' genre=$photo_genre.id uid=$photo_user->getId()}" title="{$photo_genre.name}">{$photo_genre.name}</a>
					</td>
				</tr>
				{if $photo_exif}
				<tr>
					<td colspan="2" class="vtop gray pdt_tiny">
						<a href="javascript://" title="" id="exif_link" class="tech_dashed">EXIF</a>
					</td>
				</tr>
				{/if}
			</tbody>
			</table>
		</div>

		<div class="clear_medium"></div>

		<div class="box_in_box box_info">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">
			<tbody>
				<colgroup>
					<col width="75" />
				</colgroup>
				<tr>
					<td class="vtop gray">Просмотров</td>
					<td class="vtop pdl_small">{item_count item=views var=$photo type=photo}</td>
				</tr>
				<tr>
					<td class="vtop gray pdt_tiny">Комментариев</td>
					<td class="vtop pdl_small pdt_tiny">{item_count item=comments var=$photo type=photo}</td>
				</tr>
				<tr>
					<td class="vtop gray pdt_tiny"><a href="javascript://" title="Оценка" id="v-get-ctrl" class="tech_dashed">Оценка</a></td>
					<td class="vtop pdl_small pdt_tiny">{item_count item=votes var=$photo type=photo}<span class="mrl" id="vv_ajaxloader"></span></td>
				</tr>
				<!--{*
				<tr>
					<td width="75" class="vtop gray pdt_tiny">Рейтинг</td>
					<td class="vtop pdl_small pdt_tiny">{item_count item=rate var=$photo type=photo}</td>
				</tr>
				*}-->
			</tbody>
			</table>
		</div>
	{/capture}
	{boxinfo content=$caption_info type=2}

	<div id="vv_context"></div>
	<script type="text/javascript">
		window.js_vote.item_id = {$photo->getId()};
		window.js_vote.item_type = "{$photo->getCustomField('vote')->getItemType()}";
		window.js_vote.url_get = "{href target='vote_get'}";
		window.js_vote.url_add = "{href target='vote_add'}";
		{if $USER->isModerator()}
			window.js_vote.url_del = "{href target='vote_del'}";
		{/if}
		window.js_vote.init();
	</script>

	{if $photo_exif}
		<div id="exif_info">
			<div class="clear_medium"></div>
			{capture assign="capture_exif"}
			<div class="small box_in_box box_info">
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">
				<tbody>
				<colgroup>
					<col width="95" />
				</colgroup>
				{foreach from=$photo_exif key=exif_name item=exif_value name=foreach_exif}
					<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
						<td class="vmid gray pad5px">{$exif_name}</td>
						<td class="vmid pdl_small pad5px">{$exif_value}</td>
					</tr>
				{/foreach}
				</tbody>
				</table>
				{literal}
				<script type="text/javascript">__$('exif_link').bind('click', function(){$(this).blur();__$('exif_info').toggle();return false;}).trigger('click');</script>
				{/literal}
			</div>
		</div>
		{/capture}
		{boxinfo content=$capture_exif type=2}
	{/if}

</div>
