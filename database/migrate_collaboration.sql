-- Collaboration Features
CREATE TABLE `project_members` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `project_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `role` VARCHAR(50) DEFAULT 'viewer',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE `comments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `page_id` INT(11) NOT NULL,
  `node_id` VARCHAR(255) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);
