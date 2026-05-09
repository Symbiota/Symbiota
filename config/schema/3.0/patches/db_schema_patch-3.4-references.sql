CREATE TABLE referenceobject_new (
    refid INT(11) NOT NULL AUTO_INCREMENT,
    bibliographicCitation VARCHAR(255),
    identifier VARCHAR(100),
    title VARCHAR(255),
    creator VARCHAR(255),
    date VARCHAR(45),
    source VARCHAR(255),
    description VARCHAR(5000),
    subject VARCHAR(255),
    language VARCHAR(45),
    rights VARCHAR(255),
    type VARCHAR(100),
    taxonRemarks VARCHAR(255),
    datasetID VARCHAR(255),
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

-- DROP OLD FOREIGN KEYS

ALTER TABLE referenceauthorlink
DROP FOREIGN KEY FK_refauthlink_refid;

ALTER TABLE referencechklsttaxalink
DROP FOREIGN KEY FK_refchktaxalink_ref;

ALTER TABLE referencecollectionlink
DROP FOREIGN KEY FK_refcollectionlink_refid;

ALTER TABLE referencechecklistlink
DROP FOREIGN KEY FK_refchecklistlink_refid;

ALTER TABLE referenceoccurlink
DROP FOREIGN KEY FK_refoccurlink_refid;

ALTER TABLE omoccurrencetypes
DROP FOREIGN KEY FK_occurtype_refid;

ALTER TABLE referencetaxalink
DROP FOREIGN KEY FK_reftaxalink_refid;

ALTER TABLE referencedatasetlink
DROP FOREIGN KEY FK_refdatasetlink_refid;


RENAME TABLE
    referenceobject TO referenceobject_old,
    referenceobject_new TO referenceobject;


ALTER TABLE referenceauthorlink
ADD CONSTRAINT FK_refauthlink_refid
FOREIGN KEY (refid)
REFERENCES referenceobject(refid)
ON DELETE CASCADE
ON UPDATE CASCADE;

ALTER TABLE referencechklsttaxalink
ADD CONSTRAINT FK_refchktaxalink_ref
FOREIGN KEY (refid)
REFERENCES referenceobject(refid)
ON DELETE CASCADE
ON UPDATE CASCADE;

ALTER TABLE referencecollectionlink
ADD CONSTRAINT FK_refcollectionlink_refid
FOREIGN KEY (refid)
REFERENCES referenceobject(refid)
ON DELETE CASCADE
ON UPDATE CASCADE;

ALTER TABLE referencechecklistlink
ADD CONSTRAINT FK_refchecklistlink_refid
FOREIGN KEY (refid)
REFERENCES referenceobject(refid)
ON DELETE CASCADE
ON UPDATE CASCADE;

ALTER TABLE referenceoccurlink
ADD CONSTRAINT FK_refoccurlink_refid
FOREIGN KEY (refid)
REFERENCES referenceobject(refid)
ON DELETE CASCADE
ON UPDATE CASCADE;

ALTER TABLE omoccurrencetypes
ADD CONSTRAINT FK_occurtype_refid
FOREIGN KEY (refid)
REFERENCES referenceobject(refid)
ON DELETE CASCADE
ON UPDATE CASCADE;

ALTER TABLE referencetaxalink
ADD CONSTRAINT FK_reftaxalink_refid
FOREIGN KEY (refid)
REFERENCES referenceobject(refid)
ON DELETE CASCADE
ON UPDATE CASCADE;

ALTER TABLE referencedatasetlink
ADD CONSTRAINT FK_refdatasetlink_refid
FOREIGN KEY (refid)
REFERENCES referenceobject(refid)
ON DELETE CASCADE
ON UPDATE CASCADE;

DROP TABLE referenceobject_old;