ALTER TABLE  `#__slicomments` ADD `item_id` integer unsigned NOT NULL,
ADD `extension` varchar(50) NOT NULL;

UPDATE `#__slicomments` SET
item_id = article_id,
extension = "com_content";

ALTER TABLE `#__slicomments` DROP `article_id`;
