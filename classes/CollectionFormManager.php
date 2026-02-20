<?php


include_once($SERVER_ROOT . '/classes/Manager.php');
Language::load('classes/CollectionFormManager');

class CollectionFormManager extends Manager {

    public function __construct() {
        parent::__construct(null, 'write');
    }

    public function getCollectionsByCategory() {
        $sql = 'SELECT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.icon, c.colltype, ccl.ccpk,
            cat.category, cat.icon AS caticon, cat.acronym
            FROM omcollections c INNER JOIN omcollectionstats s ON c.collid = s.collid
            LEFT JOIN omcollcatlink ccl ON c.collid = ccl.collid
            LEFT JOIN omcollcategories cat ON ccl.ccpk = cat.ccpk
            WHERE s.recordcnt > 0 AND (cat.inclusive IS NULL OR cat.inclusive = 1 OR cat.ccpk = 1)
            order by cat.category, collectionname';

        $collectionsByCategory = [
            'Specimens' => [],
            'Observations' => [],
        ];

        try {
            $rs = QueryUtil::executeQuery(Database::connect('readonly'), $sql);
            $specimenTypes = ['Preserved Specimens', 'Fossil Specimens'];
            foreach ($rs->fetch_all(MYSQLI_ASSOC) as $collection) {
                $type = in_array($collection['colltype'], $specimenTypes) ?
                    'Specimens' :
                    'Observations';

                if (!isset($collectionsByCategory[$type][$collection['category']])) {
                    $collectionsByCategory[$type][$collection['category']] = [
                        'name' => $collection['category'],
                        'icon' => $collection['caticon'],
                        'acronym' => $collection['acronym'],
                        'id' => $collection['ccpk'],
                        'collections' => [],
                    ];
                }

                $collectionsByCategory[$type][$collection['category']]['collections'][] = $collection;
            }
        } catch (Throwable $th) {
            echo $th->getMessage();
        }

        return $collectionsByCategory;
    }

    public function generateCodeStr($collectionArr) {
        if (!(array_key_exists('institutioncode', $collectionArr) || array_key_exists('collcode', $collectionArr))) {
            return null;
        }
        $codeStr = '(';
        if (array_key_exists('institutioncode', $collectionArr)) {
            $codeStr .= $collectionArr['institutioncode'];
        }
        if (array_key_exists('collcode', $collectionArr)) {
            $codeStr .= '-' . $collectionArr['collcode'];
        }
        $codeStr .= ')';
        return $codeStr;
    }


    /**
     * Reorders the Specimens/Observations category arrays in $data according to
     * the provided ID order arrays. Items not listed in the order arrays are kept
     * at the end, preserving their original relative order.
     *
     * @param array $data       The original nested array.
     * @param array $order   Desired order for both $data['Specimens'] and $data['Observations'] by category 'id'.
     * @return array            A new array with reordered categories.
     */
    public function reorderPortalCategories(array $data, array $order = []): array {
        // Helper: reorder an associative array of categories by each item's 'id'
        // while preserving original relative order among "unknown" items.
        $reorderByIdOrder = function (array $categoryMap, array $idOrder): array {
            if (empty($categoryMap) || empty($idOrder)) {
                return $categoryMap;
            }

            // Build rank map: id => position
            $rank = [];
            $pos = 0;
            foreach ($idOrder as $id) {
                // Normalize ids to string to match our data ('id' is string like '5')
                $rank[(string)$id] = $pos;
                $pos++;
            }

            // Decorate items with sort keys while remembering original order.
            $decorated = [];
            $i = 0;
            foreach ($categoryMap as $key => $value) {
                $id = isset($value['id']) ? (string)$value['id'] : null;

                $isKnown = ($id !== null && array_key_exists($id, $rank));
                $sortGroup = $isKnown ? 0 : 1;                 // known first, unknown last
                $sortRank  = $isKnown ? $rank[$id] : PHP_INT_MAX;
                $origIndex = $i;

                $decorated[] = [
                    'key'       => $key,
                    'value'     => $value,
                    'sortGroup' => $sortGroup,
                    'sortRank'  => $sortRank,
                    'origIndex' => $origIndex,
                ];

                $i++;
            }

            usort($decorated, function ($a, $b) {
                if ($a['sortGroup'] !== $b['sortGroup']) {
                    return $a['sortGroup'] <=> $b['sortGroup'];
                }
                if ($a['sortRank'] !== $b['sortRank']) {
                    return $a['sortRank'] <=> $b['sortRank'];
                }
                // Preserve original relative order within ties (esp. unknown items)
                return $a['origIndex'] <=> $b['origIndex'];
            });

            // Undecorate back to associative array in the new order.
            $out = [];
            foreach ($decorated as $item) {
                $out[$item['key']] = $item['value'];
            }

            return $out;
        };

        $out = $data;

        if (isset($out['Specimens']) && is_array($out['Specimens'])) {
            $out['Specimens'] = $reorderByIdOrder($out['Specimens'], $order);
        }

        if (isset($out['Observations']) && is_array($out['Observations'])) {
            $out['Observations'] = $reorderByIdOrder($out['Observations'], $order);
        }

        return $out;
    }

    /**
     * Validates a comma-separated list of collection IDs.
     *
     * @param string $catId The collection ID string to validate.
     * @return bool True if valid, false otherwise.
     */
    public function areCollectionIdsValid(string $requestStr): bool {
        if(!preg_match('/^[,\d]+$/',$requestStr)) return false;
        return true;
     }

     public function areCollectionCategoriesValid(string $requestStr): bool {
        $categories = explode(",", $requestStr);
        foreach ($categories as $category) {
            if(!preg_match('/^(Specimens_|Observations_)\d+$/', $category)) return false;
        }
        return true;
     }

     private function isAnEmptyString($str): bool {
        return is_string($str) && trim($str) === '';
     }

     public function reviseUncategorizedCollections(array $collections): array {
        $revisedCollections = $collections;
        $filteredCollections =array_filter($collections, function($key) {
            return $this->isAnEmptyString($key);
        }, ARRAY_FILTER_USE_KEY);
        $filteredCollectionsCount = count($filteredCollections);
        if ($filteredCollectionsCount === 1) {
            $uncategorizedKey = array_key_first($filteredCollections);
            $revisedCollections['Uncategorized'] = $revisedCollections[$uncategorizedKey];
            unset($revisedCollections[$uncategorizedKey]);
            $allIds = array_column($revisedCollections, 'id');
            $numericIds = array_map(function($id) {
                return is_numeric($id) ? (int)$id : null;
            },$allIds);
            $allOrphans = count($allIds) === 1;
            global $LANG;
            $revisedCollections['Uncategorized']['name'] = $allOrphans ? $LANG['SPECIMEN_AND_OBSERVATION_COLLECTIONS'] : $LANG['UNCATEGORIZED'];
            if (count($numericIds) === count($allIds)) {
                $maxId = max($numericIds);
                $revisedCollections['Uncategorized']['id'] = (string)($maxId + 1);
            }
        }
        return $revisedCollections;
     }

     public function areAllCollectionsCategoryless($hyperCollection): bool {
        $finalCount = 0;
        foreach($hyperCollection as $collectionType => $categories){
                $allIds = array_column($categories, 'id');
                $finalCount += count($allIds);
        }
        return $finalCount === 1;
     }
}
