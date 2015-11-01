DROP TABLE IF EXISTS `bugs`;

CREATE TABLE `bugs` (
  `id` binary(36) NOT NULL DEFAULT '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `summary` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `expected` text COLLATE utf8_unicode_ci NOT NULL,
  `actual` text COLLATE utf8_unicode_ci NOT NULL,
  `date_reported` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `requirements`;

CREATE TABLE `requirements` (
  `id` binary(36) NOT NULL DEFAULT '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `story` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `priority` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `is_a_theme` tinyint(1) DEFAULT '0',
  `estimate` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `date_recorded` datetime NOT NULL,
  `parent` binary(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `triage`;

CREATE TABLE `triage` (
  `id` binary(36) NOT NULL DEFAULT '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `table_link` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `requirement_id` binary(36) DEFAULT NULL,
  `bug_id` binary(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requirement_id` (`requirement_id`),
  KEY `bug_id` (`bug_id`),
  CONSTRAINT `triage_ibfk_1` FOREIGN KEY (`requirement_id`) REFERENCES `requirements` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `triage_ibfk_2` FOREIGN KEY (`bug_id`) REFERENCES `bugs` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;