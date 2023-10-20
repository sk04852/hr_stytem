INSERT INTO `groups`(`id`, `group_name`, `is_default`, `created_at`, `updated_at`)
VALUES
(1, 'Administrator', 0, now(), now()),
(2, 'Modrator', 0, now(), now()),
(3, 'Employee', 0, now(), now()),
(4, 'Company Owner', 0, now(), now()),
(5, 'Guest', 1, now(), now());
-- First user of the system should belong to root(Administrator) Group
INSERT INTO `user_groups`(`user_id`, `group_id`, `created_at`, `updated_at`)
VALUES(1, 1, now(), now());
