
-- Drop old tables if needed (for clean demo)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS conversation_users;
DROP TABLE IF EXISTS conversations;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- Users
CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(50) NOT NULL,
                       email VARCHAR(100) NOT NULL UNIQUE,
                       password_hash VARCHAR(255) NOT NULL,
                       role ENUM('front','back') NOT NULL DEFAULT 'front',
                       is_active TINYINT(1) NOT NULL DEFAULT 1,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Conversations
CREATE TABLE conversations (
                               id INT AUTO_INCREMENT PRIMARY KEY,
                               title VARCHAR(255) NOT NULL,
                               is_group TINYINT(1) NOT NULL DEFAULT 0, -- 0 = DM, 1 = group
                               created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Junction table: conversation <-> users (with admin flag)
CREATE TABLE conversation_users (
                                    conversation_id INT NOT NULL,
                                    user_id INT NOT NULL,
                                    is_admin TINYINT(1) NOT NULL DEFAULT 0,
                                    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (conversation_id, user_id),
                                    CONSTRAINT fk_conv_user_conversation
                                        FOREIGN KEY (conversation_id) REFERENCES conversations(id)
                                            ON DELETE CASCADE,
                                    CONSTRAINT fk_conv_user_user
                                        FOREIGN KEY (user_id) REFERENCES users(id)
                                            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Messages
CREATE TABLE messages (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          conversation_id INT NOT NULL,
                          user_id INT NOT NULL,
                          content TEXT NOT NULL,
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          CONSTRAINT fk_messages_conversation
                              FOREIGN KEY (conversation_id) REFERENCES conversations(id)
                                  ON DELETE CASCADE,
                          CONSTRAINT fk_messages_user
                              FOREIGN KEY (user_id) REFERENCES users(id)
                                  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========== SAMPLE DATA ==========

-- 10 users (alice is admin/backoffice)
INSERT INTO users (id, username, email, password_hash, role, is_active) VALUES
                                                                            (1, 'alice',   'alice@example.com',   'pass123', 'back',  1),
                                                                            (2, 'bob',     'bob@example.com',     'pass123', 'front', 1),
                                                                            (3, 'charlie', 'charlie@example.com', 'pass123', 'front', 1),
                                                                            (4, 'david',   'david@example.com',   'pass123', 'front', 1),
                                                                            (5, 'eve',     'eve@example.com',     'pass123', 'front', 1),
                                                                            (6, 'frank',   'frank@example.com',   'pass123', 'front', 1),
                                                                            (7, 'grace',   'grace@example.com',   'pass123', 'front', 1),
                                                                            (8, 'heidi',   'heidi@example.com',   'pass123', 'front', 1),
                                                                            (9, 'ivan',    'ivan@example.com',    'pass123', 'front', 1),
                                                                            (10,'judy',    'judy@example.com',    'pass123', 'front', 1);

-- 30 conversations (some DMs, some groups)
INSERT INTO conversations (id, title, is_group) VALUES
                                                    (1,  'Alice & Bob',        0),
                                                    (2,  'Alice & Charlie',    0),
                                                    (3,  'Alice & David',      0),
                                                    (4,  'Alice & Eve',        0),
                                                    (5,  'Bob & Charlie',      0),
                                                    (6,  'Bob & David',        0),
                                                    (7,  'Bob & Eve',          0),
                                                    (8,  'Charlie & David',    0),
                                                    (9,  'Charlie & Eve',      0),
                                                    (10, 'David & Eve',        0),
                                                    (11, 'Study Group A',      1),
                                                    (12, 'Study Group B',      1),
                                                    (13, 'Gaming Squad',       1),
                                                    (14, 'Project Team 1',     1),
                                                    (15, 'Project Team 2',     1),
                                                    (16, 'Family Chat 1',      1),
                                                    (17, 'Family Chat 2',      1),
                                                    (18, 'Friends Forever',    1),
                                                    (19, 'Weekend Plans',      1),
                                                    (20, 'Music Lovers',       1),
                                                    (21, 'Anime Club',         1),
                                                    (22, 'Work Buddies',       1),
                                                    (23, 'Esprit G2',          1),
                                                    (24, 'Dev Chat',           1),
                                                    (25, 'Random',             1),
                                                    (26, 'Lab Group',          1),
                                                    (27, 'Dorm Chat',          1),
                                                    (28, 'Gym Bros',           1),
                                                    (29, 'Movie Night',        1),
                                                    (30, 'CS Project',         1);

-- conversation_users (admin = first user of each list)
INSERT INTO conversation_users (conversation_id, user_id, is_admin) VALUES
                                                                        (1, 1,1),(1, 2,0),
                                                                        (2, 1,1),(2, 3,0),
                                                                        (3, 1,1),(3, 4,0),
                                                                        (4, 1,1),(4, 5,0),
                                                                        (5, 2,1),(5, 3,0),
                                                                        (6, 2,1),(6, 4,0),
                                                                        (7, 2,1),(7, 5,0),
                                                                        (8, 3,1),(8, 4,0),
                                                                        (9, 3,1),(9, 5,0),
                                                                        (10,4,1),(10,5,0),

                                                                        (11,1,1),(11,2,0),(11,3,0),
                                                                        (12,4,1),(12,5,0),(12,6,0),
                                                                        (13,2,1),(13,3,0),(13,4,0),(13,5,0),
                                                                        (14,1,1),(14,4,0),(14,7,0),(14,8,0),
                                                                        (15,2,1),(15,5,0),(15,8,0),(15,9,0),
                                                                        (16,1,1),(16,6,0),(16,7,0),
                                                                        (17,3,1),(17,8,0),(17,9,0),
                                                                        (18,4,1),(18,5,0),(18,6,0),(18,7,0),
                                                                        (19,2,1),(19,3,0),(19,8,0),(19,10,0),
                                                                        (20,1,1),(20,3,0),(20,5,0),(20,7,0),(20,9,0),
                                                                        (21,1,1),(21,8,0),(21,9,0),(21,10,0),
                                                                        (22,2,1),(22,4,0),(22,6,0),(22,8,0),
                                                                        (23,3,1),(23,5,0),(23,7,0),(23,9,0),
                                                                        (24,1,1),(24,2,0),(24,3,0),(24,4,0),(24,5,0),
                                                                        (25,6,1),(25,7,0),(25,8,0),(25,9,0),(25,10,0),
                                                                        (26,1,1),(26,6,0),(26,9,0),
                                                                        (27,2,1),(27,7,0),(27,10,0),
                                                                        (28,4,1),(28,6,0),(28,10,0),
                                                                        (29,3,1),(29,5,0),(29,8,0),
                                                                        (30,1,1),(30,3,0),(30,6,0),(30,10,0);

-- Messages (2 demo messages per conversation – copy pattern if you want more)
INSERT INTO messages (conversation_id, user_id, content) VALUES
                                                             (1, 1, 'Hi Bob, this is Alice in conversation 1'),
                                                             (1, 2, 'Hey Alice! Nice to chat.'),

                                                             (2, 1, 'Hi Charlie, Alice here in convo 2'),
                                                             (2, 3, 'Hi Alice!'),

                                                             (3, 1, 'Hi David, we need to talk about project.'),
                                                             (3, 4, 'Sure, tell me.'),

                                                             (4, 1, 'Hi Eve, how are you?'),
                                                             (4, 5, 'I am good, thanks Alice.'),

                                                             (5, 2, 'Yo Charlie, it''s Bob'),
                                                             (5, 3, 'Hey Bob!'),

                                                             (6, 2, 'David, are you coming later?'),
                                                             (6, 4, 'Yes, I will.'),

                                                             (7, 2, 'Eve, did you see this?'),
                                                             (7, 5, 'Not yet, send it.'),

                                                             (8, 3, 'David, ready for the exam?'),
                                                             (8, 4, 'Almost, still revising.'),

                                                             (9, 3, 'Eve, group homework?'),
                                                             (9, 5, 'Working on it.'),

                                                             (10,4, 'Eve, let''s meet after class.'),
                                                             (10,5,'Ok David.'),

                                                             (11,1,'Welcome to Study Group A!'),
                                                             (11,2,'Thanks for the invite.'),
                                                             (11,3,'Let''s ace this.'),

                                                             (12,4,'Study Group B starting.'),
                                                             (12,5,'Hi everyone.'),
                                                             (12,6,'Hello!'),

                                                             (13,2,'Gaming squad, tonight?'),
                                                             (13,3,'I''m in.'),
                                                             (13,4,'Me too.'),

                                                             (14,1,'Project Team 1 kickoff.'),
                                                             (14,4,'Let''s go.'),
                                                             (14,7,'I''m here.'),
                                                             (14,8,'Hi all.'),

                                                             (15,2,'Project Team 2 meeting.'),
                                                             (15,5,'Ok.'),
                                                             (15,8,'Got it.'),
                                                             (15,9,'Cool.'),

                                                             (16,1,'Family Chat 1, hello!'),
                                                             (16,6,'Hi there.'),
                                                             (16,7,'Hey!'),

                                                             (17,3,'Family Chat 2 checking in.'),
                                                             (17,8,'Hi!'),
                                                             (17,9,'Hello.'),

                                                             (18,4,'Friends Forever ❤️'),
                                                             (18,5,'Always.'),
                                                             (18,6,'For sure.'),
                                                             (18,7,'Yesss.'),

                                                             (19,2,'Weekend plans?'),
                                                             (19,3,'Let''s go out.'),
                                                             (19,8,'I''m in.'),
                                                             (19,10,'Same.'),

                                                             (20,1,'Music Lovers, new playlist.'),
                                                             (20,3,'Drop the link.'),
                                                             (20,5,'Nice tracks.'),
                                                             (20,7,'Love it.'),
                                                             (20,9,'Fire.'),

                                                             (21,1,'Anime Club tonight.'),
                                                             (21,8,'Can''t wait.'),
                                                             (21,9,'Same here.'),
                                                             (21,10,'Let''s go.'),

                                                             (22,2,'Work buddies, standup in 10.'),
                                                             (22,4,'Ok.'),
                                                             (22,6,'On my way.'),
                                                             (22,8,'Got it.'),

                                                             (23,3,'Esprit G2 lecture starts now.'),
                                                             (23,5,'Thanks.'),
                                                             (23,7,'Here.'),
                                                             (23,9,'Joining.'),

                                                             (24,1,'Dev Chat: merge conflict again.'),
                                                             (24,2,'haha.'),
                                                             (24,3,'classic.'),
                                                             (24,4,'lol.'),
                                                             (24,5,'We fix it.'),

                                                             (25,6,'Random chat time.'),
                                                             (25,7,'What''s up.'),
                                                             (25,8,'All good.'),
                                                             (25,9,'Same.'),
                                                             (25,10,'Nice.'),

                                                             (26,1,'Lab Group meeting at 2 PM.'),
                                                             (26,6,'Ok.'),
                                                             (26,9,'Fine by me.'),

                                                             (27,2,'Dorm Chat noise complaints.'),
                                                             (27,7,'Oops.'),
                                                             (27,10,'Sorry.'),

                                                             (28,4,'Gym Bros, leg day.'),
                                                             (28,6,'Nooo.'),
                                                             (28,10,'Let''s do it.'),

                                                             (29,3,'Movie Night picks?'),
                                                             (29,5,'Action.'),
                                                             (29,8,'Comedy.'),

                                                             (30,1,'CS Project deadline soon.'),
                                                             (30,3,'We''re close.'),
                                                             (30,6,'Need more coffee.'),
                                                             (30,10,'Same 😂');
