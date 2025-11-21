-- Check if token table has remark column
DESCRIBE token;

-- If remark column doesn't exist, add it
ALTER TABLE token ADD COLUMN remark TEXT;

-- Check the current structure
SHOW CREATE TABLE token;