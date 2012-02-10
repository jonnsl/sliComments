CREATE TABLE IF NOT EXISTS `#__slicomments` (
  `id` integer unsigned NOT NULL auto_increment,
  `user_id` integer NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `raw` text NOT NULL default '',
  `text` text NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `article_id` integer unsigned NOT NULL,
  `rating` integer NOT NULL default '0',
  `status` smallint default '1',
  PRIMARY KEY (`id`),
  INDEX `idx_article_id` (`article_id`),
  INDEX `idx_status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__slicomments_ratings` (
  `comment_id` integer unsigned NOT NULL,
  `user_id` integer unsigned NOT NULL,
  `vote` integer NOT NULL,
  `ip` varchar(100) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  INDEX `idx_comment_id` (`comment_id`),
  INDEX `idx_user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__slicomments_flags` (
  `comment_id` integer unsigned NOT NULL,
  `user_id` integer unsigned NOT NULL,
  INDEX `idx_comment_id` (`comment_id`),
  INDEX `idx_user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;