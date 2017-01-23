{include file="admin/header.tpl"}

<form name="form_dbg" action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">
	<span class="mrl">
		<input type="radio" name="debug" id="debug_on" value="on" {checked_selected type="checkbox" arg1=$dbg_info.on arg2=true} />
		{label for='debug_on' data='ON'}

		<input type="radio" name="debug" id="debug_off" value="off" {checked_selected type="checkbox" arg1=$dbg_info.off arg2=true} />
		{label for='debug_off' data='OFF'}
	</span>
	<span class="mrl">
		<input type="submit" name="submit_debug" id="" class="button"value="dbg on/off" />
	</span>
</form>

<div class="clear_big"></div>

<form name="form_dbg_realtime" action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">
	<span class="mrl">
		<input type="radio" name="debug_realtime" id="debug_realtime_on" value="on" {checked_selected type="checkbox" arg1=$dbg_realtime.on arg2=true} />
		{label for='debug_realtime_on' data='ON'}

		<input type="radio" name="debug_realtime" id="debug_realtime_off" value="off" {checked_selected type="checkbox" arg1=$dbg_realtime.off arg2=true} />
		{label for='debug_realtime_off' data='OFF'}
	</span>
	<span class="mrl">
		<input type="submit" name="submit_debug_realtime" id="" class="button"value="dbg realtime on/off" />
	</span>
</form>

<div class="clear_big"></div>

<form name="form_compress" action="{$smarty.server.PHP_SELF}" method="post" accept-charset="utf-8">
	<span class="mrl">
		<input type="radio" name="compress" id="compress_on" value="on" {checked_selected type="checkbox" arg1=$compress_info.on arg2=true} />
		{label for='compress_on' data='ON'}

		<input type="radio" name="compress" id="compress_off" value="off" {checked_selected type="checkbox" arg1=$compress_info.off arg2=true} />
		{label for='compress_off' data='OFF'}
	</span>
	<span class="mrl">
		<input type="submit" name="submit_compress" id="" class="button"value="compress html on/off" />
	</span>
</form>

{include file="admin/footer.tpl"}