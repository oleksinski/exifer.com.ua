<div class="fr smallcol">

	{if $user->exists()}
		{assign var="user_name" value=$user->getExtraField('name')}
		{capture assign="capture_photo_user"}
		<div class="box_in_box box_info small">
			<a href="{href target='user' p1=$user->getId() var=$user}" title="{$user_name}">{userpic_img var=$user u_format='FORMAT_75'|constant:'UserpicModel' alt=$user_name align=left class=mrr_small width=35 height=35} {$user_name}</a> {$user|online}
			{if $user->getExtraField('occupation')}
			<div class="mrt_tiny">{$user|occupation:true}</div>
			{/if}
			<div class="clear2"></div>
		</div>
		{/capture}
		{boxinfo content=$capture_photo_user type=2}
		<div class="clear_small"></div>
	{/if}

	<div class="fl" style="width:230px">
		{include file="samples/genre.tpl" genre_collection=$genre_collection page_target=$page_target}
	</div>

	<div class="fr mrr_tiny" style="width:70px">
		<input type="button" name="jscal_btn" id="jscal_btn" value="Дата &darr;" class="button btn_cons" />
		<input type="text" name="cal2_field" id="cal2_field" value="" class="hidden" />

		{literal}
		<script type="text/javascript">

		var Cal2_Param = {
			now_day : '{/literal}{$smarty.now|date_format:"%d"}{literal}',
			now_month : '{/literal}{$smarty.now|date_format:"%m"}{literal}',
			now_year : '{/literal}{$smarty.now|date_format:"%Y"}{literal}',
			now_date : '{/literal}{$smarty.now|date_format:"%Y%m%d"}{literal}',
			date_from : '{/literal}{$filter_date_from|date_format:"%Y%m%d"}{literal}',
			date_to : '{/literal}{$filter_date_to|date_format:"%Y%m%d"}{literal}',
			url_pattern : {/literal}{href target=$page_target query=$request_query date="__date__" p=null jsify=true}{literal}
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

	<div class="clear2"></div>

</div>
