CREATE TABLE IF NOT EXISTS `#__plg_easyfrontendseo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `title` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `keywords` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `generator` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `robots` tinytext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;