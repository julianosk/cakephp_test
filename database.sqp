CREATE TABLE IF NOT EXISTS posts (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(50) DEFAULT NULL,
  body text,
  created datetime DEFAULT NULL,
  modified datetime DEFAULT NULL,
  user_id int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

CREATE TABLE IF NOT EXISTS users (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(50) DEFAULT NULL,
  password varchar(255) DEFAULT NULL,
  role varchar(20) DEFAULT NULL,
  created datetime DEFAULT NULL,
  modified datetime DEFAULT NULL,
  attempts int(11) NOT NULL,
  last_attempt datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;