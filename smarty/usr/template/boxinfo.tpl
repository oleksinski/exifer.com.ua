{if isset($__content__) && $__content__}

	{if $__type__==1}

	<div class="{$__class__}" style="position:relative;{if $__width__} width:{$__width__};{/if}{if $__min_height__} min-height:{$__min_height__};{/if}">
		<div class="box_bg_opacity box_bg_m2"></div>
		<div class="box_bg_opacity box_bg_m1"></div>
		<div class="box_bg_opacity pad_obr_s">
			<div class="box_bg_m1 box_bg_wh"></div>
			<div class="box_bg_wh pad5px">
				<div class="clear2"></div>
				{$__content__}
				<div class="clear2"></div>
			</div>
			<div class="box_bg_m1 box_bg_wh"></div>
			<div class="clear2"></div>
		</div>
		<div class="box_bg_opacity box_bg_m1"></div>
		<div class="box_bg_opacity box_bg_m2"></div>
	</div>

	{elseif $__type__==2}

	<div style="position:relative;{if $__width__} width:{$__width__};{/if}{if $__min_height__} min-height:{$__min_height__};{/if}">
		<div class="fl_ie6 pr">
			<div class="corner_tl"></div>
			<div class="corner_tr"></div>
			<div class="box_info bbord bgrad smallcol_ie_width">{$__content__}</div>
			<div class="corner_bl"></div>
			<div class="corner_br"></div>
		</div>
		<div class="clear"></div>
	</div>

	{elseif $__type__==3}

	<div class="{$__class__}" style="position:relative;{if $__width__} width:{$__width__};{if $__min_height__} min-height:{$__min_height__};{/if}{/if}">
		<div class="b_out"></div>
		<div class="b_in"></div>
		<div class="b_pad">
			{$__content__}
			<div class="clear2"></div>
		</div>
		<div class="b_in"></div>
		<div class="b_out"></div>
	</div>

	{/if}

{/if}
