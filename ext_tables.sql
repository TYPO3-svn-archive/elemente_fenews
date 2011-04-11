#
# Table structure for table 'tt_news'
#
CREATE TABLE tt_news (
	tx_elementefenews_feuser int(11) DEFAULT '0' NOT NULL,
	tx_elementefenews_fegroup text DEFAULT '' NOT NULL,
	tx_elementefenews_author tinyint(4) DEFAULT '0' NOT NULL,
);