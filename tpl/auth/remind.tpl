<form name="form_remind" action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">

<table width="100%" cellspacing="2" cellpadding="4" border="0" class="small">
<tbody>

<tr>
	<td width="30%" class="hright">
		&nbsp;
	</td>
	<td width="70%" class="hleft">
		<h1>Напоминание пароля</h1>
	</td>
</tr>

{if $Error->isError()}
<tr>
	<td class="hright">&nbsp;</td>
	<td class="hleft">{errorbox error=$Error}</td>
</tr>
{elseif $user->getCustomField('reminded')}
<tr>
	<td class="hright">
		&nbsp;
	</td>
	<td class="hleft">
		{capture assign="remind_confirmation"}
			На email <b>{$user->getField('email')|escape:'html'}</b> отправлено письмо с регистрационными данными.
			<div class="clear_small"></div>
			Для <a href="{href target='auth_login_clear'}" title="Вход на сайт">входа</a> на сайт используйте имейл и пароль, указанные при регистрации.
		{/capture}
		{boxinfo content=$remind_confirmation type=3 scheme=green}
	</td>
</tr>
{/if}

<tr>
	<td class="hright">
		&nbsp;
	</td>
	<td class="hleft">
		<div class="gray">
			Пожалуйста, введите адрес электронной почты, указанный во время регистрации.
			<br />
			На него будет отправлено письмо с регистрационными данными.
		</div>
	</td>
</tr>


<tr>
	<td class="hright">
		E-mail <span class="red mrl_small">*</span>
	</td>
	<td class="hleft">
		<input type="text" name="email" id="email" class="input_text" maxlength="{$Validator->emailMaxLength}" value="{$user->getCustomField('email')|escape:'html'}" />
	</td>
</tr>

<tr>
	<td class="hright">
		&nbsp;
	</td>
	<td class="hleft">
		<input type="submit" class="button" value="Напомнить" />
	</td>
</tr>

</tbody>
</table>

<input type="hidden" name="url" value="{$user->getCustomField('url')}" />

</form>