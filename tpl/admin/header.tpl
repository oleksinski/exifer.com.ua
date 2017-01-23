<div class="fl" style="width:220px">
	{capture assign="admin_left_panel"}
		<div class="box_in_box box_info">
			<ul class="dotted">

				{if $USER->isAdmin($ACCESS_PAGE_MAP.admin_dbg_ctrl)}
				<li><a href="{href target='admin_dbg_ctrl'}">Debug</a></li>
				{/if}

				{if $USER->isAdmin($ACCESS_PAGE_MAP.admin_location)}
				<li><a href="{href target='admin_location'}">Location</a></li>
				{/if}

				{if $USER->isAdmin($ACCESS_PAGE_MAP.admin_dbg_cron)}
				<li><a href="{href target='admin_dbg_cron'}">Cron</a></li>
				{/if}

				{if $USER->isAdmin($ACCESS_PAGE_MAP.admin_user)}
				<li><a href="{href target='admin_user'}">User</a></li>
				{/if}

				{if $USER->isAdmin($ACCESS_PAGE_MAP.admin_photo)}
				<li><a href="{href target='admin_photo'}">Photo</a></li>
				{/if}

				{if $USER->isAdmin($ACCESS_PAGE_MAP.admin_stat)}
				<li><a href="{href target='admin_stat'}">Stat</a></li>
				{/if}
			</ul>
		</div>
	{/capture}
	{boxinfo content=$admin_left_panel type=2}
</div>

<div class="fr" style="width:730px">
