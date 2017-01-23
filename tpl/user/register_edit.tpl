<form name="form_register_edit" action="{$smarty.server.PHP_SELF}" method="post" enctype="multipart/form-data" accept-charset="utf-8">

{assign var="IS_REGISTER" value=!$USER->exists()}
{assign var="IS_EDIT" value=!$IS_REGISTER}

<table width="100%" cellspacing="2" cellpadding="4" border="0" class="small">
<tbody>
<colgroup>
	<col width="30%" />
	<col width="70%" />
</colgroup>
<tr>
	<td class="hright">
		&nbsp;
	</td>
	<td class="hleft">
	{if $IS_REGISTER}
		<h1>Регистрация</h1>
	{elseif $IS_EDIT}
		<h1>Редактирование профайла</h1>
	{/if}
	</td>
</tr>

{if $user->isError()}
<tr>
	<td class="hright">
		&nbsp;
	</td>
	<td class="hleft">
		{errorbox error=$user->getErrorObject()}
	</td>
</tr>
{/if}


{if $IS_REGISTER}
<tr>
	<td class="hright">
		E-mail <span class="red mrl_small">*</span>
	</td>
	<td class="hleft">
		<input type="text" name="email" id="email" class="input_text" maxlength="{$Validator->emailMaxLength}" value="{$user->getCustomField('email')|escape:'html'}" autocomplete="off" />
		<span class="gray mrl">Ваш email не будет отображаться на страницах сайта</span>
	</td>
</tr>
{/if}


{if $IS_EDIT}
<tr>
	<td class="hright">
		E-mail <span class="white mrl_small">*</span>
	</td>
	<td class="hleft">
		{$user->getCustomField('email')|escape:'html'}
	</td>
</tr>
{/if}


{if $IS_REGISTER}
<tr>
	<td class="hright">
		Пароль <span class="red mrl_small">*</span>
	</td>
	<td class="hleft">
		<input type="password" name="password" id="password" class="input_text" maxlength="{$Validator->passwordMaxLength}" value="{$user->getCustomField('password')|escape:'html'}" />
		<span class="gray mrl">Минимальная длина пароля - {$Validator->passwordMinLength} символа</span>
	</td>
</tr>
{/if}


{if $IS_EDIT}

<tr id="tr_password_change" class="hidden">
	<td class="hright">
		Пароль <span class="white mrl_small">*</span>
	</td>
	<td class="hleft">
		*************
		<a href="javascript://" title="Изменить пароль" id="a_password_change" class="gray mrl_small">Изменить пароль</a>
	</td>
</tr>

<tr rel="rel_password_change">
	<td class="hright">
		Текущий пароль <span class="red mrl_small">*</span>
	</td>
	<td class="hleft">
		<input type="password" name="password_verify" id="password_verify" class="input_text" maxlength="{$Validator->passwordMaxLength}" value="{$user->getCustomField('password_verify')|escape:'html'}" />
	</td>
</tr>

<tr rel="rel_password_change">
	<td class="hright">
		Новый пароль <span class="red mrl_small">*</span>
	</td>
	<td class="hleft">
		<input type="password" name="password_new" id="password_new" class="input_text" maxlength="{$Validator->passwordMaxLength}" value="{$user->getCustomField('password_new')|escape:'html'}" />
	</td>
</tr>

{if !$user->getCustomField('password_new')}
	{literal}
	<script type="text/javascript">
	var tr_list = $('tr[rel=rel_password_change]').hide();
	var tr_password_control = __$('tr_password_change').show();
	__$('a_password_change').bind('click', function(){
		tr_list.show();
		tr_password_control.hide();
	});
	</script>
	{/literal}
{/if}

{/if}

<tr>
	<td class="hright">
		Имя, фамилия <span class="red mrl_small">*</span>
	</td>
	<td class="hleft">
		<input type="text" name="name" id="name" class="input_text" maxlength="{$Validator->nameMaxLength}" value="{$user->getCustomField('name')|escape:'html'}" autocomplete="off" />
	</td>
</tr>

{assign var="GENDER_MALE" value='GENDER_MALE'|constant:'User'}
{assign var="GENDER_FEMALE" value='GENDER_FEMALE'|constant:'User'}

