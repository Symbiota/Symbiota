#Changelog

All notable changes to this project will be documented in this file.

## [3.1] - 2024-08-29

### New Feature

- 508 Accessibility features
- Extended occurrence data upload module. Supports import and linking occurrence to: associated occurrences, identifications/annotations, image links, material samples, and reference links
- LeafLet JavaScript added as an open source mapping option
- MediaWiki parser for mapping and integration of Flora of North American descriptions into taxon profile pages
- Occurrence associations management tools
- Occurrence distribution static map thumbnail generator
- Public occurrence search form - new streamline version (via NEON support), including trait-based and material-sample-based search for portals with those features enabled
- Responsive design framework
- Specimen image batch tagging tool
- New quicksearch box in the collection profile

### Changed

- Bionomia badges and GBIF citation counts/links added to collection profile pages
- Glossary improvements including ability to link terms into identification keys
- Identification Key user interface improved to including display of taxa as thumbnails
- Significant extension of Spanish and French translation files, including refactor of how header, footer, and index language are integrated
- Support for new Occurrence fields: waterBody, continent, islandGroup, island, countryCode, locationID, eventID, vitality, eventDateEnd
- Restructure css files, including removal of deprecated and redundant stylings
- Taxon harvesting tool reconfigured to import directly from ChecklistBank API rather than Catalog of Life
- Security patches
  ** Update TinyMCE JS library
  ** Update jQuery JS libraries
  ** Cross-site scripting (XSS) protections - Improve sanitation of input variables to protect against
  ** SQL Injection protections - Improved prepared statements support

## [3.0] - 2022-01-14

### Added

- Integrate additional identifiers table into occurrence management and publishing tools (table: omoccuridentifiers)
- Integrate Material Sample module into occurrence editor, public display, import, and export tools

### Changed

- Add coordinate verification tool to editor using GBIF reverse geocode REST service

## [1.2.0] - 2022-01-13

### Added

- Occurrence dataset management toolkits (available via My Profile => Occurrence Management) (2020-12-23)
- New module for creating custom Specimen Labels formats linked to portal, collection, or user
- Option to link multiple ResponsibleParty associations to a collection (e.g. collection contacts) (2020-12-21)
- Option to associate multiple reference links to a collection (2020-12-21)
- Support tables for defining Controlled Vocabularies
- Paleo data management module including support for: editor, import, export, public display
- Establishing and managing relationships between occurrences (e.g. parasite/host)
- Occurrence Traits management tools

### Changed

## [1.1.0] - 2015-12-29

### Added

### Changed

[![version](https://img.shields.io/badge/Symbiota-v1.2.0.1.202201-blue.svg)](https://semver.org)
