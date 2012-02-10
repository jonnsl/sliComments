CREATE TABLE IF NOT EXISTS `#__slicomments_flags` (
  `comment_id` integer unsigned NOT NULL,
  `user_id` integer unsigned NOT NULL,
  INDEX `idx_comment_id` (`comment_id`),
  INDEX `idx_user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;