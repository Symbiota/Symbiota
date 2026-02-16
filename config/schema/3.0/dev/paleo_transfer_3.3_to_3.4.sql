#Set the early and late intervals to correct gtsterm if present
UPDATE omoccurpaleo p
INNER JOIN omoccurpaleogts g
  ON g.gtsterm = COALESCE(
        NULLIF(p.stage, ''),
        NULLIF(p.epoch, ''),
        NULLIF(p.period, ''),
        NULLIF(p.era, ''),
        NULLIF(p.eon, '')
     )
SET
  p.earlyInterval = g.gtsterm,
  p.lateInterval  = g.gtsterm
WHERE
  (p.earlyInterval IS NULL OR p.earlyInterval = '')
  AND
  (p.lateInterval IS NULL OR p.lateInterval = '');


#Fill earlyInterval from lateInterval
UPDATE omoccurpaleo
SET earlyInterval = lateInterval
WHERE (earlyInterval IS NULL OR earlyInterval = '')
  AND (lateInterval IS NOT NULL AND lateInterval != '');

#Fill lateInterval from earlyInterval
UPDATE omoccurpaleo
SET lateInterval = earlyInterval
WHERE (lateInterval IS NULL OR lateInterval = '')
  AND (earlyInterval IS NOT NULL AND earlyInterval != '');

#Store mismatched terms in stratRemarks
UPDATE omoccurpaleo
SET stratRemarks = CONCAT_WS(
  '; ',
  stratRemarks,
  CONCAT(
    'VERBATIM CHRONOSTRATIGRAPHY: ',
    COALESCE(
      NULLIF(stage, ''),
      NULLIF(epoch, ''),
      NULLIF(period, ''),
      NULLIF(era, ''),
      NULLIF(eon, '')
    )
  )
)
WHERE
  (earlyInterval IS NULL OR earlyInterval = '')
  AND COALESCE(
        NULLIF(stage, ''),
        NULLIF(epoch, ''),
        NULLIF(period, ''),
        NULLIF(era, ''),
        NULLIF(eon, '')
      ) IS NOT NULL;

#Set collections colltype based on dynamic property value
UPDATE omcollections
SET collType = 'Fossil Specimens'
WHERE JSON_SEARCH(dynamicproperties, 'one', '1', NULL, '$.editorProps."modules-panel"[*].paleo.status') IS NOT NULL;
