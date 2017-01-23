<form name="form_feedback" action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">

<div class="fl smallcol">

	<div class="clear_big"></div>
	<div class="clear_big"></div>
	<div class="clear_big"></div>

	{capture assign="support_info"}
		<div class="box_in_box box_info ">
			С нами можно связаться:
			<div class="clear_small"></div>
			<ul class="dot small">
				<li>через форму обратной связи</li>
				<li>по адресу {mailto address=$SUPPORT_EMAIL encode='javascript'}</li>
			</ul>
		</div>
	{/capture}
	{boxinfo content=$support_info type=3 scheme=green}

	<div class="clear_medium"></div>

</div>

<div class="fr bigcol">

	<table width="100%" cellspacing="2" cellpadding="4" border="0" class="small">
	<tbody>

	<tr>
		<td width="30%" class="hright">
			&nbsp;
		</td>
		<td width="70%" class="hleft">
			<h2>Обратная связь</h2>
		</td>
	</tr>

	{if $Error->isError()}
	<tr>
		<td class="hright">&nbsp;</td>
		<td class="hleft">{errorbox error=$Error}</td>
	</tr>
	{elseif $DATA_SAVED_FLAG}
	<tr>
		<td class="hright">
			&nbsp;
		</td>
		<td class="hleft">
			{capture assign="messok"}
				Сообщение успешно отправлено
			{/capture}
			{boxinfo content=$messok type=3 scheme='green'}
		</td>
	</tr>
	{/if}

	<tr>
		<td class="vtop hright">
			Имя <span class="red mrl_small">*</span>
		</td>
		<td class="hleft">
			{if $USER->exists()}
				{$USER->getExtraField('name')}
			{else}
				<input type="text" name="username" id="username" class="input_text" maxlength="{$Validator->nameMaxLength}" value="{$feedback.username|escape:'html'}" autocomplete="off" />
			{/if}
		</td>
	</tr>

	<tr>
		<td class="vtop hright">
			E-mail <span class="red mrl_small">*</span>
		</td>
		<td class="hleft">
		{if $USER->exists()}
			{$USER->getField('email')|escape:'html'}
		{else}
			<input type="text" name="email" id="email" class="input_text" maxlength="{$Validator->emailMaxLength}" value="{$feedback.email|escape:'html'}" autocomplete="off" />
		{/if}
		</td>
	</tr>

	<tr>
		<td class="vmid hright">
			Тема <span class="red mrl_small">*</span>
		</td>
		<td class="hleft">
			<input type="text" name="subject" id="subject" class="input_text" style="width:500px" maxlength="255" autocomplete="off" />
		</td>
	</tr>

	<tr>
		<td class="vtop hright">
			Сообщение <span class="red mrl_small">*</span>
		</td>
		<td class="hleft">
			<textarea cols="" rows="" name="message" id="message" class="input_text" style="width:500px; height:150px" maxlength="{$Validator->feedbackMaxLength}" autocomplete="off">{$feedback.message|escape:'html'}</textarea>
		</td>
	</tr>

	{if !$USER->exists()}
	<tr>
		<td class="vtop hright">
			<div>Защитный код <span class="red mrl_small">*</span></div>
			<div class="gray">({if $captcha_ini.caseInsensitive}регистронезависимый{else}регистрозависимый{/if})</div>
		</td>
		<td class="hleft">
			<a href="{$smarty.server.PHP_SELF}" id="captcha_a" title="Обновить" onclick="this.blur(); return window.js_captcha.imgsrc_reload();"><img src="{$captcha_ini.imgsrc}" id="captcha_img" width="{$captcha_ini.width}" height="{$captcha_ini.height}" border="0" /></a>
			<div class="clear"></div>
			<input type="text" name="captcha" id="captcha" class="input_text" style="width:190px" maxlength="{$captcha_ini.chars}" autocomplete="off" />
		</td>
	</tr>
	{/if}

	<tr>
		<td class="hright">
			&nbsp;
		</td>
		<td class="hleft">
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
			<input type="submit" name="" id="" class="button" value="Отправить" />
		</td>
	</tr>

	</tbody>
	</table>

</div>

</form>
