<div class="fl bigcol">

	<div class="mrb_tiny">

	{capture assign="capture_sort"}

		<div class="fl">

			<span class="small gray">Сортировать по:</span>

			{capture assign="capture_orderby_date"}дате{/capture}
			{capture assign="capture_orderby_views"}просмотрам{/capture}
			{capture assign="capture_orderby_uploads"}загрузкам{/capture}
			{capture assign="capture_orderby_hit"}посещениям{/capture}
			<!--{*capture assign="capture_orderby_rating"}рейтингу{/capture*}-->

			<span class="mrl_small">
				{if $request_query.orderby=='id'}
					<b>{$capture_orderby_date}</b>
				{else}
					<a href="{href target='user_lenta' query=$request_query orderby='id'}" title="">{$capture_orderby_date}</a>
				{/if}

				<!--{*
				<span class="gray mrl_tiny mrr_tiny"> | </span>

				{if $request_query.orderby=='rating'}
					<b>{$capture_orderby_rating}</b>
				{else}
					<a href="{href target='user_lenta' query=$request_query orderby='rating'}" title="">{$capture_orderby_rating}</a>
				{/if}
				*}-->

				<span class="gray mrl_tiny mrr_tiny"> | </span>

				{if $request_query.orderby=='views'}
					<b>{$capture_orderby_views}</b>
				{else}
					<a href="{href target='user_lenta' query=$request_query orderby='views'}" title="">{$capture_orderby_views}</a>
				{/if}

				<span class="gray mrl_tiny mrr_tiny"> | </span>

				{if $request_query.orderby=='upload_tstamp'}
					<b>{$capture_orderby_uploads}</b>
				{else}
					<a href="{href target='user_lenta' query=$request_query orderby='upload_tstamp'}" title="">{$capture_orderby_uploads}</a>
				{/if}

				<span class="gray mrl_tiny mrr_tiny"> | </span>

				{if $request_query.orderby=='hit_tstamp'}
					<b>{$capture_orderby_hit}</b>
				{else}
					<a href="{href target='user_lenta' query=$request_query orderby='hit_tstamp'}" title="">{$capture_orderby_hit}</a>
				{/if}
			</span>

		</div>

		<div class="fr">

			<span class="mrr_big">
				<span class="gray">&#8721;</span> {$total_cnt|default:0}
			</span>

			<span class="small gray">Вид:</span>

			{capture assign="v_full_txt"}детально{/capture}
			{capture assign="v_brief_txt"}таблицей{/capture}

			{capture assign="v_full_ico"}
				<img src="{$S_URL}img/icon/%s" width="17" height="17" class="vmid" border="0" alt="{$v_full_txt}" />
			{/capture}

			{capture assign="v_brief_ico"}
				<img src="{$S_URL}img/icon/%s" width="17" height="17" class="vmid" border="0" alt="{$v_brief_txt}" />
			{/capture}

			<span class="mrl_small">
				{if $request_query.viewmode=='full'}
					<b>{$v_full_ico|sprintf:'v_full_selected.gif'}</b>
				{else}
					<a href="{href target='user_lenta' query=$request_query viewmode='full'}" title="{$v_full_txt}">{$v_full_ico|sprintf:'v_full_inactive.gif'}</a>
				{/if}

				<span class="gray mrl_tiny mrr_tiny"> | </span>

				{if $request_query.viewmode=='brief'}
					<b>{$v_brief_ico|sprintf:'v_thumb_selected.gif'}</b>
				{else}
					<a href="{href target='user_lenta' query=$request_query viewmode='brief'}" title="{$v_brief_txt}">{$v_brief_ico|sprintf:'v_thumb_inactive.gif'}</a>
				{/if}
			</span>
		</div>

		<div class="clear"></div>

	{/capture}
	{boxinfo content=$capture_sort type=2 width='100%'}
	</div>

	{if isset($request_query.occupation)}
	<div class="mrb_tiny">
		{capture assign="capture_occupation_filter"}
			<span class="small gray">Специализация:</span>
			<span class="mrl_small">
				<b>{$occupation_data.name}</b> <a href="{href target='rss_user' occupation=$occupation_data.id}" class="mrl_tiny" title="{$occupation_data.name}">{rss_icon}</a>
				{if isset($request_query.experience)}<span class="gray small"> ({$experience_data.name})</span>{/if}
				<span class="gray mrl_tiny mrr_tiny"> | </span>
				<a href="{href target='user_lenta' query=$request_query occupation=null experience=null}" title="">все специализации</a>
			</span>
		{/capture}
		{boxinfo content=$capture_occupation_filter type=3 scheme=green}
	</div>
	{/if}

	{if isset($request_query.country)}
	<div class="mrb_tiny">
		{capture assign="capture_location_filter"}
			<span class="small gray">Страна/город:</span>
			<span class="mrl_small">
				<b>{$country_data.name}</b>
				{if isset($request_query.city)}<span class="gray small"> ({$city_data.name})</span>{/if}
				<span class="gray mrl_tiny mrr_tiny"> | </span>
				<a href="{href target='user_lenta' query=$request_query country=null city=null}" title="">все страны&nbsp;/&nbsp;города</a>
			</span>
		{/capture}
		{boxinfo content=$capture_location_filter type=3 scheme=magenta}
	</div>
	{/if}

	{if isset($request_query.date)}
	<div class="mrb_tiny">
		{capture assign="capture_time_filter"}
			<span class="small gray">За период:</span>
			<span class="mrl_small">
				<b>{$filter_date_from|datetime:'d.m.Y'} &ndash; {$filter_date_to|datetime:'d.m.Y'}</b>
				<span class="gray mrl_tiny mrr_tiny"> | </span>
				<a href="{href target='user_lenta' query=$request_query date=null}" title="">все время</a>
			</span>
		{/capture}
		{boxinfo content=$capture_time_filter type=3 scheme=yellow}
	</div>
	{/if}

	{if isset($request_query.q) && $request_query.q}
		<div class="clear2"></div>
		<div class="mrb_tiny">
			{capture assign="capture_user_filter"}
				<span class="small gray">Поиск:</span>
				<span class="mrl_small">
					<form name="q_form" id="q_form" method="get" action="{href target='user_lenta'}" accept-charset="utf-8">
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

	{if $user_collection->length()}

		{pager var=$pager}

		<div class="clear_medium"></div>

		{if isset($request_query.viewmode) && $request_query.viewmode=='brief'}

			<div class="small">
				{user_lenta collection=$user_collection user_per_row=2 highlight=$request_query.q|default:null}
			</div>

		{else}

			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">

			{foreach from=$user_collection key=user_id item=user name=foreach_user_lenta}

				{assign var="user_name" value=$user->getExtraField('name')}

				<tr class="pdr_small">
					<td width="75" class="vtop pdr hright">
						<a href="{href target='user' p1=$user_id var=$user}" title="{$user_name}">{userpic_img var=$user u_format='FORMAT_75'|constant:'UserpicModel'}</a>
					</td>
					<td width="580" class="vtop mrl_big hleft">
						<h2><a href="{href target='user' p1=$user_id var=$user}" title="{$user_name}">
							{if isset($request_query.q) && $request_query.q}
								{$user_name|highlight:$request_query.q}
							{else}
								{$user_name}
							{/if}
						</a> {$user|online}</h2>
						<div class="clear_small"></div>

						{if $USER->isModerator() && $user->isBanned()}
							<span class="red mrr_tiny">Забанен до {$user->getField('ban_tstamp')|datetime} ({$user->getField('ban_tstamp')})</span>
							<div class="clear_small"></div>
						{/if}

						{if $user->getExtraField('occupation')}
						<span class="gray mrr_tiny">Специализация:</span> {$user|occupation}
						<div class="clear_small"></div>
						{/if}

						{if !$user->isAdmin()}
						<span class="gray mrr_tiny">Зарегистрирован:</span> {$user->getField('reg_tstamp')|datetime}
						<span class="gray mrl_tiny mrr_tiny"> | </span>
						<span class="gray mrr_tiny">Последнee посещение:</span> {$user->getField('hit_tstamp')|datetime}
						<div class="clear_small"></div>
						{/if}

						<span class="gray mrr_tiny">Страна/город:</span> {$user|location}
						<span class="gray mrl_tiny mrr_tiny"> | </span>

						<span class="gray mrr_tiny">Пол:</span> {$user|gender}
						<div class="clear_small"></div>

						<span class="gray mrr_tiny"><a href="{href target='photo_lenta' uid=$user_id}" title="">Фотографий:</a></span> {item_count item=photos var=$user type=user}
						<span class="gray mrl_tiny mrr_tiny"> | </span>

						<span class="gray mrr_tiny"><a href="{href target='comment_lenta' uid=$user_id}" title="">Комментариев:</a></span> {item_count item=comments var=$user type=user}
						<span class="gray mrl_tiny mrr_tiny"> | </span>

						<span class="gray mrr_tiny">Просмотров:</span> {item_count item=views var=$user type=user}

						<!--{*
						<span class="gray mrl_tiny mrr_tiny"> | </span>
						<span class="gray mrr_tiny">Рейтинг:</span> {item_count item=rate var=$user type=user}
						*}-->

						{if $user->getCustomField('photo_collection')->length()}
							<div class="clear_big"></div>
							{photo_lenta photo_per_row=6 collection=$user->getCustomField('photo_collection') p_format='THUMB_75'|constant:'ThumbModel'}
						{/if}

					</td>
				</tr>

				{if !$smarty.foreach.foreach_user_lenta.last}
					<tr style="height:30px"><td class="vmid" width="100%" colspan="2"><hr class="casper"/></td></tr>
				{/if}

			{/foreach}

			</table>

		{/if}

		<div class="clear_small"></div>

		{pager var=$pager}

	{else}

		<div class="mrt_small">Нет пользователей</div>

	{/if}

