{include file="admin/header.tpl"}

{if $Error->isError()}
	{errorbox error=$Error}
	<div class="clear_medium"></div>
{/if}

{capture assign="form_country"}

<h3 class="fl">Страны</h3>
<div class="fr mrl_big">
	<a href="{href target='admin_location_country' p1='NULL_COUNTRY'|constant:'LocationModel'}">Добавить страну</a>
</div>

<div class="clear_medium"></div>

Все страны

<form name="form_country_select" action="{href target='admin_location_country' p1=$country_data.id|default:'NULL_COUNTRY'|constant:'LocationModel'}" method="get" accept-charset="utf-8">

	<select name="country_id" onchange="javascript:document.forms.form_country_select.submit();">
		{foreach from=$country_arr key=co_id item=co_data name=foreach_country}
		<option value="{$co_id}" {checked_selected type="option" arg1=$co_id arg2=$country_data.id|default:'NULL_COUNTRY'|constant:'LocationModel'}>{$co_data.name|escape:'html'} ({$co_id})</option>
		{/foreach}
	</select>

	<input type="hidden" name="target" value="country" />

	<input type="submit" class="button mrl_small" value="Редактировать" />

</form>

<div class="clear_medium"></div>

Активные страны

<form name="form_country_select_active" action="{href target='admin_location_country' p1=$country_data.id|default:'NULL_COUNTRY'|constant:'LocationModel'}" method="get" accept-charset="utf-8">

	<select name="country_id" onchange="javascript:document.forms.form_country_select_active.submit();">
		{foreach from=$country_active_arr key=co_id item=co_data name=foreach_country_active}
		<option value="{$co_id}" {checked_selected type="option" arg1=$co_id arg2=$country_data.id|default:'NULL_COUNTRY'|constant:'LocationModel'}>{$co_data.name|escape:'html'} ({$co_id})</option>
		{/foreach}
	</select>

	<input type="hidden" name="target" value="country" />

	<input type="submit" class="button mrl_small" value="Редактировать" />

</form>

<div class="clear_medium"></div>
<a href="{href target='admin_location_save_static' p1='country'}">Генерирорвать статику (country)</a>
<a href="{href target='admin_location_name_url' p1='country'}" class="mrl_small">Генерирорвать name_url (country)</a>

{if $country_data || $mode_add_country}

	<div class="clear_medium"></div>

	<div class="box_in_box box_info">

		<form name="form_country" action="{href target='admin_location_country' p1=$country_data.id}" method="post" accept-charset="utf-8">

		<table width="100%" cellpadding="2" cellspacing="3" border="0">
		<tbody>
		<colgroup>
			<col width="20%" />
			<col width="80%" />
		</colgroup>
		<tr>
			<td class="hright">[Country] ID: </td>
			<td width="" class="hleft">
				{$country_data.id|default:'NULL_COUNTRY'|constant:'LocationModel'}
				<input type="hidden" name="id" value="{$country_data.id|default:'NULL_COUNTRY'|constant:'LocationModel'}" />
			</td>
		</tr>

		<tr>
			<td class="hright">RU: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
				<input type="text" name="name_ru" class="input_text" maxlength="255" value="{$country_data.name_ru|escape:'html'}" autocomplete="off" />
			</td>
		</tr>

		<tr>
			<td class="hright">UA: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
				<input type="text" name="name_ua" class="input_text" maxlength="255" value="{$country_data.name_ua|escape:'html'}" autocomplete="off" />
			</td>
		</tr>

		<tr>
			<td class="hright">EN: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
				<input type="text" name="name_en" class="input_text" maxlength="255" value="{$country_data.name_en|escape:'html'}" autocomplete="off" />
			</td>
		</tr>

		<tr>
			<td class="hright">URL: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
			<!--
				<input type="text" name="name_url" class="input_text" maxlength="255" value="{$country_data.name_url|escape:'html'}" autocomplete="off" />
			-->
			{$country_data.name_url|escape:'html'}
			</td>
		</tr>

		<tr>
			<td class="hright">Столица: </td>
			<td class="hleft">
				<select name="capital_id" class="" style="" rel="">
					<option value="">---</option>
					{foreach from=$city_arr key=ci_id item=ci_data name=foreach_country_capital}
					<option value="{$ci_id}" {checked_selected type="option" arg1=$ci_id arg2=$country_data.capital_id}>{$ci_data.name|escape:'html'} ({$ci_id})</option>
					{/foreach}
				</select>
			</td>
		</tr>

		<tr>
			<td class="hright">&nbsp;</td>
			<td class="hleft">
				<label class="m_mrl_tiny">
					<input type="checkbox" name="active" id="country_active" class="vmid" value="1" {checked_selected type="checkbox" arg1=$country_data.active arg2=true} />
					активно
				</label>
			</td>
		</tr>

		<tr>
			<td class="hright">&nbsp;</td>
			<td class="hleft"><hr width="250" /></td>
		</tr>

		<tr>
			<td class="vtop hright">&nbsp;</td>
			<td class="vtop hleft">
				<input type="submit" class="button mrl_small" value="Сохранить" />
				<label class="m_mrl_tiny">
					<input type="checkbox" name="country_del" id="country_del" class="mrl_big vmid" value="1" />
					удалить
				</label>
			</td>
		</tr>

		</tbody>
		</table>

		</form>

	</div>

