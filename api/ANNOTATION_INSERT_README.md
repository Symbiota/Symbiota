# Occurrence Annotation Insert Method

## Overview

The `insertAnnotation` method in `OccurrenceAnnotationController` has been implemented to handle external occurrence annotations, particularly from systems like VoucherVision. It uses Eloquent ORM where possible and follows the same logic pattern as the provided SQL example.

## Endpoint

```
POST /api/v2/occurrence/annotation/insert
```

## Parameters

### Query Parameters
- `apiToken` (required): API security token for authentication
- `occid` (required): Internal occurrence identifier
- `externalSource` (optional): Source of annotation (e.g., "VoucherVision")
- `externalEditor` (optional): External editor/agent responsible
- `externalTimestamp` (optional): When external annotation was created
- `appliedStatus` (optional): Whether to apply changes (0=not applied, 1=applied, default: 0)

### Request Body
JSON object with field name/value pairs directly:
```json
{
    "catalogNumber": "ZRH 546 MVZ192273",
    "collector": "G. B. Rossbach",
    "associatedCollectors": "",
    "collectorNumber": "7390",
    "verbatimCollectionDate": "3/26/67",
    "collectionDate": "1967-03-26",
    "scientificName": "Opuntia humifusa",
    "scientificNameAuthorship": "Raf.",
    "family": "Cactaceae",
    "genus": "Opuntia",
    "specificEpithet": "humifusa",
    "infraspecificEpithet": "",
    "identifiedBy": "",
    "identifiedConfidence": "",
    "identifiedDate": "",
    "identifiedRemarks": "",
    "continent": "North America",
    "country": "United States",
    "stateProvince": "West Virginia",
    "county": "Pendleton",
    "locality": "Local colonies on dark, elongate-fractured shale slope above road, with local Phlox subulata, & Juniperus virginiana, Pinus virginiana, Quercus spp., w. edge of flat by Moorefield River, Rt. 33, 1 mi. s. of Oak Flat,",
    "decimalLatitude": "",
    "decimalLongitude": "",
    "verbatimCoordinates": "",
    "datum": "",
    "verbatimElevation": "",
    "cultivated": "",
    "habitat": "",
    "specimenDescription": "Pads 6x5 cm. to 14x8 cm. when alive.",
    "associatedSpecies": "Phlox subulata, Juniperus virginiana, Pinus virginiana, Quercus spp.",
    "additionalText": "PLANTS OF WEST VIRGINIA\n(~~~0. Rafinesquii. Engelm.~~~)"
}
```

## Field Mapping

The method includes automatic field name mapping for common variations:
- `collector` → `recordedBy`
- `collectionDate` → `eventDate`
- `verbatimCollectionDate` → `verbatimEventDate`
- `specimenDescription` → `occurrenceRemarks`
- `additionalText` → `fieldNotes`
- `cultivated` → `cultivationStatus`
- `datum` → `geodeticDatum`

## Implementation Features

1. **Eloquent Usage**: Uses Eloquent models where possible for data access and updates
2. **Field Validation**: Only processes fields that exist in the Occurrence model's fillable array
3. **Change Detection**: Only stores fields that actually differ from current values
4. **Authorization**: Checks user permissions for the target collection
5. **Revision Tracking**: Stores old and new values in `omoccurrevisions` table
6. **Conditional Application**: Can optionally apply changes to main occurrence record
7. **Error Handling**: Comprehensive error handling with meaningful responses

## Response

Success response (201):
```json
{
    "status": "SUCCESS",
    "message": "Annotation successfully submitted and applied to occurrence record",
    "occid": 12345,
    "fieldsChanged": 5,
    "appliedStatus": 1
}
```

Error response (400/401/404/500):
```json
{
    "error": "Error description"
}
```

## Database Tables

- **Read**: `omoccurrences` (for current values and validation)
- **Insert**: `omoccurrevisions` (for annotation history)
- **Update**: `omoccurrences` (if appliedStatus=1)