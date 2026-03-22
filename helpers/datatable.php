<?php
/**
 * datatable_response()
 *
 * Handles the DataTables server-side protocol:
 * - Reads draw/start/length/search from $_GET
 * - Runs a COUNT query for total and filtered records
 * - Runs the data query with LIMIT/OFFSET
 * - Returns structured JSON
 *
 * @param mysqli  $conn
 * @param string  $baseQuery      SELECT ... FROM ... JOIN ... WHERE base_conditions
 * @param array   $searchCols     Column expressions to apply LIKE search on
 * @param string  $defaultOrder   e.g. "t.created_at DESC"
 * @param array   $bindTypes      e.g. ['i', 1] for base WHERE bind params
 * @param array   $bindValues     Values for base WHERE params
 * @param callable $rowMapper     fn($row) => transformed row array
 */
function datatable_response($conn, $baseQuery, $searchCols, $defaultOrder, $bindTypes, $bindValues, $rowMapper, $groupBy = '', $orderableCols = []) {
    // Catch any DB/runtime error and return a JSON error instead of crashing
    try {
    // Ensure groupBy has a leading space so it doesn't merge with the preceding SQL token
    if ($groupBy !== '' && $groupBy[0] !== ' ') $groupBy = ' ' . $groupBy;
    $draw   = (int) ($_GET['draw']   ?? 1);
    $start  = (int) ($_GET['start']  ?? 0);
    $length = (int) ($_GET['length'] ?? 10);
    $search = trim($_GET['search']['value'] ?? '');

    // ── Total records (no search filter) ────────────────────
    $countStmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM ($baseQuery$groupBy) base_count");
    if ($bindTypes && $bindValues) {
        $countStmt->bind_param($bindTypes, ...$bindValues);
    }
    $countStmt->execute();
    $recordsTotal = (int) $countStmt->get_result()->fetch_assoc()['cnt'];

    // ── Search filter ────────────────────────────────────────
    $searchClause = '';
    $searchBindTypes  = '';
    $searchBindValues = [];

    if ($search !== '' && count($searchCols) > 0) {
        $likes = array_map(fn($col) => "$col LIKE ?", $searchCols);
        $searchClause = ' AND (' . implode(' OR ', $likes) . ')';
        foreach ($searchCols as $_) {
            $searchBindTypes  .= 's';
            $searchBindValues[] = "%$search%";
        }
    }

    // ── Filtered count ───────────────────────────────────────
    $filteredStmt = $conn->prepare(
        "SELECT COUNT(*) AS cnt FROM ($baseQuery$searchClause$groupBy) filtered_count"
    );
    $allTypes  = $bindTypes  . $searchBindTypes;
    $allValues = array_merge($bindValues, $searchBindValues);
    if ($allTypes) {
        $filteredStmt->bind_param($allTypes, ...$allValues);
    }
    $filteredStmt->execute();
    $recordsFiltered = (int) $filteredStmt->get_result()->fetch_assoc()['cnt'];

    // ── Resolve sort column ───────────────────────────────────
    $orderColIdx = isset($_GET['order'][0]['column']) ? (int) $_GET['order'][0]['column'] : -1;
    $orderDir    = isset($_GET['order'][0]['dir']) && strtolower($_GET['order'][0]['dir']) === 'desc' ? 'DESC' : 'ASC';
    $order       = (isset($orderableCols[$orderColIdx])) ? $orderableCols[$orderColIdx] . ' ' . $orderDir : $defaultOrder;

    // ── Data query with ORDER + LIMIT/OFFSET ─────────────────
    $dataStmt = $conn->prepare(
        "$baseQuery$searchClause$groupBy ORDER BY $order LIMIT ? OFFSET ?"
    );
    $dataTypes  = $allTypes . 'ii';
    $dataValues = array_merge($allValues, [$length, $start]);
    $dataStmt->bind_param($dataTypes, ...$dataValues);
    $dataStmt->execute();
    $rows = $dataStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $data = array_map($rowMapper, $rows);

    header('Content-Type: application/json');
    echo json_encode([
        'draw'            => $draw,
        'recordsTotal'    => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data'            => $data,
    ]);
    exit;
    } catch (Throwable $e) {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'draw'            => (int) ($_GET['draw'] ?? 1),
            'recordsTotal'    => 0,
            'recordsFiltered' => 0,
            'data'            => [],
            'error'           => $e->getMessage(),
        ]);
        exit;
    }
}
