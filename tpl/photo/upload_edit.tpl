<form name="form_upload_edit" action="{$smarty.server.PHP_SELF}" method="post" enctype="multipart/form-data" accept-charset="utf-8">

{assign var="IS_UPLOAD" value=!$photo->getId()}
{assign var="IS_EDIT" value=!$IS_UPLOAD}

{if $IS_UPLOAD && ($UPLOAD_EXCEEDED || $UPLOAD_INACTIVE)}
	{assign var="disabled_form_field" value='disabled="true"'}
{else}
	{assign var="disabled_form_field" value=null}
{/if}

{assign var="user" value=$photo->getUserObject()}

<div class="fl" style="width:250px">

	<div class="clear_big"></div>
	<div class="clear_big"></div>
	<div class="clear_big"></div>

	{if $IS_UPLOAD}

		{if !$UPLOAD_INACTIVE}
			{capture assign="upload_stat"}
				<div class="box_in_box box_info ">
					<ul class="dot small">
						<li>Кол-во доступных загрузок: <b>{$user->getField('upload_limit')|default:0}</b></li>
						<li>Загружено фотографий: <b>{$user->getField('photos')|default:0}</b></li>
						{if $user->getField('upload_tstamp')}
						<li>Дата последней загрузки: <b>{$user->getField('upload_tstamp')|datetime}</b></li>
						{/if}
						{if $user->getField('upload_next_tstamp')}
						<li>Дата следующей загрузки после окончания оставшихся загрузок: <b>{$user->getField('upload_next_tstamp')|datetime:'d.m.Y G:i'}</b></li>
						{/if}
					</ul>
				</div>
			{/capture}
			{boxinfo content=$upload_stat type=3 scheme=green}

			<div class="clear_medium"></div>

			{capture assign="upload_info"}
				<ul class="dot">
					<li>Загрузка до <b>{$user->getPeriodicalUploadLimit()}</b> фотографий в день.</li>
				</ul>
			{/capture}
			{boxinfo content=$upload_info type=3 scheme=yellow}

			<div class="clear_medium"></div>
		{/if}

		{capture assign="upload_warning"}
			<ul class="dot">
				<li class="mrb_small">Авторские права на загружаемое изображение должны принадлежать вам. Или на это должно быть дано разрешение автора.</li>
				<li class="mrt_small mrb_small">Изображение не должно содержать в себе элементов порнографии, призывов к насилию, межнациональным или религиозным конфликтам.</li>
				<li class="mrt_small">Запрещается публикация изображений не представляющих художественной ценности.</li>
			</ul>
		{/capture}
		{boxinfo content=$upload_warning type=3 scheme=red}

	{elseif $IS_EDIT}

		{capture assign="photo_preview"}
			<div class="box_in_box box_info">
				<center id="p_preview" class="pdt pdb" style="background-color:rgb({$photo->getCustomField('rgb')})">
					<a href="{href target='photo' p1=$photo->getId() var=$photo}" title="{$photo->getCustomField(name)|escape:'html'}">{photo_img var=$photo p_format='THUMB_150'|constant:'ThumbModel'}</a>
				</center>
			</div>
		{/capture}
		{boxinfo content=$photo_preview type=2}

	{/if}

</div>

<div class="fr" style="width:700px">

<table width="100%" cellspacing="2" cellpadding="4" border="0" class="small">
<tbody>
<colgroup>
	<col wifth="15%" />
	<col wifth="85%" />
</colgroup>
<tr>
	<td class="hright">
		&nbsp;
	</td>
	<td class="hleft">
	{if $IS_UPLOAD}
		<h1>Загрузка фото</h1>
	{else}
		<h1>Редактирование фото</h1>
	{/if}
	</td>
</tr>

{if $photo->isError()}
<tr>
	<td class="hright">
		&nbsp;
	</td>
	<td class="hleft">
		{errorbox error=$photo->getErrorObject()}
	</td>
</tr>
{/if}

{if $IS_UPLOAD}
<tr>
	<td class="vtop hright">
		Фото <span class="red mrl_small">*</span>
	</td>
	<td class="vtop hleft">
		<input type="hidden" name="MAX_FILE_SIZE" value="{$MAX_FILE_SIZE_BYTES}" />
		<input type="file" name="photo" id="photo" size="32" value="" {$disabled_form_field} />
		<div class="clear_small"></div>
		<div class="gray">Максимальный размер загружаемого изображение - {$MAX_FILE_SIZE_MBYTES}</div>
		<div class="gray">Поддерживаемые форматы: {$ALLOWED_FORMATS}</div>
		<div class="gray">Минимальные ширина и высота фотографии - {'MIN_WIDTH'|constant:'ThumbModel'}х{'MIN_HEIGHT'|constant:'ThumbModel'} px</div>
	</td>
