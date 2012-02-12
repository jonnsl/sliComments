CREATE TABLE IF NOT EXISTS `#__slicomments_flags` (
  `comment_id` integer unsigned NOT NULL,
  `user_id` integer unsigned NOT NULL,
  INDEX `idx_comment_id` (`comment_id`),
  INDEX `idx_user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

ALTER TABLE  `#__slicomments_ratings` CHANGE  `vote`  `vote` BOOLEAN NOT NULL;

UPDATE `#__slicomments_ratings` SET `vote` = 0 WHERE `vote` = -1;