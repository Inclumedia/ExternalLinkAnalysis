BEGIN;

CREATE TABLE /*_*/link_desc(
-- Primary key
ld_id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
-- URL index
ld_url_index varchar(255) DEFAULT NULL,
-- URL
ld_url blob NOT NULL,
-- URL description
ld_desc blob DEFAULT NULL
)/*$wgDBTableOptions*/;

CREATE INDEX /*i*/ld_url_index ON /*_*/link_desc (ld_url_index);
COMMIT;