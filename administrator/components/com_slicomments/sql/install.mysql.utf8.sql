CREATE TABLE IF NOT EXISTS `#__slicomments` (
  `id` integer unsigned NOT NULL auto_increment,
  `user_id` integer NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `raw` text NOT NULL default '',
  `text` text NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `item_id` integer unsigned NOT NULL,
  `status` smallint default '1',
  `extension` varchar(50) NOT NULL,
  `positive_votes` int(11) NOT NULL default '1',
  `negative_votes` int(11) NOT NULL,
  `total_votes` int(11) NOT NULL default '1',
  `score` double NOT NULL default '0.20654329147389294',
  `hot` double NOT NULL,
  `spam` INT NOT NULL ,
  `spaminess` DOUBLE NOT NULL,
  `ip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_item_id` (`item_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_score` (`score`),
  INDEX `idx_hot` (`hot`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__slicomments_ratings` (
  `comment_id` integer unsigned NOT NULL,
  `user_id` integer unsigned NOT NULL,
  `vote` boolean NOT NULL,
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