</tr>
{/if}

<tr>
	<td class="vtop hright">
		Жанр <span class="red mrl_small">*</span>
	</td>
	<td class="hleft">
		<select name="genre" id="genre" class="select select_cons">
			<option value="0">-- Выберите жанр --</option>
			{foreach from=$listGenre key=genre_id item=genre name=foreach_genre}
			<option value="{$genre_id}" {checked_selected type="option" arg1=$genre_id arg2=$photo->getCustomField('genre')}>{$genre.name}</option>
			{/foreach}
		</select>
		<div class="clear"></div>
	</td>
</tr>

<tr>
	<td class="vtop hright">
		Название <span class="white mrl_small">*</span>
	</td>
	<td class="hleft">
		<input type="text" name="name" id="name" class="input_text" style="width:100%" maxlength="{$Validator->photoNameMaxLength}" value="{$photo->getCustomField('name')|escape:'html'}" {$disabled_form_field} />
		<div class="gray">Интересные названия к вашим фотографиям помогут быстрее найти их среди других фоторабот</div>
	</td>
</tr>

<tr>
	<td class="vtop hright">
		Описание <span class="white mrl_small">*</span>
	</td>
	<td class="hleft">
		<textarea cols="5" rows="" name="description" id="description" class="input_text" style="width:100%" maxlength="{$Validator->photoDescMaxLength}" autocomplete="off" {$disabled_form_field}>{$photo->getCustomField('description')|escape:'html'}</textarea>
	</td>
</tr>

<tr>
	<td class="vtop hright">
		Фон <span class="white mrl_small">*</span>
	</td>
	<td class="hleft">
		<select name="rgb" id="rgb" class="select select_cons" style="background-color:rgb({$photo->getCustomField('rgb')})" {$disabled_form_field}>
			{foreach from=$listColorRGB key=index item=color_rgb name=foreach_rgb_color}
			<option value="{$color_rgb}" style="background-color:rgb({$color_rgb}); width:100px" rel="rgb({$color_rgb})" {checked_selected type="option" arg1=$color_rgb arg2=$photo->getCustomField('rgb')}>&nbsp;</option>
			{/foreach}
		</select>
		{literal}
		<script type="text/javascript">
		__$('rgb').children().bind('click', function(index, item){
			var color_rgb = $(this).attr('rel');
			__$('rgb').css('background-color', color_rgb);
			__$('p_preview').css('background-color', color_rgb);
		});
		</script>
		{/literal}
	</td>
</tr>

<tr>
	<td class="hright">
		&nbsp;
	</td>
	<td class="hleft">
		<hr class="casper" width="100%" />
		<div class="clear_small"></div>
		<span class="red">*</span>
		<span class="gray"> Поля, обязательные для заполнения</span>
		<div class="clear_small"></div>
	</td>
</tr>

<tr>
	<td class="hright">
		&nbsp;
	</td>
	<td class="hleft">
		{if $IS_UPLOAD}
			<input type="submit" class="button" id="upload" value="Загрузить" {$disabled_form_field} style="width:300px" />
			{literal}
			<script type="text/javascript">
			function upload_manager() {
				var upload = __$('upload');
				if(__$('select[name=genre]').val()>0) {
					upload.removeAttr('disabled');
					upload.removeClass('btn_cons');
				}
				else {
					upload.attr('disabled', true);
					upload.addClass('btn_cons');
				}
			}
			__$('select[name=genre]').bind('change', function(){upload_manager()});
			upload_manager();
			</script>
			{/literal}
		{elseif $IS_EDIT}
		<div class="fl mrr_big">
			<input type="submit" name="" id="" class="button" value="Сохранить" style="width:300px" />
		</div>
		<div class="fr">
			<input type="button" name="" id="btn_cancel" class="button btn_cons" value="Отмена" />
			{literal}
			<script type="text/javascript">
			__$('btn_cancel').bind('click', function(){
				if(window.confirm('Покинуть страницу без сохранения?')) {
					window.location.href = {/literal}"{href target='photo' p1=$photo->getId() var=$photo}"{literal};
				}
			});
			</script>
			{/literal}
		</div>
		<div class="clear"></div>
		{/if}
	</td>
</tr>

</tbody>
</table>

</div>

</form>