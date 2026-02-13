#Set collections colltype based on dynamic property value
UPDATE omcollections
SET collType = 'Fossil Specimens'
WHERE JSON_SEARCH(dynamicproperties, 'one', '1', NULL, '$.editorProps."modules-panel"[*].paleo.status') IS NOT NULL;