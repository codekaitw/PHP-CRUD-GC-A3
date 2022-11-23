CREATE TABLE Profile(
                            id int(11) NOT NULL auto_increment primary key,
                            profile_fname varchar(100),
                            profile_lname varchar(100),
                            profile_phone varchar(100),
                            profile_email varchar(100),
                            profile_job_position varchar(100),
                            profile_bio_text varchar(255),
                            profile_img_name varchar(255),
                            profile_img_path varchar(255),
                            created varchar(100),
                            modified varchar(100),
                            deleted varchar(100)
)
    ENGINE = InnoDB default charset = utf8mb4;