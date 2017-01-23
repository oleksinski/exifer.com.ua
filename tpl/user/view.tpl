{assign var="user_name" value=$user->getExtraField('name')}

<div class="fl bigcol">

	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="">
	<tbody>
		<colgroup>
			<col width="1%" />
		</colgroup>
		<tr>
			<td class="vtop hleft">
				{userpic_img var=$user u_format='FORMAT_300'|constant:'UserpicModel' alt=$user_name}
				{if $user->getId()==$USER->getId()}
					<div class="clear_small"></div>
					<a href="{href target='user_edit'}" title="Редактировать профайл" class="tech_dashed">Редактировать профайл</a>
					<div class="clear"></div>
				{/if}
			</td>
			<td class="vtop hleft pdl">

				<h3>{$user_name} {$user|online}</h3>
				<div class="clear_small pdt_tiny"></div>

				{if $USER->isModerator() && $user->isBanned()}
					<span class="small mrr_tiny red">Забанен до {$user->getField('ban_tstamp')|datetime} ({$user->getField('ban_tstamp')})</span>
					<div class="clear_small"></div>
				{/if}

				<span class="gray small mrr_tiny">Пол:</span> {$user|gender}
				<div class="clear_small"></div>

				<span class="gray small mrr_tiny">Страна/город:</span> {$user|location}
				<div class="clear_small"></div>

				<span class="gray small mrr_tiny">Дата рождения:</span> {$user|birthday}
				<div class="clear_small"></div>

				{if !$user->isAdmin()}
				<span class="gray small mrr_tiny">Зарегистрирован:</span> {$user->getField('reg_tstamp')|datetime}
				<div class="clear_small"></div>

				<span class="gray small mrr_tiny">Последнee посещение:</span> {$user->getField('hit_tstamp')|datetime}
				<div class="clear_small"></div>
				{/if}

				{if $user->getExtraField('occupation')}
					<span class="gray small mrr_tiny">Специализация:</span> {$user|occupation}
					<div class="clear_small"></div>
				{/if}

				<hr class="casper pdb_tiny" />

				<span class="gray small mrr_tiny" style="width:150px"><a href="{href target='photo_lenta' uid=$user->getId()}" title="">Фотографий:</a></span> {item_count item=photos var=$user type=user}
				<span class="gray mrl_tiny mrr_tiny"> | </span>
				<span class="gray small mrr_tiny"><a href="{href target='comment_lenta' uid=$user->getId()}" title="">Комментариев:</a></span> {item_count item=comments var=$user type=user}
				<div class="clear_small"></div>

				<span class="gray small mrr_tiny">Просмотров:</span> {item_count item=views var=$user type=user}
				<div class="clear_small"></div>

				<!--{*
				<span class="gray small mrr_tiny">Рейтинг:</span> {item_count item=rate var=$user type=user}
				*}-->

				{if $user->getExtraField('about_emails') || $user->getExtraField('about_phones') || $user->getExtraField('about_urls') || $user->getExtraField('about_ims')}
					<h3 class="pdt pdb_small">Контакты</h3>
					{foreach from=$user->getExtraField('about_emails') key=index item=email name=foreach_email}
						Email: {mailto address=$email encode='javascript'}
						<div class="clear_small"></div>
					{/foreach}
					{foreach from=$user->getExtraField('about_phones') key=index item=phone name=foreach_phone}
						Телефон: {$phone|escape:'html'}
						<div class="clear_small"></div>
					{/foreach}
					{foreach from=$user->getExtraField('about_ims') key=index item=im name=foreach_im}
						{foreach from=$im key=im_key item=im_value name=foreach_im}
							{if $im_key=='IM_ICQ'|constant:'User'}
								{$im_key|im}: {$im_value|escape:'html'} <a href="http://www.icq.com/people/{$im_value|escape:'html'}/"><img src="http://icq.com/scripts/online.dll?icq={$im_value|escape:'html'}&img=5" alt=""></a>
							{else}
								{$im_key|im}: {$im_value|escape:'html'}
							{/if}
							<div class="clear_small"></div>
						{/foreach}
					{/foreach}
					{foreach from=$user->getExtraField('about_urls') key=index item=url name=foreach_url}
						URL: {$url|urlify:150}
						<div class="clear_small"></div>
					{/foreach}
				{/if}

				<div class="clear_medium"></div>
				<div>Альтернативный адрес страницы</div>
				{if $altProfileUrl.user!=$altProfileUrl.userByIdName}
					<a href="{href target='userByIdName' p1=$user->getId() var=$user}" title="{$user->getExtraField('name')}" rel="nofollow" class="tech_dashed">{href target='userByIdName' p1=$user->getId() var=$user relative=false}</a>
					<div class="clear_small"></div>
				{/if}
				{if $altProfileUrl.user!=$altProfileUrl.userByUrlname}
					<a href="{href target='userByUrlname' p1=$user->getId() var=$user}" title="{$user->getExtraField('name')}" rel="nofollow" class="tech_dashed">{href target='userByUrlname' p1=$user->getId() var=$user relative=false}</a>
					<div class="clear_small"></div>
				{/if}
				{if $altProfileUrl.user!=$altProfileUrl.userById}
					<a href="{href target='userById' p1=$user->getId() var=$user}" title="{$user->getExtraField('name')}" rel="nofollow" class="tech_dashed">{href target='userById' p1=$user->getId() var=$user relative=false}</a>
					<div class="clear_small"></div>
				{/if}

				{if $user->getField('about')}
					<h3 class="pdt pdb_small">О себе</h3>
					{$user->getField('about')|urlify}
				{/if}
			</td>
		</tr>
	</tbody>
	</table>

