<form name="form_remove" action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">

<div class="fl" style="width:250px">

	{capture assign="i_preview"}

		<div class="box_in_box box_info" style="background-color:rgb({$photo->getField('rgb')})">
			<center id="p_preview">
				<a href="{href target='photo' p1=$photo->getId() var=$photo}" title="{$photo->getExtraField('name')}">{photo_img var=$photo p_format='THUMB_150'|constant:'ThumbModel' alt=$photo->getExtraField('name')}</a>
			</center>
		</div>

	{/capture}

	{boxinfo content=$i_preview type=2}

</div>

<div class="fr" style="width:700px">

	<table width="100%" cellspacing="2" cellpadding="4" border="0" class="small">
	<tbody>
	<colgroup>
		<col width="10%">
		<col width="90%">
	</colgroup>
	<tr>
		<td class="hright">
			&nbsp;
		</td>
		<td class="hleft">
			<h2>Удаление фотографии</h2>
			<div class="clear_big"></div>
			<input type="submit" name="" id="" class="button" value="Удалить" />
		</td>
	</tr>

	</tbody>
	</table>

</div>

</form>
