DROP DATABASE IF EXISTS chan;
SET time_zone = 'UTC';
CREATE DATABASE chan;
USE chan;

CREATE TABLE IF NOT EXISTS boards (
 id INTEGER PRIMARY KEY AUTO_INCREMENT,
 name TEXT NOT NULL,
 short_name TEXT NOT NULL,
 creation_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO boards (id, name, short_name) VALUES (1, "General", "gn");

CREATE TABLE IF NOT EXISTS posts_gn (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  created DATETIME DEFAULT CURRENT_TIMESTAMP,
  last_bump DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  thread_id INT NULL,
  type ENUM('thread', 'reply') NOT NULL,
  name TEXT NOT NULL DEFAULT 'Anonymous',
  subject TEXT NULL,
  filename TEXT NULL,
  comment TEXT NULL CHECK (comment != ''),
  image_id VARCHAR(18) NULL CHECK (image_id != ''),
  ip VARCHAR(15) NOT NULL,
  
  CONSTRAINT valid_content CHECK (
    CASE
      WHEN type = 'thread' THEN (comment IS NOT NULL OR subject IS NOT NULL) AND (image_id IS NOT NULL) AND (filename IS NOT NULL)
      WHEN type = 'reply' THEN (subject IS NULL) AND (comment IS NOT NULL OR image_id IS NOT NULL)
    END
  ),
  CONSTRAINT thread_id FOREIGN KEY (thread_id)
    REFERENCES posts_gn(id)
    ON DELETE CASCADE,
  CONSTRAINT thread_id_necessity CHECK (
    CASE
      WHEN type = 'thread' THEN (thread_id IS NULL)
      WHEN type = 'reply' THEN (thread_id IS NOT NULL)
    END
  )
);

--thread id must be referring to an actual thread
DELIMITER //
CREATE TRIGGER validate_thread_id_before_insert
BEFORE INSERT ON posts_gn
FOR EACH ROW
BEGIN
  IF NEW.thread_id IS NOT NULL AND NOT EXISTS (
    SELECT 1 FROM posts_gn 
    WHERE id = NEW.thread_id AND type = 'thread'
  ) THEN
    SIGNAL SQLSTATE '45000' 
    SET MESSAGE_TEXT = 'thread_id must reference a post of type "thread"';
  END IF;
END//
DELIMITER ;

--a view used for selecting reply count and image count for catalog view
CREATE VIEW thread_stats AS
SELECT 
  t.id AS thread_id,
  COUNT(r.id) AS reply_count,
  SUM(CASE WHEN r.image_id IS NOT NULL THEN 1 ELSE 0 END) AS image_count
FROM posts_gn t
LEFT JOIN posts_gn r ON r.thread_id = t.id AND r.type = 'reply'
WHERE t.type = 'thread'
GROUP BY t.id;

--indexes for frequent select operations
CREATE INDEX idx_thread_type ON posts_gn(type);
CREATE INDEX idx_threadid_type ON posts_gn(thread_id, type);
CREATE INDEX idx_reply_thread ON posts_gn(thread_id, created DESC);
CREATE INDEX idx_thread_bump ON posts_gn(type, last_bump DESC);

-- SELECT p.*, s.reply_count, s.image_count
-- FROM posts_gn p
-- LEFT JOIN thread_stats s ON p.id = s.thread_id
-- WHERE p.type = 'thread'
-- ORDER BY p.last_bump DESC;

-- INSERT INTO posts_gn (thread_id, comment, type, image_id, filename)
-- VALUES(null, 'comment', 'thread', 'image_id', null);

-- SELECT DISTINCT ip FROM posts_gn;
-- SELECT * FROM posts_gn WHERE comment LIKE '%test%';

-- SELECT * FROM posts_gn WHERE type = 'reply' AND thread_id = 1 ORDER BY created DESC LIMIT 5 