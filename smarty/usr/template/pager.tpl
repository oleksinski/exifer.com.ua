{if isset($__pager__) && $__pager__.pageCount>1}
<div id="{$__pager__.containerId}" class="paginator {$__pager__.class}" style="{$__pager__.style}">
	<table width="100%">
	<tr>
	{foreach from=$__pager__.noscript key=page item=href name=pager_pages}
	{if $page==$__pager__.currentPage}
		<td width="{$__pager__.td_width_percent}%"><span><strong>{$page}</strong></span></td>
	{else}
		<td width="{$__pager__.td_width_percent}%"><span><a href="{$href}" title="{$page}" {if $__pager__.onclick}onclick="javascript:return {$__pager__.onclick}({$page});"{/if}>{$page}</a></span></td>
	{/if}
	{/foreach}
	</tr>
	<tr>
		<td colspan="{$__pager__.noscript_count}">
			<div class="scroll_bar">
				<div class="scroll_trough"></div>
			</div>
		</td>
	</tr>
	</table>
	<div class="paginator_pages" style="{$__pager__.style}">
		<span class="left" rel="paginator_ctrl_left">Ctrl&nbsp;&larr;</span>
		<span style="width:98%">&#8721;{$__pager__.pageCount}&nbsp;&nbsp;[{$__pager__.totalCount}]</span>
		<span class="right" rel="paginator_ctrl_right">Ctrl&nbsp;&rarr;</span>
		<br clear="all" />
	</div>
</div>

{literal}
<script type="text/javascript">
(function(){
if(window.Paginator) {
new Paginator("{/literal}{$__pager__.containerId}", {$__pager__.pageCount}, {$__pager__.totalCount}, {$__pager__.onPage}, {$__pager__.currentPage}, {$__pager__.pagePatternUrl|jsify}{literal});
}})();
</script>
{/literal}

{/if}
