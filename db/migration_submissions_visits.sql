-- ADEPTIO – New tables for the merged site/admin backend.
-- Charset/collation match the existing core tables (utf8mb4 / utf8mb4_general_ci).

USE `adeptio_db`;

-- Contact form submissions coming from /api/submit-form.php
CREATE TABLE IF NOT EXISTS `submissions` (
  `id`           INT AUTO_INCREMENT PRIMARY KEY,
  `name`         VARCHAR(150) NOT NULL,
  `email`        VARCHAR(150) DEFAULT NULL,        -- forms don't collect email yet -> nullable
  `phone`        VARCHAR(40)  DEFAULT NULL,         -- captured from the contact forms
  `message`      TEXT,
  `source_page`  VARCHAR(255) DEFAULT NULL,
  `submitted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_submitted` (`submitted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Page visits logged by /api/track-visit.php (deduped per session+page via cookie)
CREATE TABLE IF NOT EXISTS `page_visits` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `page_url`   VARCHAR(500) DEFAULT NULL,
  `visited_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `referrer`   VARCHAR(500) DEFAULT NULL,
  `session_id` VARCHAR(64)  DEFAULT NULL,
  KEY `idx_visited` (`visited_at`),
  KEY `idx_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
