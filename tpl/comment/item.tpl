<table border="0" cellpadding="5" cellspacing="0" width="100%">
<tbody>
{foreach from=$comment_collection key=comment_id item=comment name=foreach_comment}
	{assign var="user" value=$comment->getUserObject()}
	{assign var="user_name" value=$user->getExtraField('name')}
	<tr>
		<td width="100%" class="comment-item vmid hleft">
			<a name="{$comment->getExtraField('anchor')}"></a>
			<a title="{$user_name}" href="{href target='user' p1=$user->getId() var=$user}">{userpic_img var=$user u_format='FORMAT_75'|constant:'UserpicModel' alt=$user_name align=left class='mrr mrb' width=50 height=50} {$user_name}</a> {$user|online}
			<span class="small gray mrl">{$comment->getField('add_tstamp')|datetime}</span>
			<div class="fr">
				<a href="{$comment->getExtraField('url')}" class="tech" id="{$comment->getExtraField('anchor')}" rel="c_url" title="">#{$comment->getExtraField('anchor')}</a>
			</div>
			<div class="comment-body mrt_tiny">
				{$comment|comment_rich}
				{if $USER->isModerator()}
					{assign var="c_text_prev" value=$comment->getExtraField('text_prev')}
					{if $c_text_prev}
						<span class="small gray">[ {$comment->getExtraField('text_prev')} ]</span>
					{/if}
				{/if}
			</div>
			{assign var="isRemovable" value=$comment->isRemovable()}
			{assign var="isClearable" value=$comment->isClearable()}
			{assign var="isEditable" value=$comment->isEditable()}
			{if $isClearable || $isRemovable || $isEditable}
			<div class="fr">
				{capture assign="comment_moderator"}
				{if $isRemovable}
					<a href="{href target=comment_del p1=$comment_id p2=$comment->getItemType()}" class="mrl_tiny mrr_tiny" rel="c_del" value="{$comment_id}">Del</a>
				{/if}
				{if $isClearable}
					<a href="{href target=comment_clr p1=$comment_id p2=$comment->getItemType()}" class="mrl_tiny mrr_tiny" rel="c_clr" value="{$comment_id}">Clear</a>
				{/if}
				{if $isEditable}
					<a href="{href target=comment_upd p1=$comment_id p2=$comment->getItemType()}" class="mrl_tiny mrr_tiny" rel="c_upd" value="{$comment_id}">Edit</a>
				{/if}
				{/capture}
				{boxinfo content=$comment_moderator type=3 scheme='yellow'}
				<div class="clear"></div>
			</div>
			{/if}
		</td>
	</tr>
	{if $smarty.foreach.foreach_comment.iteration!=$comment_collection->length()}
		<tr><td class="vmid" colspan="1" height="2" width="100%"><hr class="casper"></td></tr>
	{/if}
{/foreach}
</tbody>
</table>
