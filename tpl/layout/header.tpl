<div class="clear_small"></div>

<div class="fr mrr">
{if $USER->exists()}
	{if $USER->isModerator()}
		<a href="{href target='admin_index'}" class="red" title="Админка">Админка</a>
		<span class="gray mrl_tiny mrr_tiny gray">|</span>
	{/if}
	<a href="{href target='user' p1=$USER->getId() var=$USER}" title=""><b>{$USER->getExtraField('name')}</b></a>
	<span class="gray mrl_tiny mrr_tiny gray">|</span>
	<a href="{href target='user_edit'}" title="редактировать" class="small"><img src="{$S_URL}img/gear.png" class="vmid" width="16" height="16" alt="редактировать" /></a>
	<span class="mrl_tiny mrr_tiny gray">|</span>
	<a href="{href target='auth_logout'}" title="">Выйти</a>
{else}
	<a href="{href target='auth_login'}" title="Войти" rel="nofollow"><b>Войти</b></a>
	<span class="mrl_tiny mrr_tiny gray">|</span>
	<a href="{href target='user_register'}" title="">Зарегистрироваться</a>
{/if}
</div>

<div class="clear_small"></div>

<div class="header">

	<div class="page">

		<div class="fl" style="width:142px">
			<a href="{href target='homepage'}" title="{$PROJECT_NAME}" onclick="javascript:this.blur()">
				<img src="{$S_URL}img/logo/logo_header.gif" width="142" height="66" alt="{$PROJECT_NAME}" />
			</a>
		</div>

		<div class="fr" style="width:838px; padding-top:21px;">

			<div class="fl mrl">
				<a href="{href target='photo_lenta'}" class="h_menu" title="Фотографии">
					<div class="fl m_left">&nbsp;</div>
					<div class="fl m_item"><b>Фотографии</b></div>
				</a>
				<div class="clear2"></div>
			</div>

			<div class="fl mrl">
				<a href="{href target='user_lenta'}" class="h_menu" title="Пользователи">
					<div class="fl m_left">&nbsp;</div>
					<div class="fl m_item"><b>Пользователи</b></div>
				</a>
				<div class="clear2"></div>
			</div>

			<div class="fl mrl">
				<a href="{href target='comment_lenta'}" class="h_menu" title="Комментарии">
					<div class="fl m_left">&nbsp;</div>
					<div class="fl m_item"><b>Комментарии</b></div>
				</a>
				<div class="clear2"></div>
			</div>

			<div class="fr mrr">
				<a href="{href target='photo_upload'}" class="h_menu" title="Загрузка фото" rel="nofollow">
					<div class="fl m_left">&nbsp;</div>
					<div class="fl m_item"><b>Загрузка фото</b></div>
				</a>
				<div class="clear2"></div>
			</div>

			{if 0 && $USER->exists()}
			<div class="fr mrr">
				<a href="{href target='user' p1=$USER->getId() var=$USER}" class="h_menu" title="Мой профайл" rel="nofollow">
					<div class="fl m_left">&nbsp;</div>
					<div class="fl m_item"><b>Мой профайл</b></div>
				</a>
				<div class="clear2"></div>
			</div>
			{/if}

			<div class="fr mrr">
				<form name="q_form" id="q_form" method="get" action="{href target=search}" accept-charset="utf-8">
					<input type="text" name="q" id="q" value="{$smarty.get.q|default:''|escape:'html'}" style="width:130px" />
					<select name="q_what" class="select select_cons mrl_tiny" onchange="if(document.forms['q_form'].q.value.length) document.forms['q_form'].submit()">
						{assign var="q_what" value=$q_what|default:'photo'}
						<option value="photo" {checked_selected type="option" arg1=$q_what arg2='photo'}>Фотографиям</option>
						<option value="profile" {checked_selected type="option" arg1=$q_what arg2='profile'}>Пользователям</option>
						<option value="comment" {checked_selected type="option" arg1=$q_what arg2='comment'}>Комментариям</option>
					</select>
					<input type="submit" name="q_submit" class="button btn_cons mrl" value="Поиск" />
				</form>
				<div class="clear2"></div>
			</div>

		</div>

		<div class="clear"></div>

	</div>

</div>

<div class="clear_medium"></div>

<div class="page">
