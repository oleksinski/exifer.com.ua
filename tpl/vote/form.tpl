{assign var="vote" value=$item->getCustomField('vote')}
{if $vote->canVote()}
<div id="va_context">
	{capture assign="capture_vote"}
	<div class="box_in_box box_info">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<colgroup>
				<col width="33%" />
				<col width="33%" />
				<col width="33%" />
			</colgroup>
			<tr>
				<td class="vmid hcenter nowrap">
					<sup class="gray" rel="vc-{$vote->getItemType()}-{$item->getId()}-cons">{$item->getField('votes_cons')}</sup>
					<a href="javascript://" rel="v-add-ctrl" value="{$vote->getTypeCons()}" class="button vote_cons" style="width:25px" title="против"> - </a>
				</td>
				<td class="vmid hcenter nowrap">
					<sup class="gray" rel="vc-{$vote->getItemType()}-{$item->getId()}-zero">{$item->getField('votes_zero')}</sup>
					<a href="javascript://" rel="v-add-ctrl" value="{$vote->getTypeZero()}" class="button vote_zero" style="width:25px" title="воздержусь"> 0 </a>
				</td>
				<td class="vmid hcenter nowrap">
					<a href="javascript://" rel="v-add-ctrl" value="{$vote->getTypePros()}" class="button vote_pros" style="width:25px" title="за"> + </a>
					<sup class="gray" rel="vc-{$vote->getItemType()}-{$item->getId()}-pros">{$item->getField('votes_pros')}</sup>
				</td>
			</tr>
			<tr>
				<td colspan="3" class="vmid hcenter nowrap pdt_small">
					<span class="gray">Оценка:</span>
					<span id="vf_ajaxloader" class="mrl_tiny mrr_tiny"></span>
					<span>{item_count item=votes var=$item type=$vote->getItemType()}</span>
				</td>
			</tr>
		</tbody>
		</table>
	</div>
	<div id="ve_context" class="pdt_small hidden">
		{boxinfo content='<ul></ul>' type=3}
	</div>
	{/capture}
	{boxinfo content=$capture_vote type=2}
	<div class="clear_medium"></div>
</div>
{/if}