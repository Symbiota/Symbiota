
CREATE TABLE referenceobject_new (
    refid INT(11) NOT NULL AUTO_INCREMENT,
    bibliographicCitation VARCHAR(1000),
    identifier VARCHAR(100),
    title VARCHAR(255),
    creator VARCHAR(255),
    date VARCHAR(45),
    source VARCHAR(255),
    description TEXT,
    subject VARCHAR(255),
    language VARCHAR(45),
    rights VARCHAR(255),
    type VARCHAR(100),
    taxonRemarks TEXT,
    datasetID INT(11),
    url VARCHAR(255),
    modifiedByUid INT(11),
    modifiedTimestamp DATETIME,
    initialTimestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (refid)
);

INSERT INTO referenceobject_new (
    refid,
    title,
    creator,
    date,
    source,
    bibliographicCitation,
    identifier,
    url,
	modifiedByUid,
    modifiedTimestamp,
    initialTimestamp
)
SELECT
    refid,
    title,
    cheatauthors,
    pubdate,
    secondarytitle,
    cheatcitation,
    guid,
    url,
    modifieduid,
    modifiedtimestamp,
    initialtimestamp
FROM referenceobject;

SET FOREIGN_KEY_CHECKS = 0;

RENAME TABLE referenceobject TO referenceobject_old,
             referenceobject_new TO referenceobject;
             
SET FOREIGN_KEY_CHECKS = 1;

