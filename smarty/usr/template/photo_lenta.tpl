{if $__photo_collection_length__}

	{math assign="photo_td_width" format="%u%%" equation="x/y" x=100 y=$__photo_per_row__}
	{math assign="truncate_size" format="%u" equation="x*1.25/10" x=$__thumb_format__}

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
	{foreach from=$__photo_collection__ key=photo_id item=photo name=foreach_photo_lenta}

		{assign var="__iteration__" value=$smarty.foreach.foreach_photo_lenta.iteration%$__photo_per_row__}

		{if $__iteration__==1 || $__photo_per_row__==1}
		<tr>
		{/if}

		<td width="{$photo_td_width}" class="{$__td_class__}{if $smarty.foreach.foreach_photo_lenta.iteration>$__photo_per_row__} pdt{/if}"{if $__td_style__} style="{$__td_style__}"{/if}>
		{assign var="photo_name" value=$photo->getExtraField('name')}
		{if $__photo_info__}
			{assign var="user" value=$photo->getUserObject()}
			{assign var="user_name" value=$user->getExtraField('name')}
			<div style="width:{$__thumb_format__}px; margin:0 auto">
				<a href="{href target='photo' p1=$photo_id var=$photo}" title="{$photo_name}">{photo_img var=$photo p_format=$__thumb_format__ alt=$photo_name class=shadow}</a>
				<div class="photo_info11"></div>
				<div class="photo_info12 mrr_tiny mrl_tiny">
					<div class="clear_small2"></div>
					{item_count item=orient var=$photo detailed=false icon=true icon_scheme=white}
					<span class="gray mrr_tiny"> | </span>
					{item_count item=views var=$photo detailed=false icon=true icon_scheme=white}
					<span class="gray mrl_tiny mrr_tiny"> | </span>
					{item_count item=comments var=$photo detailed=false icon=true icon_scheme=white}
					<span class="gray mrl_tiny mrr_tiny"> | </span>
					{item_count item=votes var=$photo detailed=false icon=true icon_scheme=white}
				</div>
				<div class="photo_info21 shadow"></div>
				<div class="photo_info22">
					<div class="pad5px">
						<div class="pdb_tiny large overflow">
							<a href="{href target='photo' p1=$photo_id var=$photo}" title="{$photo_name}">
								{if $photo_name}
									{if $__highlight__}
										{$photo_name|truncate:$truncate_size|highlight:$__highlight__:'highlight1'}
									{else}
										{$photo_name|truncate:$truncate_size}
									{/if}
								{else}
									*****
								{/if}
							</a>
						</div>
						<div class="pdb_tiny gray">{$photo->getField('add_tstamp')|datetime}</div>
						<div class="p_user overflow"><a href="{href target='user' p1=$user->getId() var=$user}" title="{$user_name}">{$user_name}</a> {$user|online:false}</div>
					</div>
				</div>
			</div>
		{else}
			<a href="{href target='photo' p1=$photo_id var=$photo}" title="{$photo_name}">{photo_img var=$photo p_format=$__thumb_format__ alt=$photo_name class=shadow}</a>
		{/if}
		</td>

		{if ($__iteration__==0 && $smarty.foreach.foreach_photo_lenta.index>0) || $__photo_per_row__==1}
		</tr>
		{elseif $smarty.foreach.foreach_photo_lenta.iteration==$__photo_collection_length__}
			<td colspan="{$__photo_per_row__-$__iteration__}">&nbsp;</td>
		</tr>
		{/if}

	{/foreach}
	</tbody>
	</table>
{/if}