{/if}

{/capture}
{boxinfo content=$form_country type=2}

<div class="clear_medium"></div>

{capture assign="form_state"}

<h3 class="fl">Области</h3>
<div class="fr mrl_big">
	<a href="{href target='admin_location_state' p1='NULL_STATE'|constant:'LocationModel'}">Добавить область</a>
</div>

<div class="clear_medium"></div>

Все области

<form name="form_state_select" action="{href target='admin_location_state' p1=$state_data.id|default:'NULL_STATE'|constant:'LocationModel'}" method="get" accept-charset="utf-8">

	<select name="state_country_all_id" id="state_country_all_id" onchange="javascript: return window.js_loc.get_country_states(this.value, 'select_state_all', {'S_MIX'|constant:'LocationModel'});">
		{foreach from=$country_arr key=co_id item=co_data name=foreach_country_state}
		<option value="{$co_id}" {checked_selected type="option" arg1=$co_id arg2=$country_data.id|default:'NULL_COUNTRY'|constant:'LocationModel'}>{$co_data.name|escape:'html'} ({$co_id})</option>
		{/foreach}
	</select>

	<select name="state_id" id="select_state_all" onchange="javascript:document.forms.form_state_select.submit();">
		{foreach from=$state_arr key=s_id item=s_data name=foreach_state}
		<option value="{$s_id}" {checked_selected type="option" arg1=$s_id arg2=$state_data.id|default:'NULL_STATE'|constant:'LocationModel'}>{$s_data.name|escape:'html'} ({$s_id})</option>
		{foreachelse}
		<option value="">---</option>
		{/foreach}
	</select>

	<input type="hidden" name="target" value="state" />

	<input type="submit" class="button mrl_small" value="Редактировать" />

</form>

<div class="clear_medium"></div>

Активные области

<form name="form_state_select_active" action="{href target='admin_location_state' p1=$state_data.id|default:'NULL_STATE'|constant:'LocationModel'}" method="get" accept-charset="utf-8">

	<select name="state_country_active_id" id="state_country_active_id" onchange="javascript: return window.js_loc.get_country_states(this.value, 'select_state_active', {'S_ACTIVE'|constant:'LocationModel'});">
		{foreach from=$country_arr key=co_id item=co_data name=foreach_country_state}
		<option value="{$co_id}" {checked_selected type="option" arg1=$co_id arg2=$country_data.id|default:'NULL_COUNTRY'|constant:'LocationModel'}>{$co_data.name|escape:'html'} ({$co_id})</option>
		{/foreach}
	</select>

	<select name="state_id" id="select_state_active" onchange="javascript:document.forms.form_state_select_active.submit();">
		{foreach from=$state_active_arr key=s_id item=s_data name=foreach_state_active}
		<option value="{$s_id}" {checked_selected type="option" arg1=$s_id arg2=$state_data.id|default:'NULL_STATE'|constant:'LocationModel'}>{$s_data.name|escape:'html'} ({$s_id})</option>
		{foreachelse}
		<option value="">---</option>
		{/foreach}
	</select>

	<input type="hidden" name="target" value="state" />

	<input type="submit" class="button mrl_small" value="Редактировать" />

