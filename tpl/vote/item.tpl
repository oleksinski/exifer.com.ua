<div id="vi_context">
{if $vote_collection->length()}
<div class="pdb pdt" style="overflow:auto; max-height:300px">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody>
<colgroup>
	<col width="75" />
</colgroup>
{foreach from=$vote_collection key=vote_id item=vote name=foreach_votelist}
	{assign var="user" value=$vote->getUserObject()}
	{assign var="user_name" value=$user->getExtraField('name')}
	<tr{if $vote->isMyVote()} bgcolor="lightyellow"{/if}>
		<td class="vmid xlarge hcenter {if $vote->isTypeCons()}red{elseif $vote->isTypePros()}green{else}gray{/if}">
			{$vote->getExtraField('value')}
		</td>
		<td class="vmid hleft pdl">
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tbody>
			<colgroup>
				<col width="100%" />
			</colgroup>
			<td>
				<a href="{href target='user' p1=$user->getId() var=$user}" title="{$user_name}" class="tech_dark">{userpic_img var=$user u_format='FORMAT_75'|constant:'UserpicModel' alt=$user_name class=mrr_small align=left width=30 height=30} {$user_name}</a>&nbsp;{$user|online}
				<div class="gray">{$vote->getField('add_tstamp')|datetime}</div>
			</td>
			{if $vote->isRemovable()}
			<td>
				{capture assign="vote_moderator"}
					<a href="{href target='vote_del' p1=$vote->getId() p2=$vote->getItemType()}" rel="v-{$vote->getItemType()}-del" title="Delete" value="{$vote_id}">Delete</a>
				{/capture}
				{boxinfo content=$vote_moderator type=3 scheme=yellow}
			</td>
			{/if}
			</tbody>
			</table>
		</td>
	</tr>
	{if $smarty.foreach.foreach_votelist.iteration!=$vote_collection->length()}
		<tr><td colspan="2" class="pad5px"><hr class="casper" width="100%" /></td></tr>
	{/if}
{/foreach}
</table>
</div>
{else}
	<div class="pdl pdt_small">Оценок нет</div>
{/if}
</div>