<tr>
	<td class="hright">
		Пол <span class="red mrl_small">*</span>
	</td>
	<td class="hleft">
		<input type="radio" name="gender" id="gender_male"  value="{$GENDER_MALE}" {checked_selected type="checkbox" arg1=$GENDER_MALE arg2=$user->getCustomField('gender')} />{label for='gender_male' data=$GENDER_MALE|gender}
		<input type="radio" name="gender" id="gender_female"  value="{$GENDER_FEMALE}" {checked_selected type="checkbox" arg1=$GENDER_FEMALE arg2=$user->getCustomField('gender')} />{label for='gender_female' data=$GENDER_FEMALE|gender}
	</td>
</tr>

<tr>
	<td class="hright">
		Дата рождения <span class="red mrl_small">*</span>
	</td>
	<td class="hleft">
		<select name="day" id="day">
			{foreach from=$listDay key=index item=day name=foreach_day}
			<option value="{$day}" {checked_selected type="option" arg1=$day arg2=$user->getCustomField('day')}>{$day}</option>
			{/foreach}
		</select>
		<select name="month" id="month" class="mrl_small">
			{foreach from=$listMonth key=index item=month name=foreach_month}
			<option value="{$index}" {checked_selected type="option" arg1=$index arg2=$user->getCustomField('month')}>{$month}</option>
			{/foreach}
		</select>
		<select name="year" id="year" class="mrl_small">
			{foreach from=$listYear key=index item=year name=foreach_year}
			<option value="{$year}" {checked_selected type="option" arg1=$year arg2=$user->getCustomField('year')}>{$year}</option>
			{/foreach}
		</select>
		<span class="mrl_small">
			<input type="button" name="jscal_btn" id="jscal_btn" value="..." class="button" />
			<input type="text" name="cal2_field" id="cal2_field" value="" class="hidden" />
			{literal}
			<script type="text/javascript">

			function o_date(day, month, year) {
				return Calendar.parseDate(Calendar.formatString('${d}/${m}/${y}', {d:day, m:month, y:year}));
			}

			var Cal2 = window.Calendar ? Calendar.setup({
				bottomBar : false,
				weekNumbers : true,
				min : Calendar.dateToInt(o_date(1, 1, __$('year option:last').val())),
				max : Calendar.dateToInt(o_date(31, 12, __$('year option:first').val())),
				date : Calendar.dateToInt(o_date(__$('day').val(), __$('month').val(), __$('year').val())),
				selectionType : Calendar.SEL_SINGLE,
				align: 'TR',
				trigger : 'jscal_btn',
				inputField : 'cal2_field',
				onSelect : function() {

					var cal_date = Calendar.intToDate(this.selection.get());

					var year = Calendar.printDate(cal_date, '%Y');
					__$('year').children().each(function(index, item){
						if($(item).val()==year) {
							$(item).attr('selected', true);
						}
					});

					var month = Calendar.printDate(cal_date, '%o');
					__$('month').children().each(function(index, item){
						if($(item).val()==month) {
							$(item).attr('selected', true);
						}
					});

					var day = Calendar.printDate(cal_date, '%e');
					__$('day').children().each(function(index, item){
						if($(item).val()==day) {
							$(item).attr('selected', true);
						}
					});

					this.hide();
				}
			}) : {};

			</script>
			{/literal}

		</span>
		<span class="mrl_small">
			<label class="m_mrl_tiny">
				<input type="checkbox" name="hide_birthday" id="hide_birthday" value="1" class="vmid" {checked_selected type="checkbox" arg1=$user->getCustomField('hide_birthday') arg2=true} />
				не показывать дату рождения
			</label>
		</span>
	</td>
</tr>

<tr>
	<td class="hright">
		Страна/город <span class="red mrl_small">*</span>
	</td>
	<td class="hleft">
		<select name="country" id="country" onchange="javascript: return window.js_loc.get_country_cities(this.value, 'city', {'CI_ACTIVE'|constant:'LocationModel'});">
			{foreach from=$listCountry key=country_id item=country_data name=foreach_country}
			<option value="{$country_id}" {checked_selected type="option" arg1=$country_id arg2=$user->getCustomField('country')}>{$country_data.name}</option>
			{/foreach}
		</select>
		<select name="city" id="city" class="mrl_small">
			{foreach from=$listCity key=city_id item=city_data name=foreach_city}
			<option value="{$city_id}" {checked_selected type="option" arg1=$city_id arg2=$user->getCustomField('city')} class="{if $city_data.is_capital}bold underline{elseif $city_data.is_main}bold{/if}">{$city_data.name}</option>
			{/foreach}
		</select>
		<span class="mrl_small">
			<label class="m_mrl_tiny">
				<input type="checkbox" name="hide_location" id="hide_location" value="1" class="vmid" {checked_selected type="checkbox" arg1=$user->getCustomField('hide_location') arg2=true} />
				не показывать страну/город
			</label>
		</span>
	</td>
