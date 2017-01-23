{$HTML_DOCTYPE_LIST.xhtml1_trans_custom}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{$HTML_META.TITLE}{if $HTML_META.TITLE} &ndash; {/if}Фотосайт {$URL_NAME}</title>
{if isset($HTML_META.META_HTTP_EQUIV_CONTENT)}
{foreach from=$HTML_META.META_HTTP_EQUIV_CONTENT key=name item=content}
	<meta http-equiv="{$name}" content="{$content}" />
{/foreach}
{/if}
{if isset($HTML_META.META_NAME_CONTENT)}
{foreach from=$HTML_META.META_NAME_CONTENT key=name item=content}
	<meta name="{$name}" content="{$content}" />
{/foreach}
{/if}
{if isset($HTML_META.META_LINK_REL_HREF)}
{foreach from=$HTML_META.META_LINK_REL_HREF key=name item=content}
	<link rel="{$name}" href="{$content}" />
{/foreach}
{/if}
{if isset($HTML_META.META_PROPERTY_CONTENT)}
{foreach from=$HTML_META.META_PROPERTY_CONTENT key=name item=content}
	<meta property="{$name}" content="{$content}" />
{/foreach}
{/if}
{foreach from=$CSS key=index item=css name=css_list}
	<link rel="stylesheet" title="screen style" type="text/css" charset="utf-8" href="{$css|v}" />
{/foreach}
	<!--[if lt IE 7]><script type="text/javascript" charset="utf-8" src="{$S_URL|cat:'js/unitpngfix.js'|v}"></script><![endif]-->
{foreach from=$JAVASCRIPT key=index item=js name=js_list}
	<script type="text/javascript" language="javascript" charset="utf-8" src="{$js|v}"></script>
{/foreach}
{foreach from=$RSS key=href item=title name=rss_list}
	<link type="application/rss+xml" rel="alternate" title="RSS 2.0 {$title}" href="{$href}" charset="utf-8" />
{/foreach}
	<script type="text/javascript" language="javascript">
		var JSC = {};
		JSC.NAME = "{$URL_NAME}";
		JSC.URL = "{$URL_PROJECT}";
		JSC.I_URL = "{$I_URL}";
		JSC.S_URL = "{$S_URL}";
		JSC.PRO = {$ENV.PRO};
	</script>
</head>

<body>

{include file="layout/header.tpl"}

{$CONTROL_TPL}

{include file="layout/footer.tpl"}

</body>

</html>
