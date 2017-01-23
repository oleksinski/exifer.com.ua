{if !isset($request_query)}
	{assign var="request_query" value=null}
{/if}

<select name="genre" id="genre" class="select select_cons" style="width:100%">
	{foreach from=$genre_collection key=genre_id item=genre name=foreach_genre}
	{if $smarty.foreach.foreach_genre.first}
	<option value="0"{if isset($request_query.genre)}{checked_selected type="option" arg1=0 arg2=$request_query.genre}{/if}>Все жанры{if isset($filter_date)} (за сутки){/if}</option>
	{/if}
	<option value="{$genre_id}" {if isset($genre.count) && !$genre.count}class="gray"{/if}{if isset($request_query.genre)}{checked_selected type="option" arg1=$genre_id arg2=$request_query.genre}{/if}>
		{$genre.name}{if $page_target=='photo_lenta' && isset($genre.count) && $genre.count}&nbsp;|&nbsp;{$genre.count}{/if}
	</option>
	{/foreach}
</select>
<div class="clear"></div>

{if !isset($filter_date)}
	{assign var="filter_date" value=$request_query.date|default:null}
{/if}

{literal}
<script type="text/javascript">
	__$('select[name=genre]').change(function(){
		var url_pattern = {/literal}{href target=$page_target query=$request_query genre="__genre__" date=$filter_date p=null jsify=true}{literal};
		window.location.href = url_pattern.replace(/__genre__/, $(this).val());
	});
</script>
{/literal}