</tr>

<tr>
	<td class="hright">&nbsp;</td>
	<td class="hleft">
		<span class="gray">Нет ваших страны и/или города? <a href="{href target='support_feedback'}" class="tech_dashed" target="_blank" title="">Пишите нам</a></span>
	</td>
</tr>

<tr>
	<td class="vtop hright">
		Аватар <span class="white mrl_small">*</span>
	</td>
	<td class="vtop hleft">
		{if $IS_EDIT}
			<div class="fl mrr">
				{userpic_img var=$user u_format='FORMAT_75'|constant:'UserpicModel'}
			</div>
		{/if}
		<div class="fl">
			<input type="hidden" name="MAX_FILE_SIZE" value="{$MAX_FILE_SIZE_BYTES}" />
			<input type="file" name="userpic" id="userpic" size="32" value="" />
			<div class="clear_small"></div>
			<div class="gray">Максимальный размер загружаемого изображение - {$MAX_FILE_SIZE_MBYTES}</div>
			{if $IS_EDIT && $user->getCustomField('userpic_tstamp')}
			<label class="m_mrl_tiny">
				<input type="checkbox" name="userpic_del" id="userpic_del" value="1" class="vmid" {checked_selected type="checkbox" arg1=$user->getCustomField('userpic_del') arg2=true} />
				удалить
			</label>
			{literal}
			<script type="text/javascript">
				__$('userpic_del').bind('click', function(){
					if($(this).attr('checked')) {
						__$('userpic').attr('disabled', true);
					}
					else {
						__$('userpic').removeAttr('disabled');
					}
				});
			</script>
			{/literal}
			{/if}
		</div>
		<div class="clear2"></div>
	</td>
</tr>

<tr rel="extra">
	{assign var="user_occupation" value=$user->getCustomField('occupation')}
	<td class="vtop hright">
		Специализация <span class="white mrl_small">*</span>
	</td>
	<td class="hleft">
		{foreach from=$listOccupation key=oc_id item=oc_data name=foreach_occupation}
			{if isset($user_occupation[$oc_id])}
				{assign var="user_occupation_set" value=$user_occupation[$oc_id]}
			{else}
				{assign var="user_occupation_set" value=false}
			{/if}
			{if isset($listOccupationExperience[$oc_id])}
				{assign var="user_occupation_experience" value=$listOccupationExperience[$oc_id]|count}
			{else}
				{assign var="user_occupation_experience" value=0}
			{/if}
			<label class="m_mrl_tiny">
				<input type="checkbox" name="occupation[{$oc_id}]" id="oc_{$oc_id}" rel="ex_{$oc_id}" value="{$oc_id}" class="vmid" {checked_selected type="checkbox" use="array_keys" arg1=$user->getCustomField('occupation') arg2=$oc_id} onclick="javascript:js_occ.toggle(this);"/>
				{$oc_data.name} {if $user_occupation_experience}<a href="javascript://" class="mrl_tiny gray" title="">&#8230;</a>{/if}
			</label>
			<span id="er_{$oc_id}" class="mrl_big red hidden">(Максимум {'OCCUPATION_EXPERIENCE_LIMIT'|constant:'OccupationModel'} позиций)</span>
			{if $user_occupation_experience}
				<div id="ex_{$oc_id}" {if !$user_occupation_set}class="hidden"{/if}>
					<div class="clear_small"></div>
					{counter start=1 skip=1 assign="foreach_loop" print=false}
					{foreach from=$listExperience key=ex_id item=ex_data name=foreach_experience}
						{if in_array($ex_id, $listOccupationExperience[$oc_id])}
							<div class="fl mrl_big" style="width:190px; border:0px solid red">
								<label class="m_mrl_tiny">
									<input type="checkbox" name="occupation[{$oc_id}][]" id="oc_{$oc_id}_{$ex_id}" value="{$ex_id}" class="vmid" {checked_selected type="checkbox" arg1=$user_occupation_set arg2=$ex_id} onclick="javascript:js_occ.check_limit('ex_{$oc_id}', 'er_{$oc_id}');"/>
									{$ex_data.name}
								</label>
							</div>
							{if !$foreach_loop%3}
								<div class="clear_small"></div>
							{/if}
							{counter}
						{/if}
					{/foreach}
				</div>
			{/if}
			<div class="clear_small"></div>
		{/foreach}

		{literal}
		<script type="text/javascript">
			window.js_occ.cbx_limit = {/literal}{'OCCUPATION_EXPERIENCE_LIMIT'|constant:'OccupationModel'}{literal};
		</script>
		{/literal}
	</td>
