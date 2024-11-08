CREATE TABLE `cards` (
  `card_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `real_created` datetime DEFAULT NULL,
  `creator` int UNSIGNED NOT NULL DEFAULT '0',
  `cardfront` text COLLATE utf8mb3_swedish_ci,
  `cardback` text COLLATE utf8mb3_swedish_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

CREATE TABLE `forget_stats_forg` (
  `lesson_id` int UNSIGNED NOT NULL PRIMARY KEY,
  `dif` int UNSIGNED NOT NULL DEFAULT '0',
  `num` int UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

CREATE TABLE `forget_stats_rem` (
  `lesson_id` int UNSIGNED NOT NULL DEFAULT '0',
  `dif` int UNSIGNED NOT NULL DEFAULT '0',
  `num` int UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

CREATE TABLE `lesson2cards` (
  `lesson_id` int UNSIGNED NOT NULL DEFAULT '0',
  `card_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `expires` date DEFAULT NULL,
  `created` date DEFAULT NULL,
  `forgotten` mediumint UNSIGNED DEFAULT NULL,
  `remembered` mediumint UNSIGNED DEFAULT NULL,
  `repetition_algorithm` tinyint UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

CREATE TABLE `lessons` (
  `lesson_id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `lesson_name` varchar(100) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `lesson_filename` varchar(100) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `repetition_algorithm` tinyint UNSIGNED DEFAULT NULL,
  `plugins` json DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;


CREATE TABLE `sessions` (
  `session_id` varchar(50) COLLATE utf8mb3_swedish_ci NOT NULL,
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `ip` varchar(30) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `session_start` datetime DEFAULT NULL,
  `session_last_active` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

CREATE TABLE `sharing` (
  `from_user_id` bigint UNSIGNED NOT NULL,
  `to_user_id` bigint UNSIGNED DEFAULT NULL,
  `from_lesson_id` int UNSIGNED NOT NULL,
  `to_lesson_id` int UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;


CREATE TABLE `users` (
  `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL UNIQUE KEY,
  `password_hash` varbinary(1024) DEFAULT NULL,
  `password_inner_hash_algo` varchar(64) COLLATE utf8mb3_swedish_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `lang` varchar(20) COLLATE utf8mb3_swedish_ci NOT NULL,
  `timezone` varchar(32) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `cardformat` enum('text/plain','application/xhtml+xml') COLLATE utf8mb3_swedish_ci NOT NULL DEFAULT 'text/plain',
  `graphheight` smallint NOT NULL DEFAULT '0',
  `email` varchar(255) COLLATE utf8mb3_swedish_ci NOT NULL,
  `city` varchar(40) COLLATE utf8mb3_swedish_ci NOT NULL,
  `country` varchar(40) COLLATE utf8mb3_swedish_ci NOT NULL,
  `mobile_phone` varchar(20) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `joinedus` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;


ALTER TABLE `cards` ADD FULLTEXT KEY `cardfront` (`cardfront`);
ALTER TABLE `cards` ADD FULLTEXT KEY `cardback` (`cardback`);

ALTER TABLE `forget_stats_forg` ADD KEY `lesson_id` (`lesson_id`);
ALTER TABLE `forget_stats_rem` ADD KEY `lesson_id` (`lesson_id`);

ALTER TABLE `lesson2cards`
  ADD UNIQUE KEY `lesson-card-index` (`lesson_id`,`card_id`),
  ADD KEY `lesson_id` (`lesson_id`),
  ADD KEY `card_id` (`card_id`);

ALTER TABLE `lessons`
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `sessions`
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `sharing`
  ADD KEY `from_user_id` (`from_user_id`,`to_user_id`,`from_lesson_id`,`to_lesson_id`);