</form>

<div class="clear_medium"></div>
<a href="{href target='admin_location_save_static' p1='state'}">Генерирорвать статику (state)</a>
<a href="{href target='admin_location_name_url' p1='state'}" class="mrl_small">Генерирорвать name_url (state)</a>

{if $state_data || $mode_add_state}

	<div class="clear_medium"></div>

	<div class="box_in_box box_info">

		<form name="form_state" action="{href target='admin_location_state' p1=$state_data.id|default:'NULL_STATE'|constant:'LocationModel'}" method="post" accept-charset="utf-8">

		<table width="100%" cellpadding="2" cellspacing="3" border="0">
		<tbody>
		<colgroup>
			<col width="20%" />
			<col width="80%" />
		</colgroup>
		<tr>
			<td class="hright">[State] ID: </td>
			<td width="" class="hleft">
				{$state_data.id|default:'NULL_STATE'|constant:'LocationModel'}
				<input type="hidden" name="id" value="{$state_data.id|default:'NULL_STATE'|constant:'LocationModel'}" />
			</td>
		</tr>

		<tr>
			<td class="hright">Страна: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
				<select name="country_id" onchange="javascript: return window.js_loc.get_country_cities(this.value, 'select_capital_state_city', {'CI_MIX'|constant:'LocationModel'});">
					{foreach from=$country_arr key=co_id item=co_data name=foreach_country_city_edit}
					<option value="{$co_id}" {checked_selected type="option" arg1=$co_id arg2=$state_data.country_id|default:'NULL_COUNTRY'|constant:'LocationModel'}>{$co_data.name|escape:'html'} ({$co_id})</option>
					{/foreach}
				</select>
			</td>
		</tr>

		<tr>
			<td class="hright">RU: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
				<input type="text" name="name_ru" class="input_text" maxlength="255" value="{$state_data.name_ru|escape:'html'}" autocomplete="off" />
			</td>
		</tr>

		<tr>
			<td class="hright">UA: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
				<input type="text" name="name_ua" class="input_text" maxlength="255" value="{$state_data.name_ua|escape:'html'}" autocomplete="off" />
			</td>
		</tr>

		<tr>
			<td class="hright">EN: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
				<input type="text" name="name_en" class="input_text" maxlength="255" value="{$state_data.name_en|escape:'html'}" autocomplete="off" />
			</td>
		</tr>

		<tr>
			<td class="hright">URL: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
			<!--
				<input type="text" name="name_url" class="input_text" maxlength="255" value="{$state_data.name_url|escape:'html'}" autocomplete="off" />
			-->
			{$state_data.name_url|escape:'html'}
			</td>
		</tr>

		<tr>
			<td class="hright">Столица: </td>
			<td class="hleft">
				<select name="capital_id" id="select_capital_state_city" class="" style="" rel="">
					<option value="">---</option>
					{foreach from=$city_arr key=ci_id item=ci_data name=foreach_state_capital}
					<option value="{$ci_id}" {checked_selected type="option" arg1=$ci_id arg2=$state_data.capital_id|default:'NULL_CITY'|constant:'LocationModel'}>{$ci_data.name|escape:'html'} ({$ci_id})</option>
					{/foreach}
				</select>
			</td>
		</tr>

		<tr>
			<td class="hright">&nbsp;</td>
			<td class="hleft">
				<label class="m_mrl_tiny">
					<input type="checkbox" name="active" id="state_active" class="vmid" value="1" {checked_selected type="checkbox" arg1=$state_data.active arg2=true} />
					активно
				</label>
			</td>
		</tr>

		<tr>
			<td class="hright">&nbsp;</td>
			<td class="hleft"><hr width="250" /></td>
		</tr>

		<tr>
			<td class="vtop hright">&nbsp;</td>
			<td class="vtop hleft">
				<input type="submit" class="button mrl_small" value="Сохранить" />
				<label class="m_mrl_tiny">
					<input type="checkbox" name="state_del" id="state_del" class="mrl_big vmid" value="1" />
					удалить
				</label>
			</td>
		</tr>

		</tbody>
		</table>

		</form>

	</div>

