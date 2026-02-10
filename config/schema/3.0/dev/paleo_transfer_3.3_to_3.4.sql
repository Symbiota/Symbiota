#Autopopulate earlyInterval and lateInterval
UPDATE omoccurpaleo
  SET
    earlyInterval = COALESCE(
      NULLIF(stage, ''),
      NULLIF(epoch, ''),
      NULLIF(period, ''),
      NULLIF(era, ''),
      NULLIF(eon, '')
    ),
    lateInterval = COALESCE(
      NULLIF(stage, ''),
      NULLIF(epoch, ''),
      NULLIF(period, ''),
      NULLIF(era, ''),
      NULLIF(eon, '')
    )
  WHERE
    (earlyInterval IS NULL OR earlyInterval = '')
    AND
    (lateInterval IS NULL OR lateInterval = '')
    AND
    COALESCE(
      NULLIF(stage, ''),
      NULLIF(epoch, ''),
      NULLIF(period, ''),
      NULLIF(era, ''),
      NULLIF(eon, '')
    ) IS NOT NULL;

UPDATE omoccurpaleo
  SET
    earlyInterval = CASE
      WHEN (earlyInterval IS NULL OR earlyInterval = '')
          AND (lateInterval IS NOT NULL AND lateInterval != '')
      THEN lateInterval
      ELSE earlyInterval
    END,
    lateInterval = CASE
      WHEN (lateInterval IS NULL OR lateInterval = '')
          AND (earlyInterval IS NOT NULL AND earlyInterval != '')
      THEN earlyInterval
      ELSE lateInterval
    END;