{include file="admin/header.tpl"}

<form name="form_cron" action="{href target='admin_dbg_cron'}" method="post" accept-charset="utf-8">
	<span class="mrl">
		CRON
	</span>
	<span class="mrl">
		<select name="cron_file" id="cron_file" class="" style="" rel="">
			{foreach from=$cron_file_list key=index item=c_file name=foreach_cron}
			<option value="{$c_file}" {checked_selected type="option" arg1=$c_file arg2=$cron_file}>{$c_file}</option>
			{/foreach}
		</select>
		<input type="text" name="cron_param" value="{$cron_param|escape:'html'}" class="mrl_small input_text" style="width:50px" />
	</span>
	<span class="mrl">
		<input type="submit" name="" id="" class="button" style="" rel="" value="execute" />
	</span>
</form>

{include file="admin/footer.tpl"}