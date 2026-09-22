ALTER TABLE `users` ADD COLUMN `username` VARCHAR(50) NOT NULL AFTER `id`, ADD UNIQUE KEY `username` (`username`);
