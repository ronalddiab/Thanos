@author - Vivek Doshi
@mail -vivek.doshi@sparsh-technologies.co.in
@skype- tatva60
--------------installating the product module--------------

1)Paste the following querries in your database. It will create all the required table for this module


   CREATE TABLE `forum_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL COMMENT 'PK of forum_categories',
  `lang_id` int(11) NOT NULL DEFAULT '1',
  `forum_post_title` varchar(255) NOT NULL DEFAULT '',
  `slug_url` varchar(50) NOT NULL,
  `forum_post_text` text NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0' COMMENT '(0) no (1) yes',
  `attachment` varchar(255) DEFAULT NULL COMMENT 'attched file path',
  `status` tinyint(1) NOT NULL COMMENT '(-1:delete , 1:active  and  2:inactive)',
  `created_on` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) NOT NULL,
  `deleted_on` datetime NOT NULL,
  `deleted_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE `forum_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL COMMENT 'PK of forum_post',
  `lang_id` int(11) NOT NULL DEFAULT '1',
  `topic_title` varchar(255) NOT NULL,
  `topic_text` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '(-1:delete 1:active 2: inactive)',
  `attachment` varchar(255) DEFAULT NULL COMMENT 'attched file path',
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


2) Copy the folder in the following directory
    "project_name\application\modules\"
   If you cant find it, create a folder with name "module" in the application directory.

3)Create a folder named "uploads" in "project_name\assets\" directory. Then create a folder named "forum_files".
  If "uploads" already exits then just create folder "forum_files".This is for file upload.

4)Create a directory named "products" in "\project_name\themes\default\js\modules" and put "jquery.slugify.js" in that directory.
If any folder is not there , create the hierarchy accordingly.

Product module is ready to use.