{/if}

{/capture}
{boxinfo content=$form_state type=2}

<div class="clear_medium"></div>

{capture assign="form_city"}

<h3 class="fl">Города</h3>
<div class="fr mrl_big">
	<a href="{href target='admin_location_city' p1='NULL_CITY'|constant:'LocationModel'}">Добавить город</a>
</div>

<div class="clear_medium"></div>

Все города

<form name="form_all_city_select" action="{href target='admin_location_city' p1=$city_data.id|default:'NULL_CITY'|constant:'LocationModel'}" method="get" accept-charset="utf-8">

	<select name="country_id" id="country" onchange="javascript: return window.js_loc.get_country_cities(this.value, 'select_city_all', {'CI_MIX'|constant:'LocationModel'});">
		{foreach from=$country_arr key=co_id item=co_data name=foreach_country_city}
		<option value="{$co_id}" {checked_selected type="option" arg1=$co_id arg2=$country_data.id|default:'NULL_COUNTRY'|constant:'LocationModel'}>{$co_data.name|escape:'html'} ({$co_id})</option>
		{/foreach}
	</select>

	<select name="city_id" id="select_city_all" onchange="javascript:document.forms.form_all_city_select.submit();">
		{foreach from=$city_arr key=ci_id item=ci_data name=foreach_city_all}
		<option value="{$ci_id}" {checked_selected type="option" arg1=$ci_id arg2=$city_data.id|default:'NULL_CITY'|constant:'LocationModel'} class="{cssclass target='city' data=$ci_data}">{$ci_data.name|escape:'html'} ({$ci_id})</option>
		{foreachelse}
		<option value="">---</option>
		{/foreach}
	</select>

	<input type="hidden" name="target" value="city" />

	<input type="submit" class="button mrl_small" value="Редактировать" />

</form>

<div class="clear_medium"></div>

Активные города

<form name="form_active_city_select" action="{href target='admin_location_city' p1=$city_data.id|default:'NULL_CITY'|constant:'LocationModel'}" method="get" accept-charset="utf-8">

	<select name="country_id" id="country" onchange="javascript: return window.js_loc.get_country_cities(this.value, 'select_city_active', {'CI_ACTIVE'|constant:'LocationModel'});">
		{foreach from=$country_arr key=co_id item=co_data name=foreach_country_city}
		<option value="{$co_id}" {checked_selected type="option" arg1=$co_id arg2=$country_data.id|default:'NULL_COUNTRY'|constant:'LocationModel'}>{$co_data.name|escape:'html'} ({$co_id})</option>
		{/foreach}
	</select>

	<select name="city_id" id="select_city_active" onchange="javascript:document.forms.form_active_city_select.submit();">
		{foreach from=$city_active_arr key=ci_id item=ci_data name=foreach_city_active}
		<option value="{$ci_id}" {checked_selected type="option" arg1=$ci_id arg2=$city_data.id|default:'NULL_CITY'|constant:'LocationModel'} class="{cssclass target='city' data=$ci_data}">{$ci_data.name|escape:'html'} ({$ci_id})</option>
		{foreachelse}
		<option value="">---</option>
		{/foreach}
	</select>

	<input type="hidden" name="target" value="city" />

	<input type="submit" class="button mrl_small" value="Редактировать" />

</form>

<div class="clear_medium"></div>
<a href="{href target='admin_location_save_static' p1='city'}">Генерирорвать статику (city)</a>
<a href="{href target='admin_location_name_url' p1='city'}" class="mrl_small">Генерирорвать name_url (city)</a>

