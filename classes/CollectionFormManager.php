<?php
include_once($SERVER_ROOT . '/classes/Manager.php');

class CollectionFormManager extends Manager {

    public function __construct() {
        parent::__construct(null, 'write');
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
}
