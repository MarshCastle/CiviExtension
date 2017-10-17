DROP TABLE IF EXISTS `civicrm_extension_settings`;

CREATE TABLE `civicrm_extension_settings` (
  `id` int(10) unsigned 
    NOT NULL 
    AUTO_INCREMENT  
    COMMENT 'Setting for extra Summary display elements',
  `name`  varchar(255)   NOT NULL COMMENT 'unique name for setting',
  `value` varchar(255)   NOT NULL COMMENT 'data associated with setting', 
  PRIMARY KEY ( `id` ),
    UNIQUE INDEX `UI_name`(name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