</tr>

<tr rel="extra">
	<td class="vtop hright pdt_small">
		Публичный email <span class="white mrl_small">*</span>
	</td>
	<td class="hleft" id="extra_emails">
	{foreach from=$user->getCustomField('about_emails') key=index item=email name=foreach_email}
		{if !$smarty.foreach.foreach_email.first}<div class="clear_medium"></div>{/if}
		<input type="text" name="about_emails[]" class="input_text" maxlength="{$Validator->emailMaxLength}" value="{$email|escape:'html'}" autocomplete="off" />
		{if $smarty.foreach.foreach_email.last && $smarty.foreach.foreach_email.iteration<$Validator->maxEmailCount}
		<a href="javascript://" onclick="return addExtraEmail($(this).remove());" class="button btn_cons mrl_small gray" title="еще"> + </a>
		{/if}
	{foreachelse}
		<input type="text" name="about_emails[]" class="input_text" maxlength="{$Validator->emailMaxLength}" value="{$email|escape:'html'}" autocomplete="off" />
		<a href="javascript://" onclick="return addExtraEmail($(this).remove());" class="button btn_cons mrl_small gray" title="еще"> + </a>
	{/foreach}
	</td>
	{literal}
	<script type="text/javascript">
	var iEmailCount = __$(':input[type=text]', 'extra_emails').size();
	var maxEmailCount = {/literal}{$Validator->maxEmailCount}{literal};
	function addExtraEmail() {
		if(iEmailCount<maxEmailCount) {
			iEmailCount++;
			var more = '<div class="clear_medium"></div>';
			more += '<input type="text" name="about_emails[]" class="input_text" maxlength="'+{/literal}{$Validator->emailMaxLength}{literal}+'" value="" autocomplete="off" />';
			if(iEmailCount<maxEmailCount) {
				more += '<a href="javascript://" onclick="return addExtraEmail($(this).remove());" class="button btn_cons mrl_small gray" title="еще"> + </a>';
			}
			__$(':input[type=text]', __$('extra_emails').append(more)).focus();
		}
		return false;
	}
	</script>
	{/literal}
</tr>

<tr rel="extra">
	<td class="vtop hright pdt_small">
		Телефон <span class="white mrl_small">*</span>
		<div class="gray">Формат: +380XX1234567</div>
	</td>
	<td class="hleft" id="extra_phones">
	{capture assign="phoneFormat"}<div class="gray">Формат: +380XX1234567</div>{/capture}
	{foreach from=$user->getCustomField('about_phones') key=index item=phone name=foreach_phone}
		{if !$smarty.foreach.foreach_phone.first}<div class="clear_medium"></div>{/if}
		<input type="text" name="about_phones[]" class="input_text" maxlength="{$Validator->phoneMaxLength}" value="{$phone|escape:'html'}" autocomplete="off" />
		{if $smarty.foreach.foreach_phone.last && $smarty.foreach.foreach_phone.iteration<$Validator->maxPhoneCount}
		<a href="javascript://" onclick="return addExtraPhone($(this).remove());" class="button btn_cons mrl_small gray" title="еще"> + </a>
		{/if}
	{foreachelse}
		<input type="text" name="about_phones[]" class="input_text" maxlength="{$Validator->phoneMaxLength}" value="" autocomplete="off" />
		<a href="javascript://" onclick="return addExtraPhone($(this).remove());" class="button btn_cons mrl_small gray" title="еще"> + </a>
	{/foreach}
	</td>
	{literal}
	<script type="text/javascript">
	var iPhoneCount = __$(':input[type=text]', 'extra_phones').size();
	var maxPhoneCount = {/literal}{$Validator->maxPhoneCount}{literal};
	function addExtraPhone() {
		if(iPhoneCount<maxPhoneCount) {
			iPhoneCount++;
			var more = '<div class="clear_medium"></div>';
			more += '<input type="text" name="about_phones[]" class="input_text" maxlength="'+{/literal}{$Validator->phoneMaxLength}{literal}+'" value="" autocomplete="off" />';
			if(iPhoneCount<maxPhoneCount) {
				more += '<a href="javascript://" onclick="return addExtraPhone($(this).remove());" class="button btn_cons mrl_small gray" title="еще"> + </a>';
			}
			__$(':input[type=text]', __$('extra_phones').append(more)).focus();
		}
		return false;
	}
	</script>
	{/literal}
