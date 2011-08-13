CREATE TABLE IF NOT EXISTS `#__comments` (
  `id` integer unsigned NOT NULL auto_increment,
  `user_id` integer NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `text` varchar(500) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `article_id` integer unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;