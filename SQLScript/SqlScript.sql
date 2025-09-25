USE blog_db;
INSERT INTO `role` (`id`, `name`) 
VALUES (
	UNHEX(REPLACE('82915cb3-5ca1-4e2c-9a70-8da851cf37ea', '-', '')), 
    'USER'
);