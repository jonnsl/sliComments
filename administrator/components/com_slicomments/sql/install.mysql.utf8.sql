CREATE TABLE IF NOT EXISTS `#__slicomments` (
  `id` integer unsigned NOT NULL auto_increment,
  `user_id` integer NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `text` varchar(500) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `article_id` integer unsigned NOT NULL,
  `rating` integer NOT NULL default '0',
  `status` smallint default '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__slicomments_ratings` (
  `comment_id` integer unsigned NOT NULL,
  `user_id` integer unsigned NOT NULL,
  `vote` integer NOT NULL,
  PRIMARY KEY (`comment_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;