</tr>

<tr rel="extra">
	<td class="vtop hright pdt_small">
		URL <span class="white mrl_small">*</span>
	</td>
	<td class="hleft" id="extra_urls">
	{foreach from=$user->getCustomField('about_urls') key=index item=url name=foreach_url}
		{if !$smarty.foreach.foreach_url.first}<div class="clear_medium"></div>{/if}
		<input type="text" name="about_urls[]" value="{$url|escape:'html'}" class="input_text" maxlength="{$Validator->urlMaxLength}" autocomplete="off" />
		{if $smarty.foreach.foreach_url.last && $smarty.foreach.foreach_url.iteration<$Validator->maxUrlCount}
		<a href="javascript://" onclick="return addExtraUrl($(this).remove());" class="button btn_cons mrl_small gray" title="еще"> + </a>
		{/if}
	{foreachelse}
		<input type="text" name="about_urls[]" value="" class="input_text" maxlength="{$Validator->urlMaxLength}" autocomplete="off" />
		<a href="javascript://" onclick="return addExtraUrl($(this).remove());" class="button btn_cons mrl_small gray" title="еще"> + </a>
	{/foreach}
	</td>
	{literal}
	<script type="text/javascript">
	var iUrlCount = __$(':input[type=text]', 'extra_urls').size();
	var maxUrlCount = {/literal}{$Validator->maxUrlCount}{literal};
	function addExtraUrl() {
		if(iUrlCount<maxUrlCount) {
			iUrlCount++;
			var more = '<div class="clear_medium"></div>';
			more += '<input type="text" name="about_urls[]" value="" class="input_text" maxlength="'+{/literal}{$Validator->urlMaxLength}{literal}+'" autocomplete="off" />';
			if(iUrlCount<maxUrlCount) {
				more += '<a href="javascript://" onclick="return addExtraUrl($(this).remove());" class="button btn_cons mrl_small gray" title="еще"> + </a>';
			}
			__$(':input[type=text]', __$('extra_urls').append(more)).focus();
		}
		return false;
	}
	</script>
	{/literal}
</tr>

<tr rel="extra">
	<td class="vtop hright pdt_small">
		Instant Messaging <span class="white mrl_small">*</span>
	</td>
	<td class="hleft" id="extra_ims">
	{capture assign="im_selectbox"}
	<select name="%s[%u]" class="mrr_tiny">
		{foreach from=$imConstList key=index item=im_id_const name=foreach_im_select}
			{if $smarty.foreach.foreach_im_select.first}<option value="0">--Не выбрано--</option>{/if}
			<option value="{$im_id_const}">{$im_id_const|im}</option>
		{/foreach}
	</select>
	{/capture}
	<div id="im_selectbox_contents" class="hidden">{$im_selectbox}</div>
	{foreach from=$user->getCustomField('about_ims') key=index item=im_data name=foreach_im}
		{foreach from=$im_data key=im_id item=im}
			{if !$smarty.foreach.foreach_im.first}<div class="clear_medium"></div>{/if}
			<select name="im_value_key[{$smarty.foreach.foreach_im.iteration}]" class="mrr_tiny">
			{foreach from=$imConstList key=index item=im_id_const name=foreach_im_select}
				{if $smarty.foreach.foreach_im_select.first}<option value="0">--Не выбрано--</option>{/if}
				<option value="{$im_id_const}" {checked_selected type="option" arg1=$im_id arg2=$im_id_const}>{$im_id_const|im}</option>
			{/foreach}
			</select>
			<input type="text" name="im_value_item[{$smarty.foreach.foreach_im.iteration}]" class="input_text" style="width:140px;" maxlength="50" value="{$im|escape:'html'}" autocomplete="off" />
			{if $smarty.foreach.foreach_im.last && $smarty.foreach.foreach_im.iteration<$Validator->maxIMCount}
			<a href="javascript://" onclick="return addExtraIM($(this).remove());" class="button btn_cons mrl_small gray" title="еще"> + </a>
			{/if}
		{/foreach}
	{foreachelse}
		{$im_selectbox|sprintf:'im_value_key':1}
		<input type="text" name="im_value_item[1]" class="input_text" style="width:140px;" maxlength="50" value="" autocomplete="off" />
		<a href="javascript://" onclick="return addExtraIM($(this).remove());" class="button btn_cons mrl_small gray" title="еще"> + </a>
	{/foreach}
	</td>
	{literal}
	<script type="text/javascript">
	var iIMCount = __$(':input[type=text]', 'extra_ims').size();
	var maxIMCount = {/literal}{$Validator->maxIMCount}{literal};
	function addExtraIM() {
		if(iIMCount<maxIMCount) {
			iIMCount++;
			var more = '<div class="clear_medium"></div>';
			more += $.sprintf(__$('im_selectbox_contents').html(), 'im_value_key', iIMCount);
			more += '<input type="text" name="im_value_item['+iIMCount+']" class="input_text" style="width:140px" maxlength="50" value="" autocomplete="off" />';
			if(iIMCount<maxIMCount) {
				more += '<a href="javascript://" onclick="return addExtraIM($(this).remove());" class="button btn_cons mrl_small gray" title="еще"> + </a>';
			}
			__$(':input[type=text]', __$('extra_ims').append(more)).focus();
		}
		return false;
	}
	</script>
	{/literal}
