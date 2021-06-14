INSERT INTO `users`
	(`id`, `user_name`, `user_hash`, `salt`, `system_generated_password`, `sugar_login`, `last_name`, `external_auth_only`, `date_entered`, `created_by`, `status`, `portal_only`, `employee_status`, `preferred_locale`)
VALUES
	('1', 'superadmin', '$argon2id$v=19$m=65536,t=4,p=1$dQM3eQU8w28FiMXd3P9Fbg$2e9DAmNNflejVYqW5IkVnZI8rTp1Sps8/rNRyOx3gig', 'fb2a442bdea250945a3f7b8fd104747aca24578b', 0, 1, 'Administrator', 0, NOW(), '1', 'Active', 0, 'Active', 'en_BE');

INSERT INTO `acl_roles`
    (`id`, `date_entered`, `date_modified`, `modified_user_id`, `created_by`, `name`, `description`, `code`)
VALUES
    ('4ce5831a-a5d1-0458-5fd2-5819abc9bd7d','2016-11-02 09:03:03','2017-02-27 14:22:30',NULL,'1','Default','This is the default role that every user needs','ROLE_USER'),
    ('e2acbb8b-8c2b-4d98-a370-32642198b067','2019-11-07 12:00:00','2019-11-07 12:00:00','1','1','Admin role','The role you need to be an admin','ROLE_ADMIN');

INSERT INTO `acl_roles_users`
    (`acl_role_id`, `user_id`)
VALUES
    ('4ce5831a-a5d1-0458-5fd2-5819abc9bd7d', '1'),
    ('e2acbb8b-8c2b-4d98-a370-32642198b067', '1');
