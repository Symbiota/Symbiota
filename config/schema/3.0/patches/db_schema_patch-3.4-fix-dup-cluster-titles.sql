-- CURRENT: title varchar(50) NOT NULL
-- While unlikely possible combination of omocurrences
-- recordedBy(varchar(255)) + ' '  + recordNumber(varchar(45)) + ' ' eventDate(10 chars after string conversion)
-- would result in ~312 chars maximum which breaks this fields maximum characters
ALTER TABLE omoccurduplicates MODIFY title TEXT NOT NULL;
