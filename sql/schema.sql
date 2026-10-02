-- PromptForge schema
SET NAMES utf8mb4;
SET time_zone = '-03:00';

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  theme VARCHAR(16) NOT NULL DEFAULT 'system',
  avatar VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS libraries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  name VARCHAR(160) NOT NULL,
  description TEXT NULL,
  color VARCHAR(16) NOT NULL DEFAULT '#0E9AA7',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_libraries_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  KEY idx_libraries_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS entries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  library_id INT UNSIGNED NULL,
  type VARCHAR(24) NOT NULL,
  title VARCHAR(220) NOT NULL,
  description TEXT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'draft',
  tags VARCHAR(255) NULL,
  model_hint VARCHAR(120) NULL,
  current_version INT UNSIGNED NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_entries_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_entries_library FOREIGN KEY (library_id) REFERENCES libraries(id) ON DELETE SET NULL,
  KEY idx_entries_user (user_id),
  KEY idx_entries_type (type),
  KEY idx_entries_status (status),
  KEY idx_entries_library (library_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS entry_versions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  entry_id INT UNSIGNED NOT NULL,
  version INT UNSIGNED NOT NULL,
  body MEDIUMTEXT NOT NULL,
  variables_json TEXT NULL,
  changelog TEXT NULL,
  created_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_versions_entry FOREIGN KEY (entry_id) REFERENCES entries(id) ON DELETE CASCADE,
  CONSTRAINT fk_versions_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  UNIQUE KEY uq_entry_version (entry_id, version),
  KEY idx_versions_entry (entry_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  entry_id INT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL,
  input_text TEXT NULL,
  expected_output TEXT NULL,
  criteria TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_tests_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_tests_entry FOREIGN KEY (entry_id) REFERENCES entries(id) ON DELETE CASCADE,
  KEY idx_tests_user (user_id),
  KEY idx_tests_entry (entry_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS evaluations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  test_id INT UNSIGNED NOT NULL,
  entry_version_id INT UNSIGNED NULL,
  score DECIMAL(5,2) NULL,
  verdict VARCHAR(24) NOT NULL DEFAULT 'pending',
  notes TEXT NULL,
  model_label VARCHAR(120) NULL,
  output_sample TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_evaluations_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_evaluations_test FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE,
  CONSTRAINT fk_evaluations_version FOREIGN KEY (entry_version_id) REFERENCES entry_versions(id) ON DELETE SET NULL,
  KEY idx_evaluations_user (user_id),
  KEY idx_evaluations_test (test_id),
  KEY idx_evaluations_verdict (verdict)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
  user_id INT UNSIGNED NOT NULL,
  `key` VARCHAR(64) NOT NULL,
  `value` TEXT NULL,
  PRIMARY KEY (user_id, `key`),
  CONSTRAINT fk_settings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
