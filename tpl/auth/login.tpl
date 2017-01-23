<div class="clear_big"></div>

<form name="form_login" action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">

{assign var="style_width" value="width:290px;"}

<table width="100%" cellspacing="3" cellpadding="5" border="0" class="small">
<tbody>
	<colgroup>
		<col width="30%" />
		<col width="70%" />
	</colgroup>
	<tr>
		<td class="hright">&nbsp;</td>
		<td class="hleft pdl_small"><h1>Вход на сайт</h1></td>
	</tr>

	{if $Error->isError()}
	<tr>
		<td class="hright">&nbsp;</td>
		<td class="hleft pdl_small">{errorbox error=$Error width=300px}</td>
	</tr>
	{/if}

	<tr>
		<td class="hright">
			E-mail <span class="red mrl_small">*</span>
		</td>
		<td class="hleft pdl_small">
			<input type="text" name="email" id="email" class="input_text" style="{$style_width}" tabindex="1" maxlength="{$Validator->emailMaxLength}" value="{$auth.email|escape:'html'}" />
		</td>
	</tr>

	<tr>
		<td class="hright">
			Пароль <span class="red mrl_small">*</span>
		</td>
		<td class="hleft pdl_small">
			<input type="password" name="password" id="password" class="input_text" style="{$style_width}" tabindex="2" maxlength="{$Validator->passwordMaxLength}" />
		</td>
	</tr>

	{if isset($captcha_ini)}
	<tr>
		<td class="vtop hright">
			<div>Защитный код <span class="red mrl_small">*</span></div>
			<div class="gray">({if $captcha_ini.caseInsensitive}регистронезависимый{else}регистрозависимый{/if})</div>
		</td>
		<td class="hleft pdl_small">
			<a href="{$smarty.server.PHP_SELF}" id="captcha_a" title="Обновить" onclick="this.blur(); return window.js_captcha.imgsrc_reload();"><img src="{$captcha_ini.imgsrc}" id="captcha_img" width="{$captcha_ini.width}" height="{$captcha_ini.height}" class="" border="0" alt="" title="" /></a>
			<div class="clear"></div>
			<input type="text" name="captcha" id="captcha" class="input_text" style="{$style_width}" tabindex="3" maxlength="{$captcha_ini.chars}" />
		</td>
	</tr>
	{/if}

	<tr>
		<td class="hright">&nbsp;</td>
		<td class="hleft pdl_small">
			<label class="m_mrl_tiny">
				<input type="checkbox" name="remember" id="remember" class="vmid" value="1" tabindex="4" {checked_selected type="checkbox" arg1=$auth.remember arg2=true} />
				запомнить меня на 2 недели
			</label>
		</td>
	</tr>

	<tr>
		<td class="hright">&nbsp;</td>
		<td class="hleft pdl_small">
			<a href="{href target='user_register'}">Зарегистрироваться</a>
			<span class="gray mrl_small mrr_small">|</span>
			<a href="{href target='auth_remind'}" title="Забыли пароль?" rel="nofollow">Забыли пароль?</a>
		</td>
	</tr>

	<tr>
		<td class="hright">&nbsp;</td>
		<td class="hleft pdl_small">
			<hr style="{$style_width}" align="left" />
		</td>
	</tr>

	<tr>
		<td class="hright">&nbsp;</td>
		<td class="hleft pdl_small">
			<input type="submit" class="button" style="{$style_width}" tabindex="5" value="Войти" />
		</td>
	</tr>

</tbody>
</table>

<input type="hidden" name="url" value="{$auth.url}" />

</form>

{literal}<script type="text/javascript">(function(){$.trim(__$('email').val()) ? __$('password').focus() : __$('email').focus();})();</script>{/literal}

<div class="clear_big"></div>
<div class="clear_big"></div>