</tr>

<tr rel="extra">
	<td class="vtop hright">
		О себе <span class="white mrl_small">*</span>
	</td>
	<td class="hleft">
		<textarea name="about" id="about" cols="" rows="5" class="input_text" style="width:98%" maxlength="{$Validator->aboutMaxLength}">{$user->getCustomField('about')|escape:'html'}</textarea>
		<div class="clear_small"></div>
		<div id="ctrl_context" class="fr mrr">
			<div class="gray">Символов: <span id="ctrl_about">{$Validator->aboutMaxLength}</span></div>
		</div>
		{literal}
		<script type="text/javascript">
		(function(){
			__$('about').unbind().bind('keyup', function(){return js_util.controlLength(__$(this), __$('ctrl_about', __$('ctrl_context').show()));});
		})();
		</script>
		{/literal}
	</td>
</tr>

{if $IS_REGISTER}
{if !$user->getCustomField('about_emails') && !$user->getCustomField('about_phones') && !$user->getCustomField('about_urls') && !$user->getCustomField('about_ims')}
<tr rel="extra_ctrl" class="hidden">
	<td class="vtop hright">&nbsp;</td>
	<td class="hleft pdb"><a id="extra_ctrl_a" href="javascript://" title="Дополнительно" class="tech_dashed bold">Дополнительно &darr;</a></td>
	{literal}
	<script type="text/javascript">
	(function(){
		var extraOptions = {
			show : function() {
				__$('tr[rel=extra]').show();
				__$('tr[rel=extra_ctrl]').hide();
				return false;
			},
			hide : function() {
				__$('tr[rel=extra]').hide();
				__$('tr[rel=extra_ctrl]').show();
				return false;
			}
		};
		extraOptions.hide();
		__$('extra_ctrl_a').unbind().bind('click', extraOptions.show);
	})();
	</script>
	{/literal}
</tr>
{/if}
{if isset($captcha)}
<tr>
	<td class="vtop hright">
		<div>Защитный код <span class="red mrl_small">*</span></div>
		<div class="gray">({if $captcha.caseInsensitive}регистронезависимый{else}регистрозависимый{/if})</div>
	</td>
	<td class="hleft">
		<a href="{$smarty.server.PHP_SELF}" id="captcha_a" title="Обновить" onclick="this.blur();return window.js_captcha.imgsrc_reload();"><img src="{$captcha.imgsrc}" id="captcha_img" width="{$captcha.width}" height="{$captcha.height}"  border="0" alt="" title="" /></a>
		<div class="clear"></div>
		<input type="text" name="captcha" id="captcha" class="input_text" style="width:190px" maxlength="{$captcha.chars}" value="" autocomplete="off" />
	</td>
</tr>
{/if}
{/if}


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
		{if $IS_REGISTER}
			<input type="submit" name="" class="button" value="Зарегистрироваться" style="width:300px" />
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
					window.location.href = {/literal}"{href target='user' p1=$USER->getId() var=$USER}"{literal};
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

</form>