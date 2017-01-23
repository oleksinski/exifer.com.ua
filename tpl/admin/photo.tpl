{include file="admin/header.tpl"}

<div class="mrl">

<h3>Управление фото</h3>

<div class="clear_big"></div>

<table width="1%" cellpadding="2" cellspacing="2" border="0">
<tbody>
<colgroup>
	<col width="20%" />
	<col width="60%" />
	<col width="20%" />
</colgroup>
<tr>
	<form action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">
		<td class="hright">ID:</td>
		<td class="hleft">
			<input type="text" name="id" class="input_text" value="{$smarty.request.id|escape:'html'}" />
		</td>
		<td class="hleft">
			<input type="submit" class="button" value="Search" />
		</td>
	</form>
</tr>

</tbody>
</table>

<div class="clear_big"></div>

{if $photo->exists()}

<div class="box_in_box box_info pdt_tiny">
	<div class="pdt_small small" style="overflow:visible">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
		<colgroup>
			<col width="20%" />
			<col width="80%" />
		</colgroup>

		{assign var="STATUS_LOCK" value='STATUS_LOCK'|constant:'Photo'}
		{assign var="STATUS_OKE" value='STATUS_OKE'|constant:'Photo'}
		{assign var="MODERATED_OFF" value='MODERATED_OFF'|constant:'Photo'}
		{assign var="MODERATED_ON" value='MODERATED_ON'|constant:'Photo'}
		{assign var="BITMASK_HIDE_EXIF" value='BITMASK_HIDE_EXIF'|constant:'Photo'}
		{assign var="BITMASK_ADULT" value='BITMASK_ADULT'|constant:'Photo'}
		{assign var="BITMASK_RECEIVE_COMMENTS" value='BITMASK_RECEIVE_COMMENTS'|constant:'Photo'}

		{assign var="user" value=$photo->getUserObject()}
		{assign var="user_name" value=$user->getExtraField('name')}

		{foreach from=$photo->getFields() key=field item=value}
		<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
			<td class="vmid gray pad5px">{$field}</td>
			<td class="vmid pdl_small pad5px">
				{if $field=='id'}
					<a href="{href target='photo' p1=$photo->getId() var=$photo}" title="">{href target='photo' p1=$photo->getId() var=$photo}</a>
					<a href="{href target='photo' p1=$photo->getId() var=$photo}" title="{$photo->getExtraField('name')}">{photo_img var=$photo p_format='THUMB_75'|constant:'ThumbModel' width=50 height=50 align=right alt=$photo->getExtraField('name')}</a>
				{elseif $field=='user_id'}
					<a href="{href target='user' p1=$user->getId() var=$user}" title="{$user_name}">{userpic_img var=$user u_format='FORMAT_75'|constant:'UserpicModel' alt=$user_name align=right class=mrr_small width=50 height=50} {$user_name}</a> {$user|online}
				{elseif $field=='description'}
					{$value|urlify}
				{elseif $field=='exif'}
				{else}
					{$value}
					{if $value}
						<span class="mrl_small gray">
						{if in_array($field, array('add_tstamp', 'update_tstamp', 'view_tstamp'))}
							({$value|datetime})
						{elseif $field=='rgb'}
							<span stype="background-color:rgb({$value})">||||||||</span>
						{elseif $field=='bitmask'}
							<div>BITMASK_HIDE_EXIF = {if $photo->isBitmaskSet($BITMASK_HIDE_EXIF)}1{else}0{/if}</div>
							<div>BITMASK_ADULT = {if $photo->isBitmaskSet($BITMASK_ADULT)}1{else}0{/if}</div>
							<div>BITMASK_RECEIVE_COMMENTS = {if $photo->isBitmaskSet($BITMASK_RECEIVE_COMMENTS)}1{else}0{/if}</div>
						{/if}
						</span>
					{/if}
				{/if}
			</td>
		</tr>
		{/foreach}
		</tbody>
		</table>

		<div class="pdt">
			<a href="{href target='photo_remove' p1=$photo->getId() var=$photo}">remove</a>
			<span class="gray mrl_small mrr_small"> | </span>
			<a href="{href target='photo_edit' p1=$photo->getId() var=$photo}">edit</a>
		</div>

		<div class="pdt">
			<form action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">
				<input type="hidden" name="id" value="{$photo->getId()}" />
				<input type="submit" name="recalc" value=" Recalc info " class="button small" />
			</form>
		</div>

	</div>
	<div class="clear"></div>
</div>
{elseif $smarty.request.id}
	{boxinfo content='Фото не найдено' type=3}
{/if}

</div>

{include file="admin/footer.tpl"}