</div>

<div class="fr smallcol">

	{if $USER->isAdmin()}
	<div class="mrb_tiny">
		{capture assign="capture_status_filter"}
			<span class="small gray">Статус:</span>
			<span class="mrl_small">
				{if !isset($request_query.status) || $request_query.status=='STATUS_OKE'|constant:'User'}
					<b>активный</b>
					<span class="gray mrl_tiny mrr_tiny"> | </span>
					<a href="{href target='user_lenta' query=$request_query status='STATUS_NEW'|constant:'User' p=null}" title="">неактивный</a>
				{elseif isset($request_query.status) && $request_query.status=='STATUS_NEW'|constant:'User'}
					<a href="{href target='user_lenta' query=$request_query status='STATUS_OKE'|constant:'User' p=null}" title="">активный</a>
					<span class="gray mrl_tiny mrr_tiny"> | </span>
					<b>неактивный</b>
				{/if}
			</span>
		{/capture}
		{boxinfo content=$capture_status_filter type=3 scheme=red}
	</div>
	{/if}

	<div class="mrb_tiny">
		{capture assign="capture_online_filter"}
			<div class="clear"></div>
			<span class="small gray">Online-фильтр:</span>
			<span class="mrl_small">
				{if !isset($request_query.online) || $request_query.online=='ONLINE_ALL'|constant:'User'}
					<b>все</b>
					<span class="gray mrl_tiny mrr_tiny"> | </span>
					<a href="{href target='user_lenta' query=$request_query online='ONLINE_ON'|constant:'User' p=null}" title="online">online</a>
					<span class="gray mrl_tiny mrr_tiny"> | </span>
					<a href="{href target='user_lenta' query=$request_query online='ONLINE_OFF'|constant:'User' p=null}" title="offline">offline</a>
				{elseif isset($request_query.online) && $request_query.online=='ONLINE_ON'|constant:'User'}
					<a href="{href target='user_lenta' query=$request_query online=null p=null}" title="все">все</a>
					<span class="gray mrl_tiny mrr_tiny"> | </span>
					<b>online</b>
					<span class="gray mrl_tiny mrr_tiny"> | </span>
					<a href="{href target='user_lenta' query=$request_query online='ONLINE_OFF'|constant:'User' p=null}" title="offline">offline</a>
				{elseif isset($request_query.online) && $request_query.online=='ONLINE_OFF'|constant:'User'}
					<a href="{href target='user_lenta' query=$request_query online=null p=null}" title="все">все</a>
					<span class="gray mrl_tiny mrr_tiny"> | </span>
					<a href="{href target='user_lenta' query=$request_query online='ONLINE_ON'|constant:'User' p=null}" title="online">online</a>
					<span class="gray mrl_tiny mrr_tiny"> | </span>
					<b>offline</b>
				{/if}
			</span>
			<div class="clear"></div><div class="clear"></div>
		{/capture}
		{boxinfo content=$capture_online_filter type=2}
	</div>

	<div id="js_cal" class="mrr_tiny">
		<input type="button" name="jscal_btn" id="jscal_btn" value="Дата регистрации &darr;" class="button btn_cons" style="width:100%" />
		<input type="text" name="cal2_field" id="cal2_field" value="" class="hidden" />
		<div class="clear_medium"></div>
		{literal}
		<script type="text/javascript">
		var Cal2_Param = {
			now_day : '{/literal}{$smarty.now|date_format:"%d"}{literal}',
			now_month : '{/literal}{$smarty.now|date_format:"%m"}{literal}',
			now_year : '{/literal}{$smarty.now|date_format:"%Y"}{literal}',
			now_date : '{/literal}{$smarty.now|date_format:"%Y%m%d"}{literal}',
			date_from : '{/literal}{$filter_date_from|date_format:"%Y%m%d"}{literal}',
			date_to : '{/literal}{$filter_date_to|date_format:"%Y%m%d"}{literal}',
			url_pattern : {/literal}{href target="user_lenta" query=$request_query date="__date__" p=null jsify=true}{literal}
		};
		function o_date(day, month, year) {
			return Calendar.formatString('${d}-${m}-${y}', {d:day, m:month, y:year});
		}
		var Cal2 = window.Calendar ? Calendar.setup({
			bottomBar : false,
			weekNumbers : true,
			date : Calendar.dateToInt(Cal2_Param.date_from),
			max : Calendar.dateToInt(Cal2_Param.now_date),
			selectionType : Calendar.SEL_SINGLE,
			{/literal}{if isset($request_query.date)}
			selection : [[Calendar.dateToInt(Cal2_Param.date_from), Calendar.dateToInt(Cal2_Param.date_to)]],
			{/if}{literal}
			trigger : 'jscal_btn',
			inputField : 'cal2_field',
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
	</div>

	{capture assign="capture_rightcol"}

	{if $occupation_arr}

		<div class="box_in_box box_info pdt_tiny">

			<div class="mrl_big">

				<h3 class="hnill">Специализация</h3>

				<div class="clear_small"></div>

				<ul class="dot">
				{foreach from=$occupation_arr key=o_id item=o_data name=foreach_occupation}

					{if isset($request_query.occupation) && $request_query.occupation==$o_id}
						{assign var="o_selected" value=true}
					{else}
						{assign var="o_selected" value=false}
					{/if}

					{if isset($occupation_experience_arr.$o_id) && $occupation_experience_arr.$o_id|@count}
						{assign var="o_nested" value=true}
					{else}
						{assign var="o_nested" value=false}
					{/if}

					<li><a href="{href target='user_lenta' query=$request_query occupation=$o_id experience=null p=null}" title="{$o_data.name}" class="{if $o_selected}bold{/if}" rel="nofollow">{$o_data.name}</a></li>

					{if $o_nested && $o_selected}
						<ul id="o_{$o_id}" class="dotted small">
						{foreach from=$experience_arr key=e_id item=e_data name=foreach_experience}
							{if in_array($e_id, $occupation_experience_arr.$o_id)}
								<li><a href="{href target='user_lenta' query=$request_query occupation=$o_id experience=$e_id p=null}" title="{$e_data.name}" class="{if isset($request_query.experience) && $request_query.experience==$e_id && $o_selected}bold{/if}" rel="nofollow">{$e_data.name}</a></li>
							{/if}
						{/foreach}
						</ul>
					{/if}

				{/foreach}

				</ul>

			</div>

		</div>

		<div class="clear_medium"></div>

	{/if}

	{if $country_arr}

		<div class="box_in_box box_info pdt_tiny">

			<div class="mrl_big">

				<h3 class="hnill">Страна/город</h3>

				<div class="clear_small"></div>

				<ul class="dot">
				{foreach from=$country_arr key=co_id item=co_data name=foreach_country}

					{if isset($request_query.country) && $request_query.country==$co_id}
						{assign var="co_selected" value=true}
					{else}
						{assign var="co_selected" value=false}
					{/if}

					<li><a href="{href target='user_lenta' query=$request_query country=$co_id p=null}" title="{$co_data.name}" class="{if $co_selected}bold{/if}" rel="nofollow">{$co_data.name}</a></li>

					{if $co_selected && $city_arr}
						<ul id="co_{$co_id}" class="dotted small">
						{foreach from=$city_arr key=ci_id item=ci_data name=foreach_city}
							<li><a href="{href target='user_lenta' query=$request_query country=$co_id city=$ci_id p=null}" title="{$ci_data.name}" class="{if isset($request_query.city) && $request_query.city==$ci_id && $co_selected}bold{/if} {if $ci_data.is_capital||$ci_data.is_main}underline{/if}" rel="nofollow">{$ci_data.name}</a></li>
						{/foreach}
						</ul>
					{/if}

				{/foreach}
				</ul>

			</div>

		</div>

	{/if}

	{/capture}

	{boxinfo content=$capture_rightcol type=2}

</div>
