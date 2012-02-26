ALTER TABLE sd_invitation DROP `projects`;
ALTER TABLE sd_invitation DROP `sprints`;
ALTER TABLE sd_invitation ADD `project_id` INT NOT NULL;
ALTER TABLE sd_invitation ADD `status` INT NOT NULL;
ALTER TABLE sd_invitation ADD `updated_at` DATETIME;
ALTER TABLE sd_invitation ADD FOREIGN KEY (project_id) REFERENCES sd_project(id);

