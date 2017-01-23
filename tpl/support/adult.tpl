<h3>Предупреждение</h3>

<p>
	Обращаем Ваше внимание на то, что Вы переходите на страницу, которую авторы или администрация сайта отнесли к категории &laquo;Ню&raquo;.
</p>

<p>
	В зависимости от Вашего возраста или места расположения законы могут ограничивать просмотр Вами страниц данного содержания.
</p>

<p>
	Мы считаем необходимым предупредить Вас о возможном содержании таких страниц и попросить Вас подтвердить Ваше решение.
</p>

<p>
	Если Вы хотите продолжить просмотр, нажмите кнопку &laquo;Согласен&raquo;.
</p>

<p>
	При этом сайт не берет на себя ответственности за законность Ваших действий по просмотру данных страниц.
</p>

<p>
	Если Вы не хотите продолжить просмотр - нажмите кнопку &laquo;Не согласен&raquo;.
</p>

<div class="clear_small"></div>

<form name="support_adult" accept-charset="utf-8">
	<div class="fl mrr_big">
		<input type="submit" name="btn_submit" id="btn_submit" class="button" value="Согласен" />
	</div>
	<div class="fl">
		<input type="button" name="btn_cancel" id="btn_cancel" class="button btn_cons" value="Не согласен" />
	</div>
	<div class="clear"></div>
	<input type="hidden" name="url" value="{$redirect_url|default:null}" />
</form>

{literal}
<script type="text/javascript">
$('btn_cancel').addEvent('click', function(){
	window.location.href = {/literal}"{href target='homepage'}"{literal};
});
</script>
{/literal}
