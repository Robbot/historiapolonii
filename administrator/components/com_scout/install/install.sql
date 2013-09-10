-- -----------------------------------------------------
-- Table `#__scout_config`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `#__scout_config` (
  `config_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `config_name` VARCHAR(255) NOT NULL ,
  `value` TEXT NOT NULL ,
  PRIMARY KEY (`config_id`) )
TYPE = MyISAM
DEFAULT CHARACTER SET = utf8;

-- --------------------------------------------------------
-- Table structure for table `#__scout_logs`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__scout_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL COMMENT 'Always in GMT',
  `subject_id` int(11) NOT NULL,
  `verb_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `subject_id` (`subject_id`),
  KEY `object_id` (`object_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- Table structure for table `#__scout_objects`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__scout_objects` (
  `object_id` int(11) NOT NULL AUTO_INCREMENT,
  `object_name` varchar(255) NOT NULL,
  `scope_id` int(11) NOT NULL,
  `object_value` text NOT NULL,
  PRIMARY KEY (`object_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- Table structure for table `#__scout_scopes`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__scout_scopes` (
  `scope_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` tinyint(1) NOT NULL COMMENT '0=site, 1=administrator',
  `scope_name` varchar(255) NOT NULL COMMENT 'Plain English name for the scope',
  `scope_identifier` varchar(255) NOT NULL COMMENT 'String unique ID for the scope',
  `scope_url` text NOT NULL,
  PRIMARY KEY (`scope_id`),
  KEY `scope_identifier` (`scope_identifier`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- Table structure for table `#__scout_subjects`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__scout_subjects` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(255) NOT NULL,
  `subjecttype_id` int(11) NOT NULL,
  `subject_value` text NOT NULL,
  PRIMARY KEY (`subject_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- Table structure for table `#__scout_subjecttypes`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__scout_subjecttypes` (
  `subjecttype_id` int(11) NOT NULL AUTO_INCREMENT,
  `subjecttype_name` varchar(255) NOT NULL,
  `subjecttype_value` text NOT NULL,
  `site_url` text NOT NULL,
  `admin_url` text NOT NULL,
  PRIMARY KEY (`subjecttype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Index of subject types, such as user, plugin, cronjob, etc';

-- --------------------------------------------------------
-- Dumping data for table `#__scout_subjecttypes`
-- --------------------------------------------------------

INSERT IGNORE INTO `#__scout_subjecttypes` (`subjecttype_id`, `subjecttype_name`, `subjecttype_value`, `site_url`, `admin_url`) VALUES
(1, 'A User', 'user', '', 'index.php?option=com_users&view=user&task=edit&cid[]='),
(2, 'A Plugin', 'plugin', '', ''),
(3, 'A CronJob', 'cronjob', '', '');

-- --------------------------------------------------------
-- Table structure for table `#__scout_verbs`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__scout_verbs` (
  `verb_id` int(11) NOT NULL AUTO_INCREMENT,
  `verb_name` varchar(255) NOT NULL,
  `verb_value` text NOT NULL,
  PRIMARY KEY (`verb_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

