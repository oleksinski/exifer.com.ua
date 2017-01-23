</div>

<div class="clear"></div>

<div class="footer">
	<div class="footer-content">
		<div class="footer-about">
			Copyright &copy; 2010-{$smarty.now|date_format:"%Y"} <a href="{href target='homepage'}" title="{$URL_NAME}"><b>{$URL_NAME}</b></a>
			<div class="clear_medium"></div>
			Все фотографии являются собственностью их авторов.
			{if $ENV.PRO && !$HtmlRobotsDisallow}
				<div class="clear_big"></div>
				{include file="layout/counter.tpl"}
			{/if}
		</div>
		<div class="footer-services">
			<dl>
				<dt>Профайл</dt>
				<dd><a href="{href target='auth_login'}" title="Вход" rel="nofollow">Вход</a></dd>
				<dd><a href="{href target='user_register'}" title="Регистрация" rel="nofollow">Регистрация</a></dd>
			</dl>
			<dl>
				<dt>Разделы</dt>
				<dd><a href="{href target='photo_lenta'}" title="Фотографии">Фотографии</a></dd>
				<dd><a href="{href target='user_lenta'}" title="Пользователи">Пользователи</a></dd>
				<dd><a href="{href target='comment_lenta'}" title="Комментарии">Комментарии</a></dd>
			</dl>
			<dl>
				<dt>Сервисы</dt>
				<dd><a href="{href target='rss'}" title="RSS 2.0 подписка">RSS подписка {rss_icon}</a></dd>
			</dl>
			<dl>
				<dt>Инфо</dt>
				<dd><a href="{href target='support_about'}" title="О сайте">О сайте</a></dd>
				<dd><a href="{href target='support_rules'}" title="Правила">Правила</a></dd>
				<dd><a href="{href target='support_eula'}" title="Соглашение">Соглашение</a></dd>
			</dl>
			<dl>
				<dt>Служба поддержки</dt>
				<dd><a href="{href target='support_feedback'}" title="Обратная связь">Обратная связь</a></dd>
				<dd><a href="{href target='reformal'}" rel="nofollow" title="Отзывы и идеи">Отзывы и идеи</a></dd>
			</dl>
		</div>
	</div>
</div>