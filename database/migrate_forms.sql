-- Add Form Submissions Table
CREATE TABLE `form_submissions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `page_id` INT(11) NOT NULL,
  `data_json` LONGTEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
