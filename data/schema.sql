CREATE TABLE `repository` (
  `id` INTEGER NOT NULL PRIMARY KEY,
  `full_name` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL
);

CREATE TABLE `build` (
  `id` VARCHAR(255) NOT NULL PRIMARY KEY,
  `repository_id` INTEGER NOT NULL,
  `payload` TEXT NOT NULL,
  `output` TEXT NOT NULL,
  `status` INTEGER DEFAULT 0 NOT NULL,
  `execution_time` INTEGER NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`repository_id`) REFERENCES `repository` (`id`)
);