</div>

<div class="fr smallcol">

	{if $genreList}

		{capture assign="capture_smallcol"}

		<div class="box_in_box box_info pdt_tiny">

			<h3 class="hnill">
				<a href="{href target='photo_lenta' uid=$user->getId()}" title="Фотографии по жанрам">Фотографии по жанрам</a> <span class="small gray mrl_tiny">({$user->getField('photos')})</span>
			</h3>

			<div class="small">
				{foreach from=$genreList key=genre_id item=genre name=foreach_genre}
					{assign var="__iteration__" value=$smarty.foreach.foreach_genre.iteration%2}
					<div class="pdt_tiny {if $__iteration__}fl mrr_big{else}fl{/if}" style="width:130px;">
						<div class="fl">
							{if $genre.count}
								<a href="{href target='photo_lenta' genre=$genre_id uid=$user->getId()}" title="{$genre.name} ({$genre.count})">{$genre.name}</a>
							{else}
								<span class="gray">{$genre.name}</span>
							{/if}
						</div>
						{if $genre.count}
							<div class="fr gray">{$genre.count}</div>
						{/if}
						<div class="clear2"></div>
					</div>
					{if $__iteration__==0}<div class="clear2"></div>{/if}
				{/foreach}

			</div>

		</div>

	{/if}

	<div class="clear_medium"></div>

	<input type="button" name="jscal_btn" id="jscal_btn" value="Фотографий автора по дате &darr;" class="button btn_cons" style="width:100%" />
	<input type="text" name="cal2_field" id="cal2_field" value="" class="hidden" />

	{literal}
	<script type="text/javascript">

	var TOOLTIP_START = '<div class="hcenter">Добавлено: ';
	var TOOLTIP_END = ' фото</div>';

	var Cal2_Param = {
		user_id : '{/literal}{$user->getId()}{literal}',
		now_day : '{/literal}{$smarty.now|date_format:"%d"}{literal}',
		now_month : '{/literal}{$smarty.now|date_format:"%m"}{literal}',
		now_year : '{/literal}{$smarty.now|date_format:"%Y"}{literal}',
		now_date : '{/literal}{$smarty.now|date_format:"%Y%m%d"}{literal}',
		url_pattern : {/literal}{href target="photo_lenta" ignore_query=true uid=$user->getId() date="__date__" jsify=true}{literal}
	};

	function o_date(day, month, year) {
		return Calendar.formatString('${d}-${m}-${y}', {d:day, m:month, y:year});
	}

	var ENABLED_DATES = {};

	var ALL_ENABLED_DATES = [];

	var Cal2 = window.Calendar ? Calendar.setup({
		bottomBar : false,
		weekNumbers : true,
		date : Calendar.dateToInt(Cal2_Param.now_date),
		min : 20101105,
		max : Calendar.dateToInt(Cal2_Param.now_date),
		selectionType : Calendar.SEL_MULTIPLE,
		disabled : function(date) {
			return !(Calendar.dateToInt(date) in ENABLED_DATES);
		},
		trigger : 'jscal_btn',
		inputField : 'cal2_field',
		dateInfo : function(date, wantsClassName){
			if(Calendar.dateToInt(date) in ENABLED_DATES) {
				return {
					klass   : 'DynarchCalendar-day-selected',
					tooltip : TOOLTIP_START + ENABLED_DATES[Calendar.dateToInt(date)] + TOOLTIP_END
				};
			}
		},
		onChange : function(cal, date) {

			var day = 1;
			var month = date.getMonth()+1;
			var year = 1900 + date.getYear();
			var jscal_stamp = o_date(day,month,year);

			if(ALL_ENABLED_DATES[jscal_stamp]) {
				ENABLED_DATES = ALL_ENABLED_DATES[jscal_stamp];
				cal.redraw();
			}
			else {
				$.ajax({
					url : {/literal}{'/json/user_upload_date/'|jsify}{literal},
					dataType : 'json',
					success : function(json_response) {
						ENABLED_DATES = json_response || {};
						ALL_ENABLED_DATES[jscal_stamp] = ENABLED_DATES;
						cal.redraw();
					},
					data : {
						'uid' : Cal2_Param.user_id,
						'date' : jscal_stamp,
						'rand': Math.random(),
						'dbg' : 0
					}
				});
			}
		},
		onSelect : function() {
			var cal_date = Calendar.intToDate(this.selection.get());
			var day = Calendar.printDate(cal_date, '%e');
			var month = Calendar.printDate(cal_date, '%o');
			var year = Calendar.printDate(cal_date, '%Y');
			window.location.href = Cal2_Param.url_pattern.replace(/__date__/, o_date(day,month,year));
		}
	}) : {};

	</script>
	{/literal}

{/capture}
{boxinfo content=$capture_smallcol type=2}

</div>

<div class="page">

	<div class="clear"></div>

	<h3>
		<a href="{href target='photo_lenta' uid=$user->getId()}" title="Последние фотографии">Последние фотографии</a>
		<a href="{href target='rss_photo' uid=$user->getId() var=$user}" class="mrl_tiny" title="Последние фотографии">{rss_icon}</a>
	</h3>

	<div class="clear_medium"></div>

	{if $photo_collection_new}
		{photo_lenta collection=$photo_collection_new photo_per_row=6 column=page p_format='THUMB_150'|constant:'ThumbModel'}
	{else}
		Нет фотографий
	{/if}

</div>
