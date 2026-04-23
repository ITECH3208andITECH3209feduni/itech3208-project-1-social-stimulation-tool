CREATE TABLE IF NOT EXISTS `#__spidervideoplayer_playlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `thumb` varchar(200) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `videos` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__spidervideoplayer_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `required` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__spidervideoplayer_theme` (
   `id` int(11) NOT NULL AUTO_INCREMENT,  `default` int(2) NOT NULL,  `title` varchar(256) NOT NULL,  `appWidth` int(11) NOT NULL,  `appHeight` int(11) NOT NULL,  `playlistWidth` int(11) NOT NULL,  `startWithLib` tinyint(1) NOT NULL,  `autoPlay` tinyint(1) NOT NULL,  `autoNext` tinyint(1) NOT NULL,  `autoNextAlbum` tinyint(1) NOT NULL,  `defaultVol` double NOT NULL,  `defaultRepeat` varchar(20) NOT NULL,  `defaultShuffle` varchar(20) NOT NULL,  `autohideTime` int(11) NOT NULL,  `centerBtnAlpha` double NOT NULL,  `loadinAnimType` tinyint(4) NOT NULL,  `keepAspectRatio` tinyint(1) NOT NULL,  `clickOnVid` tinyint(1) NOT NULL,  `spaceOnVid` tinyint(1) NOT NULL,  `mouseWheel` tinyint(1) NOT NULL,  `ctrlsPos` tinyint(4) NOT NULL,  `ctrlsStack` text NOT NULL,  `ctrlsOverVid` tinyint(1) NOT NULL,  `ctrlsSlideOut` tinyint(1) NOT NULL,  `watermarkUrl` varchar(255) NOT NULL,  `watermarkPos` tinyint(4) NOT NULL,  `watermarkSize` int(11) NOT NULL,  `watermarkSpacing` int(11) NOT NULL,  `watermarkAlpha` double NOT NULL,  `playlistPos` int(11) NOT NULL,  `playlistOverVid` tinyint(1) NOT NULL,  `playlistAutoHide` tinyint(1) NOT NULL,  `playlistTextSize` int(11) NOT NULL,  `libCols` int(11) NOT NULL,  `libRows` int(11) NOT NULL,  `libListTextSize` int(11) NOT NULL,  `libDetailsTextSize` int(11) NOT NULL,  `appBgColor` varchar(16) NOT NULL,  `vidBgColor` varchar(16) NOT NULL,  `framesBgColor` varchar(16) NOT NULL,  `ctrlsMainColor` varchar(16) NOT NULL,  `ctrlsMainHoverColor` varchar(16) NOT NULL,  `slideColor` varchar(16) NOT NULL,  `itemBgHoverColor` varchar(16) NOT NULL,  `itemBgSelectedColor` varchar(16) NOT NULL,  `textColor` varchar(16) NOT NULL,  `textHoverColor` varchar(16) NOT NULL,  `textSelectedColor` varchar(16) NOT NULL,  `framesBgAlpha` double NOT NULL,  `ctrlsMainAlpha` double NOT NULL,  `itemBgAlpha` double NOT NULL,  `show_trackid` tinyint(1) NOT NULL,  `openPlaylistAtStart` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__spidervideoplayer_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(200) NOT NULL,
  `urlHtml5` varchar(255) NOT NULL,
  `urlHD` varchar(200) NOT NULL,
  `urlHdHtml5` varchar(255) NOT NULL,
  `thumb` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  `published` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `fmsUrl` varchar(256) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `#__spidervideoplayer_theme` (`id`, `default`, `title`, `appWidth`, `appHeight`, `playlistWidth`, `startWithLib`, `autoPlay`, `autoNext`, `autoNextAlbum`, `defaultVol`, `defaultRepeat`, `defaultShuffle`, `autohideTime`, `centerBtnAlpha`, `loadinAnimType`, `keepAspectRatio`, `clickOnVid`, `spaceOnVid`, `mouseWheel`, `ctrlsPos`, `ctrlsStack`, `ctrlsOverVid`, `ctrlsSlideOut`, `watermarkUrl`, `watermarkPos`, `watermarkSize`, `watermarkSpacing`, `watermarkAlpha`, `playlistPos`, `playlistOverVid`, `playlistAutoHide`, `playlistTextSize`, `libCols`, `libRows`, `libListTextSize`, `libDetailsTextSize`, `appBgColor`, `vidBgColor`, `framesBgColor`, `ctrlsMainColor`, `ctrlsMainHoverColor`, `slideColor`, `itemBgHoverColor`, `itemBgSelectedColor`, `textColor`, `textHoverColor`, `textSelectedColor`, `framesBgAlpha`, `ctrlsMainAlpha`, `itemBgAlpha`, `show_trackid`, `openPlaylistAtStart`) VALUES(1, 1, 'Theme 1', 640, 480, 100, 0, 0, 0, 0, 50, 'repeatOff', 'shuffleOff', 5, 50, 2, 1, 1, 1, 1, 2, 'playlist:1,lib:1,playPrev:1,playPause:1,playNext:1,stop:0,time:1,vol:1,+:1,hd:1,repeat:1,shuffle:1,play:0,pause:0,share:1,fullScreen:1', 1, 0, '', 1, 0, 0, 50, 2, 0, 0, 12, 3, 3, 16, 20, '001326', '001326', '3665A3', 'C0B8F2', '000000', '00A2FF', 'DAE858', '0C8A58', 'DEDEDE', '000000', 'FFFFFF', 50, 79, 50, 1, 0),(2, 0, 'Theme 2', 640, 480, 120, 0, 0, 0, 0, 50, 'repeatOff', 'shuffleOff', 5, 50, 2, 1, 1, 1, 1, 2, 'playPrev:1,play:1,playNext:1,stop:0,playlist:1,lib:1,playPause:0,vol:1,+:1,time:1,hd:1,repeat:1,shuffle:1,pause:0,share:1,fullScreen:1', 1, 1, '', 1, 0, 0, 50, 1, 0, 1, 12, 3, 3, 16, 20, 'FFBB00', '001326', 'FFA200', '030000', '595959', 'FF0000', 'E8E84D', 'FF5500', 'EBEBEB', '000000', 'FFFFFF', 82, 79, 0, 1, 0),(3, 0, 'Theme 3', 640, 480, 140, 0, 0, 0, 0, 50, 'repeatOff', 'shuffleOff', 5, 50, 2, 1, 1, 1, 1, 2, 'playPause:1,play:0,playlist:1,lib:1,playPrev:1,playNext:1,stop:0,vol:1,+:1,time:1,hd:1,repeat:1,shuffle:0,pause:0,share:1,fullScreen:1', 1, 1, '', 1, 0, 0, 50, 1, 1, 1, 12, 3, 3, 16, 20, 'FF0000', '070801', 'D10000', 'FFFFFF', '00A2FF', '00A2FF', 'F0FF61', '00A2FF', 'DEDEDE', '000000', 'FFFFFF', 65, 99, 0, 1, 0),(4, 0, 'Theme 4', 640, 480, 150, 0, 0, 0, 0, 50, 'repeatOff', 'shuffleOff', 5, 60, 2, 1, 1, 1, 1, 2, 'playPause:1,playlist:1,lib:1,vol:1,playPrev:0,playNext:0,stop:0,+:1,hd:1,repeat:1,shuffle:0,play:0,pause:0,share:1,time:1,fullScreen:1', 1, 1, '', 1, 0, 0, 50, 1, 1, 1, 14, 4, 4, 14, 16, '239DC2', '000000', '2E6DFF', 'F5DA51', 'FFA64D', 'BFBA73', 'FF8800', 'FFF700', 'FFFFFF', 'FFFFFF', '000000', 71, 82, 0, 1, 0),(5, 0, 'Theme 5', 640, 480, 140, 0, 0, 0, 0, 50, 'repeatOff', 'shuffleOff', 5, 50, 2, 1, 1, 1, 1, 2, 'playPrev:0,playPause:1,playlist:1,lib:1,playNext:0,stop:0,time:1,vol:1,+:1,hd:1,repeat:1,shuffle:1,play:0,pause:0,share:1,fullScreen:1', 1, 1, '', 1, 0, 0, 50, 1, 1, 1, 14, 4, 4, 14, 16, '878787', '001326', 'FFFFFF', '000000', '525252', '14B1FF', 'CCCCCC', '14B1FF', '030303', '000000', 'FFFFFF', 100, 75, 0, 1, 0),(6, 0, 'Theme 6', 640, 480, 120, 0, 0, 0, 0, 50, 'repeatOff', 'shuffleOff', 5, 50, 2, 1, 1, 1, 1, 2, 'playPause:1,playlist:1,lib:1,vol:1,playPrev:0,playNext:0,stop:0,+:1,repeat:0,shuffle:0,play:0,pause:0,hd:1,share:1,time:1,fullScreen:1', 1, 1, '', 1, 0, 0, 50, 1, 1, 1, 14, 3, 3, 16, 16, '080808', '000000', '1C1C1C', 'FFFFFF', '40C6FF', '00A2FF', 'E8E8E8', '40C6FF', 'DEDEDE', '2E2E2E', 'FFFFFF', 61, 79, 0, 1, 0),(7, 0, 'Theme  7', 640, 480, 120, 0, 0, 0, 0, 50, 'repeatOff', 'shuffleOff', 5, 50, 2, 1, 1, 1, 1, 2, 'playPause:1,playlist:1,lib:1,playPrev:0,playNext:0,stop:0,vol:1,+:1,hd:0,repeat:0,shuffle:0,play:0,pause:0,share:1,fullScreen:1,time:1', 1, 1, '', 1, 0, 0, 50, 1, 1, 1, 12, 3, 3, 16, 16, '212121', '000000', '222424', 'FFCC00', 'FFFFFF', 'ABABAB', 'B8B8B8', 'EEFF00', 'DEDEDE', '000000', '000000', 90, 78, 0, 1, 0);
INSERT INTO `#__spidervideoplayer_playlist` (`id`, `title`, `thumb`, `published`, `videos`) VALUES
(1, 'Nature', '../media/com_spidervideoplayer/upload/sunset4.jpg', 1, '1,2,');

INSERT INTO `#__spidervideoplayer_tag` (`id`, `name`, `required`, `published`, `ordering`) VALUES
(1, 'Year', 1, 1, 2),
(2, 'Genre', 1, 1, 1);

INSERT INTO `#__spidervideoplayer_video` (`id`, `url`, `urlHD`, `thumb`, `title`, `published`, `type`, `fmsUrl`, `params`) VALUES
(1, 'http://www.youtube.com/watch?v=eaE8N6alY0Y', '', '../media/com_spidervideoplayer/upload/red-sunset-casey1.jpg', 'Sunset 1', 1, 'youtube', '', '2#===#Nature#***#1#===#2012#***#'),
(2, 'http://www.youtube.com/watch?v=y3eFdvDdXx0', '', '../media/com_spidervideoplayer/upload/sunset10.jpg', 'Sunset 2', 1, 'youtube', '', '2#===#Nature#***#1#===#2012#***#');
