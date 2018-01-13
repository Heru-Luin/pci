CREATE TABLE `repository` (
  `id` VARCHAR(255) NOT NULL PRIMARY KEY,
  `token` VARCHAR(255) NOT NULL
);

CREATE TABLE `build` (
  `id` INTEGER NOT NULL PRIMARY KEY,
  `repository_id` VARCHAR(255) NOT NULL,
  `payload` TEXT NOT NULL,
  `output` TEXT NOT NULL,
  `status` INTEGER DEFAULT 0 NOT NULL,
  `execution_time` INTEGER NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`repository_id`) REFERENCES `repository` (`id`)
);
