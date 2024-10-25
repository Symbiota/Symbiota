INSERT INTO `schemaversion` (versionnumber) values ("3.2");

DROP TRIGGER specprocessorrawlabelsfulltext_insert
DROP TRIGGER specprocessorrawlabelsfulltext_update
DROP TRIGGER specprocessorrawlabelsfulltext_delete
DROP TABLE specprocessorawlabelsfulltext;

-- Is This even needed if we aren't using it currently?
-- ALTER TABLE specprocessorrawlabels ADD FULLTEXT INDEX(rawstr);
