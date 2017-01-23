<div id="cc_context">
	{capture assign="comment_header"}
	<div class="box_in_box box_info">
		<div class="fl">
			Комментарии <span class="gray">(<span rel="cc-{$comment->getItemType()}-{$comment->getItemId()}">{$comment_collection->length()}</span>)</span>
			<span class="mrl" id="cf_ajaxloader"></span>
		</div>
		<div class="fr"><a href="javascript://toggle" title="" id="c_toggle"><span id="c_open" class="hidden">Добавить</span><span id="c_close" class="gray">Скрыть</span></a></div>
		<div class="clear"></div>
		<div id="cf_context">
			<div id="ce_context" class="pdt_small hidden">
				{boxinfo content="<ul></ul>" type=3}
			</div>
			{if $USER->exists()}
			<form name="c_form" id="c_form" method="post" enctype="application/x-www-form-urlencoded" action="{href target=comment_add}" accept-charset="utf-8">
				<div class="clear_medium"></div>
				<textarea name="text" id="c_text" cols="" rows="5" class="input_text" style="width:98%" maxlength="{$comment->getMaxLength()}"></textarea>
				<div class="clear_medium"></div>
				<div class="fl">
					<div class="gray mrb_tiny">Символов: <span id="cf_length">{$comment->getMaxLength()}</span></div>
					<!--{*
					<label class="m_mrl_tiny gray"><input type="checkbox" name="" value="1" class="vmid" /> Получать новые комментарии на почту</label>
					<span class="mrl">ok</span>
					*}-->
				</div>
				<div class="fr mrt_small">
					<span id="c_submit_tip" class="gray hidden">Ctrl+Enter</span>
					<input type="submit" name="c_submit" id="c_submit" value="Добавить" class="button mrl" />
				</div>
				<div class="clear"></div>
				<input type="hidden" name="item_id" id="c_item_id" value="{$comment->getItemId()}" />
				<input type="hidden" name="item_type" id="c_item_type" value="{$comment->getItemType()}" />
				<input type="hidden" name="c_url_add" id="c_url_add" value="{href target='comment_add'}" />
				<input type="hidden" name="c_url_get" id="c_url_get" value="{href target='comment_get'}" />
				{if $USER->isModerator()}
				<input type="hidden" name="c_url_clr" id="c_url_clr" value="{href target='comment_clr'}" />
				<input type="hidden" name="c_url_del" id="c_url_del" value="{href target='comment_del'}" />
				{/if}
			</form>
			{else}
				<p><a href="{href target='auth_login'}" title="Вход" rel="nofollow">Войдите</a> на сайт для добавления комментариев</p>
			{/if}
		</div>
	</div>
	{/capture}
	{boxinfo content=$comment_header type=2}

	<div class="clear_medium"></div>

	<div id="ci_context">
		{include file="comment/item.tpl" comment_collection=$comment_collection}
	</div>
</div>
<script type="text/javascript">window.js_com.init();</script>