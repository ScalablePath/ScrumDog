#executed on production on 11/9

ALTER TABLE `sd_question` ADD `hours` INT NULL AFTER `obstacles`;

ALTER TABLE `sd_question` CHANGE `hours` `hours` FLOAT NULL DEFAULT NULL;

update sd_question q SET hours = (SELECT SUM(hours) FROM sd_task_hours th WHERE q.user_id=th.user_id AND q.date=th.date AND q.product_id=th.product_id GROUP BY user_id, date, product_id) WHERE hours IS NULL;

update sd_question q SET hours = .01 WHERE hours IS NULL;

ALTER TABLE `sd_question` CHANGE `hours` `hours` FLOAT NOT NULL;