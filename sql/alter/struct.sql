#
# Alter structure for table `photo`
#

# change views position
ALTER TABLE `photo` MODIFY `views` int(11) unsigned NOT NULL default "0" AFTER `rgb`;

# votes
ALTER TABLE `photo` ADD `votes` int(11) unsigned NOT NULL default "0" AFTER `comments`;
ALTER TABLE `photo` ADD `votes_pros` int(11) unsigned NOT NULL default "0" AFTER `votes`;
ALTER TABLE `photo` ADD `votes_cons` int(11) unsigned NOT NULL default "0" AFTER `votes_pros`;
ALTER TABLE `photo` ADD `votes_zero` int(11) unsigned NOT NULL default "0" AFTER `votes_cons`;
ALTER TABLE `photo` ADD `votes_value` decimal(11,1) NOT NULL default "0.0" AFTER `votes_zero`;

# articles
ALTER TABLE `photo` ADD `articles` int(11) unsigned NOT NULL default "0" AFTER `comments`;

#
#
# ------------------------------------------
#
# Alter structure for table `photo_deleted`
#

# change views position
ALTER TABLE `photo_deleted` MODIFY `views` int(11) unsigned NOT NULL default "0" AFTER `rgb`;

# votes
ALTER TABLE `photo_deleted` ADD `votes` int(11) unsigned NOT NULL default "0" AFTER `comments`;
ALTER TABLE `photo_deleted` ADD `votes_pros` int(11) unsigned NOT NULL default "0" AFTER `votes`;
ALTER TABLE `photo_deleted` ADD `votes_cons` int(11) unsigned NOT NULL default "0" AFTER `votes_pros`;
ALTER TABLE `photo_deleted` ADD `votes_zero` int(11) unsigned NOT NULL default "0" AFTER `votes_cons`;
ALTER TABLE `photo_deleted` ADD `votes_value` decimal(11,1) NOT NULL default "0.0" AFTER `votes_zero`;

# articles
ALTER TABLE `photo_deleted` ADD `articles` int(11) unsigned NOT NULL default "0" AFTER `comments`;

#
#
# ------------------------------------------
#
# Alter structure for table `vote_photo`
#
ALTER TABLE `vote_photo` ADD `type` tinyint(1) NOT NULL default "1" AFTER `user_id`;

#
#
# ------------------------------------------
#
# Alter structure for table `user`
#
ALTER TABLE `user` CHANGE `info` `about` text default NULL;

#
#
# ------------------------------------------
#
# Alter structure for table `user_deleted`
#
ALTER TABLE `user_deleted` CHANGE `info` `about` text default NULL;



# DELETE user.status_tstamp
# DELETE user_deleted.status_tstamp
# DELETE photo.privacy
# DELETE photo_deleted.privacy
# MODIFY photo_user_view
# MODIFY user_online

# DELETE user.authcode
# DELETE KEY user.authcode
# DELETE user_deleted.authcode

ALTER TABLE `user` ADD `ban_tstamp` int(11) unsigned default NULL AFTER `upload_next_tstamp`;
ALTER TABLE `user_deleted` ADD `ban_tstamp` int(11) unsigned default NULL AFTER `upload_next_tstamp`;

ALTER TABLE `user` ADD `about_emails` text default NULL after `about`;
ALTER TABLE `user` ADD `about_phones` text default NULL after `about_emails`;
ALTER TABLE `user` ADD `about_urls` text default NULL after `about_phones`;
ALTER TABLE `user` ADD `about_ims` text default NULL after `about_urls`;

ALTER TABLE `user_deleted` ADD `about_emails` text default NULL after `about`;
ALTER TABLE `user_deleted` ADD `about_phones` text default NULL after `about_emails`;
ALTER TABLE `user_deleted` ADD `about_urls` text default NULL after `about_phones`;
ALTER TABLE `user_deleted` ADD `about_ims` text default NULL after `about_urls`;


# time - 32 bit

ALTER TABLE `exifer`.`comment_photo` modify `add_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`comment_photo` modify `change_tstamp` bigint(20) default NULL;

ALTER TABLE `exifer`.`feedback` modify `add_tstamp` bigint(20) default NULL;

ALTER TABLE `exifer`.`photo_thumb` modify `tstamp` bigint(20) default NULL;

ALTER TABLE `exifer`.`photo` modify `add_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`photo` modify `update_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`photo` modify `view_tstamp` bigint(20) default NULL;

ALTER TABLE `exifer`.`photo_deleted` modify `add_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`photo_deleted` modify `update_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`photo_deleted` modify `view_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`photo_deleted` modify `del_tstamp` bigint(20) default NULL;

ALTER TABLE `exifer`.`user_online` modify `hit_tstamp` bigint(20) default NULL;

ALTER TABLE `exifer`.`user_picture` modify `tstamp` bigint(20) default NULL;

ALTER TABLE `exifer`.`user` modify `birthday` bigint(20) default NULL;
ALTER TABLE `exifer`.`user` modify `userpic_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user` modify `upload_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user` modify `upload_next_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user` modify `ban_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user` modify `hit_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user` modify `login_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user` modify `update_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user` modify `reg_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user` modify `view_tstamp` bigint(20) default NULL;

ALTER TABLE `exifer`.`user_deleted` modify `birthday` bigint(20) default NULL;
ALTER TABLE `exifer`.`user_deleted` modify `userpic_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user_deleted` modify `upload_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user_deleted` modify `upload_next_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user_deleted` modify `ban_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user_deleted` modify `hit_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user_deleted` modify `login_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user_deleted` modify `update_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user_deleted` modify `reg_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user_deleted` modify `view_tstamp` bigint(20) default NULL;
ALTER TABLE `exifer`.`user_deleted` modify `del_tstamp` bigint(20) default NULL;

ALTER TABLE `exifer`.`vote_photo` modify `add_tstamp` bigint(20) default NULL;

# change comment_table
ALTER TABLE `exifer`.`comment_photo` MODIFY `item_id` int(11) unsigned NOT NULL AFTER `id`;
ALTER TABLE `exifer`.`comment_photo` MODIFY `user_id` int(11) unsigned NOT NULL AFTER `item_id`;
ALTER TABLE `exifer`.`comment_photo` MODIFY `root_id` int(11) unsigned NOT NULL AFTER `user_id`;

