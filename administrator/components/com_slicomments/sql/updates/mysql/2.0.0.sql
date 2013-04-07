ALTER TABLE  `#__slicomments` ADD `item_id` integer unsigned NOT NULL,
ADD `extension` varchar(50) NOT NULL;

UPDATE `#__slicomments` SET
item_id = article_id,
extension = "com_content";

ALTER TABLE `#__slicomments` DROP `article_id`;

ALTER TABLE  `#__slicomments` ADD  `positive_votes` INT NOT NULL default '1',
ADD  `negative_votes` INT NOT NULL ,
ADD  `total_votes` INT NOT NULL default '1',
ADD  `score` DOUBLE NOT NULL default '0.20654329147389294',
ADD  `hot` DOUBLE NOT NULL;

UPDATE `#__slicomments` SET
negative_votes = (SELECT count(*) FROM `#__slicomments_ratings` WHERE `vote` = 0 AND comment_id = id),
positive_votes = (SELECT count(*) FROM `#__slicomments_ratings` WHERE `vote` = 1 AND comment_id = id) + 1,
total_votes = negative_votes + positive_votes;

UPDATE `#__slicomments` SET
score = ((positive_votes + 1.9208) / total_votes - 1.96 * SQRT((positive_votes * negative_votes) / total_votes + 0.9604) / total_votes) / (1 + 3.8416 / total_votes),
hot = LOG10(ABS(positive_votes - negative_votes) + 1) * SIGN(positive_votes - negative_votes) + (UNIX_TIMESTAMP(created) / 300000);
