

CREATE USER IF NOT EXISTS 'pcpoa_app'@'localhost' IDENTIFIED BY 'change_this_password';

GRANT SELECT, INSERT, UPDATE, DELETE ON pcpoa_db.* TO 'pcpoa_app'@'localhost';

-- Explicitly does NOT grant: DROP, ALTER, CREATE, GRANT OPTION, or access
-- to any database other than pcpoa_db - so even if the web app were ever
-- compromised, the attacker could not destroy data or pivot elsewhere.

FLUSH PRIVILEGES;

-- After running this, update config/database.php:
--   define('DB_USER', 'pcpoa_app');
--   define('DB_PASS', 'change_this_password'); -- the one you set above
