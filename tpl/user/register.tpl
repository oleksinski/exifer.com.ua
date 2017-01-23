<form name="form_register_edit" action="{$smarty.server.PHP_SELF}" method="post" enctype="multipart/form-data" accept-charset="utf-8">

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
		<h1>Регистрация</h1>
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

<tr>
	<td class="hright vtop">
		E-mail <span class="red mrl_small">*</span>
	</td>
	<td class="hleft vtpo">
		<input type="text" name="email" id="email" class="input_text" maxlength="{$Validator->emailMaxLength}" value="{$user->getCustomField('email')|escape:'html'}" autocomplete="off" />
		<div class="gray mrt_tiny">Ваш email не будет отображаться на страницах сайта</div>
	</td>
</tr>

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

<tr>
	<td class="hright">
		&nbsp;
	</td>
	<td class="hleft">
		<hr class="casper" width="300" />
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
		<input type="submit" name="" class="button" value="Зарегистрироваться" style="width:300px" />
	</td>
</tr>

</tbody>
</table>

</form>