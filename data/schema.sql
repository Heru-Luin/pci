CREATE TABLE `project` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `description` TEXT,
  `owner_login` varchar(255) NOT NULL
);

CREATE TABLE `build` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `project_id` INTEGER NOT NULL,
  `branch` varchar(255) NOT NULL,
  `sha` varchar(255) NOT NULL,
  `commit` TEXT NOT NULL,
  `payload` TEXT NOT NULL,
  `output` TEXT NOT NULL,
  `status` INTEGER DEFAULT 0 NOT NULL,  
  `execution_time` INTEGER NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`project_id`) REFERENCES `project` (`id`)
);