{if $city_data || $mode_add_city}

	<div class="clear_medium"></div>

	<div class="box_in_box box_info">

		<form name="form_city" action="{href target='admin_location_city' p1=$city_data.id}" method="post" accept-charset="utf-8">

		<table width="100%" cellpadding="2" cellspacing="3" border="0">
		<tbody>
		<colgroup>
			<col width="20%" />
			<col width="80%" />
		</colgroup>
		<tr>
			<td class="hright">[City] ID: </td>
			<td class="hleft">
				{$city_data.id|default:'NULL_CITY'|constant:'LocationModel'}
				<input type="hidden" name="id" value="{$city_data.id|default:'NULL_CITY'|constant:'LocationModel'}" />
			</td>
		</tr>

		<tr>
			<td class="hright">Страна: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
				<select name="country_id" onchange="javascript: return window.js_loc.get_country_states(this.value, 'select_country_state', {'S_MIX'|constant:'LocationModel'});">
					{foreach from=$country_arr key=co_id item=co_data name=foreach_country_city_edit}
					<option value="{$co_id}" {checked_selected type="option" arg1=$co_id arg2=$city_data.country_id}>{$co_data.name|escape:'html'} ({$co_id})</option>
					{/foreach}
				</select>
			</td>
		</tr>

		<tr>
			<td class="hright">Область: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
				<select name="state_id" id="select_country_state" class="" style="" rel="">
					{foreach from=$state_arr key=s_id item=s_data name=foreach_state_edit}
					<option value="{$s_id}" {checked_selected type="option" arg1=$s_id arg2=$city_data.state_id}>{$s_data.name|escape:'html'} ({$s_id})</option>
					{/foreach}
				</select>
			</td>
		</tr>

		<tr>
			<td class="hright">RU: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
				<input type="text" name="name_ru" class="input_text" maxlength="255" value="{$city_data.name_ru|escape:'html'}" autocomplete="off" />
			</td>
		</tr>

		<tr>
			<td class="hright">UA: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
				<input type="text" name="name_ua" class="input_text" maxlength="255" value="{$city_data.name_ua|escape:'html'}" autocomplete="off" />
			</td>
		</tr>

		<tr>
			<td class="hright">EN: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
				<input type="text" name="name_en" class="input_text" maxlength="255" value="{$city_data.name_en|escape:'html'}" autocomplete="off" />
			</td>
		</tr>

		<tr>
			<td class="hright">URL: <span class="red mrl_tiny">*</span></td>
			<td class="hleft">
			<!--
				<input type="text" name="name_url" class="input_text" maxlength="255" value="{$city_data.name_url|escape:'html'}" autocomplete="off" />
			-->
			{$city_data.name_url|escape:'html'}
			</td>
		</tr>

		<tr>
			<td class="hright">&nbsp;</td>
			<td class="hleft">
				<label class="m_mrl_tiny">
					<input type="checkbox" name="is_main" id="is_main" class="vmid" value="1" {checked_selected type="checkbox" arg1=$city_data.is_main arg2=true} />
					обл.центр
				</label>
			</td>
		</tr>

		<tr>
			<td class="hright">&nbsp;</td>
			<td class="hleft">
				<label class="m_mrl_tiny">
					<input type="checkbox" name="is_capital" id="is_capital" class="vmid" value="1" {checked_selected type="checkbox" arg1=$city_data.is_capital arg2=true} />
					столица
				</label>
			</td>
		</tr>

		<tr>
			<td class="hright">&nbsp;</td>
			<td class="hleft">
				<label class="m_mrl_tiny">
					<input type="checkbox" name="active" id="city_active" class="vmid" value="1" {checked_selected type="checkbox" arg1=$city_data.active arg2=true} />
					активный
				</label>
			</td>
		</tr>

		<tr>
			<td class="hright">&nbsp;</td>
			<td class="hleft"><hr width="250" /></td>
		</tr>

		<tr>
			<td class="vtop hright">&nbsp;</td>
			<td class="vtop hleft">
				<input type="submit" class="button" value="Сохранить" />
				<label class="m_mrl_tiny">
					<input type="checkbox" name="city_del" id="city_del" class="mrl_big vmid" value="1" />
					удалить
				</label>
			</td>
		</tr>

		</tbody>
		</table>

		</form>

	</div>

{/if}

{/capture}
{boxinfo content=$form_city type=2}

{include file="admin/footer.tpl"}