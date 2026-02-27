import { CollectionSearchPage } from './pages/CollectionSearch';
import { test as base, Taxon, type Occurrence } from './fixtures/occurrence';

enum TaxonType {
	ScientificName = '2',
	Family= '3',
	TaxonGroup= '4',
	Common= '5',
}

enum AssociationType {
	NotSpecified = 'none',
	Any = 'any'
}

const test =  base.extend<{ collectionSearchPage: CollectionSearchPage }>({
	collectionSearchPage: async ({ page, occurrenceFactory, collId }, use) => {
		await occurrenceFactory.getNewRecord(collId)
		const searchPage = CollectionSearchPage.make(page);
		await searchPage.goto();
		await searchPage.expandAll();
		await searchPage.setAllCollections(false);
		await searchPage.selectCollection(collId);
		await use(searchPage);
	}
});

// TODO Current testing is wip, but want to get commited

// Running Searches in parallel runs into lots of false negatives
test.describe.configure({ mode: 'serial' });

interface OccurrenceSearchTest {
	name: string,
	fields: Object,
	occurrences: Array<Occurrence>,
	taxa?: Array<Taxon>,
	taxaLink?: Array<number>,
}

let tests: Array<OccurrenceSearchTest> = [
	{
		name: 'Scientific Name',
		fields: {
			taxa: 'Genus Species',
			taxontype: TaxonType.ScientificName,
			usethes: false,
		},
		occurrences: [
			{ sciname: 'Genus Species' },
			{ sciname: 'Dont Match' }
		],
	},
	{
		name: 'Family',
		fields: {
			taxa: 'family',
			taxontype: TaxonType.Family,
			usethes: false,
		},
		occurrences: [
			{ family: 'family' },
			{ family: 'dontmatch' }
		]
	},
	/*  Requires taxa seeding
	 * {
		name: 'Taxon Group',
		fields: {
			taxa: 'tgroup',
			taxontype: TaxonType.TaxonGroup,
			usethes: false,
		},
		occurrences: [
			{ family: 'tgroup' },
			{ family: 'dontmatch' }
		]
	},
	{
		name: 'Common Name',
		fields: {
			taxa: 'common',
			taxontype: TaxonType.Common,
			usethes: false,
		},
		occurrences: []
	},*/
	{
		name: 'Country',
		fields: {
			country: 'country',
		},
		occurrences: [
			{ country: 'country' },
			{ country: 'dontmatch' }
		]
	},
	{
		name: 'Locality',
		fields: {
			local: 'locality',
		},
		occurrences: [
			{ locality: 'locality' },
			{ locality: 'dontmatch' }
		]
	},
	{
		name: 'State',
		fields: {
			state: 'state',
		},
		occurrences: [
			{ stateProvince: 'state' },
			{ stateProvince: 'dontmatch' }
		]
	},
	{
		name: 'County',
		fields: {
			county: 'county',
		},
		occurrences: [
			{ county: 'county' },
			{ county: 'dontmatch' }
		]
	},
	{
		name: 'Min Elevation',
		fields: {
			elevlow: '15',
		},
		occurrences: [
			{ minimumElevationInMeters: 17 },
			{ minimumElevationInMeters: 1 }
		]
	},
	// Todo Issue Max Elevation Search Requires min elevation to be defined?
	{
		name: 'Max Elevation',
		fields: {
			elevhigh: '10',
		},
		occurrences: [
			{ maximumElevationInMeters: 9, minimumElevationInMeters: 0 },
			{ maximumElevationInMeters: 15, minimumElevationInMeters: 0 }
		]
	},
	{
		name: 'Bounding Box',
		fields: {
			upperlat: '39',
			upperlat_NS: 'N',
			bottomlat: '38',
			bottomlat_NS: 'N',
			leftlong: '122',
			leftlong_EW: 'W',
			rightlong: '121',
			rightlong_EW: 'W',
		},
		occurrences: [
			{ decimalLongitude: -121.5 , decimalLatitude: 38.5},
			{ decimalLongitude: -122.5 , decimalLatitude: 39.5},
		]
	},
	{
		name: 'Radius',
		fields: {
			pointlat: '38',
			pointlat_NS: 'N',
			pointlong: '121',
			pointlong_EW: 'W',
			radius: '5',
			radiusunits: 'km',
		},
		occurrences: [
			{ decimalLongitude: -121, decimalLatitude: 38},
			{ decimalLongitude: -125, decimalLatitude: 39},
		]
	},
	{
		name: 'Polygon',
		fields: {
			footprintGeoJson: '{"type":"Feature","properties":{},"geometry":{"type":"Polygon","coordinates":[[[-121.40441894531251,38.63662274572859],[-121.15997314453125,38.97707809911935],[-120.93750000000001,38.65379142966591],[-121.40441894531251,38.63662274572859]]]}}',
		},
		occurrences: [
			{ decimalLongitude: -121.2, decimalLatitude: 38.76},
			{ decimalLongitude: -121, decimalLatitude: 38.76},
		]
	},
	{
		name: 'Collection Start Date',
		fields: {
			eventdate1: '2000-01-01',
		},
		occurrences: [
			{ eventDate: '2000-01-01' },
			{ eventDate: '2025-01-01' }
		]
	},
	// Todo does do the search if not for range
	{
		name: 'Collection End Date',
		fields: {
			eventdate1: '2000-01-01',
			eventdate2: '2000-01-03',
		},
		occurrences: [
			{ eventDate: '2000-01-02' },
			{ eventDate: '2025-01-01' }
		]
	},
	{
		name: 'Collector',
		fields: {
			collector: 'collector',
		},
		occurrences: [
			{ recordedBy: 'collector' },
			{ recordedBy: 'notmatch' }
		]
	},
	{
		name: 'Collector Number',
		fields: {
			collnum: '1',
		},
		occurrences: [
			{ recordNumber: '1' },
			{ recordNumber: '5' }
		]
	},
	{
		name: 'Catalog Number',
		fields: {
			catnum: '11111',
			includeothercatnum: false
		},
		occurrences: [
			{ catalogNumber: '11111' },
			{ catalogNumber: '55555' }
		]
	},
	{
		name: 'Other Catalog Numbers/Guids',
		fields: {
			catnum: 'other11111',
			includeothercatnum: true
		},
		occurrences: [
			{ otherCatalogNumbers: 'other11111' },
			{ otherCatalogNumbers: '11111' }
		]
	},
	/* Requires Seeding of linked tables
		* {
		name: 'Specimens Only',
		fields: {
			typestatus: true,
		},
		occurrences: []
	},
	{
		name: 'Has Images Only',
		fields: {
			hasimages: true,
		},
		occurrences: []
	},
	{
		name: 'Has Audio Only',
		fields: {
			hasaudio: true,
		},
		occurrences: []
	},
	{
		name: 'Has Audio and Images',
		fields: {
			hasaudio: true,
			hasimages: true,
		},
		occurrences: []
	},
	{
		name: 'Has Genetic',
		fields: {
			hasgenetic: true,
		},
		occurrences: []
	},
	{
		name: 'Include cultivated',
		fields: {
			includecult: true,
		},
		occurrences: []
	},
	*/
	/* Requires seeding of associations
	{
		name: 'Associations Sciname/Not Specified',
		fields: {
			'association-type': AssociationType.NotSpecified,
			'associated-taxa': 'Genus Species',
			'usethes-associations': false
		},
		occurrences: []
	},
	{
		name: 'Associations Sciname/Any',
		fields: {
			'association-type': AssociationType.NotSpecified,
			'associated-taxa': 'Genus Species',
			'usethes-associations': false
		},
		occurrences: []
	},
	{
		name: 'Associations Sciname/Not Specified w/ Synonyms',
		fields: {
			'association-type': AssociationType.NotSpecified,
			'associated-taxa': 'Genus Species',
			'usethes-associations': false
		},
		occurrences: []
	},
	{
		name: 'Associations Sciname/Any w/ Synonyms',
		fields: {
			'association-type': AssociationType.NotSpecified,
			'associated-taxa': 'Genus Species',
			'usethes-associations': true 
		},
		occurrences: []
	},
	{
		name: 'Associations Family/Not Specified',
		fields: {
			'association-type': AssociationType.NotSpecified,
			'associated-taxa': 'Genus Species',
		},
		occurrences: []
	},
	{
		name: 'Associations Family/Any',
		fields: {
			'association-type': AssociationType.NotSpecified,
			'associated-taxa': 'Genus Species',
		},
		occurrences: []
	},
	Requires seeding of associations  */
];

test.describe('List Result', () => {
	for(let t of tests) {
		test(t.name, async ({ collectionSearchPage, occurrenceFactory, collId }) => {
			await occurrenceFactory.seedOccurrences(
				collId, 
				t.occurrences
			);
			await collectionSearchPage.searchForm.setMany(t.fields);
			await collectionSearchPage.setListResult();
			await collectionSearchPage.search();
			await collectionSearchPage.expectListResult();
			await collectionSearchPage.expectListCount(1);
		});
	}
})

/* Currently is failing because is broken
test.describe('Table Result', () => {
	for(let t of tests) {
		test(t.name, async ({ collectionSearchPage, occurrenceFactory, collId }) => {
			await occurrenceFactory.seedOccurrences(
				collId, 
				t.occurrences
			);
			await collectionSearchPage.searchForm.setMany(t.fields);
			await collectionSearchPage.setTableResult();
			await collectionSearchPage.search();
			await collectionSearchPage.expectTableResult();
			await collectionSearchPage.expectTableCount(1);
		});
	}
})
*/
