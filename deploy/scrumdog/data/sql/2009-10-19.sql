#executed on production on 10/31

ALTER TABLE `sd_user` DROP `force_sponsee_image`;
  
ALTER TABLE `sd_user` DROP `sponsor_image`;

DROP TABLE `sd_sponsorship_history`;

DROP TABLE `sd_sponsorship_request`;

DROP TABLE `sd_sponsorship_request_history`;

ALTER TABLE `sd_user` DROP FOREIGN KEY `sd_user_FK_1`;

ALTER TABLE `sd_user` DROP INDEX `sd_user_FI_1`;

ALTER TABLE `sd_user` DROP `current_sponsor_user_id`;