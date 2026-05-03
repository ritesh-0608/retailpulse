<?php
header('Content-Type: application/json');
include 'dbconnect.php';

// Fetch numeric data for clustering
$res = $conn->query("SELECT SaleID, Quantity, Price FROM sales WHERE Quantity IS NOT NULL AND Price IS NOT NULL");
$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = [
        'SaleID'=>$row['SaleID'],
        'Quantity'=>floatval($row['Quantity']),
        'Price'=>floatval($row['Price'])
    ];
}
if (count($data) == 0) {
    echo json_encode(['error'=>'No data for clustering.']);
    exit;
}

// K-Means parameters
$k = 3; // number of clusters
$max_iter = 100;

// Initialize centroids randomly
$centroids = [];
for ($i=0; $i<$k; $i++) {
    $centroids[] = [
        'Quantity' => $data[array_rand($data)]['Quantity'],
        'Price' => $data[array_rand($data)]['Price']
    ];
}

for ($iter=0; $iter<$max_iter; $iter++) {
    // Assign points to nearest centroid
    $clusters = array_fill(0, $k, []);
    foreach ($data as $point) {
        $distances = [];
        foreach ($centroids as $c) {
            $distances[] = sqrt(pow($point['Quantity']-$c['Quantity'],2) + pow($point['Price']-$c['Price'],2));
        }
        $min_idx = array_search(min($distances), $distances);
        $clusters[$min_idx][] = $point;
    }
    // Update centroids
    $new_centroids = [];
    foreach ($clusters as $cluster) {
        if (count($cluster) == 0) {
            $new_centroids[] = $centroids[count($new_centroids)];
            continue;
        }
        $sumQ = 0; $sumP = 0;
        foreach ($cluster as $p) {
            $sumQ += $p['Quantity'];
            $sumP += $p['Price'];
        }
        $new_centroids[] = [
            'Quantity' => $sumQ / count($cluster),
            'Price' => $sumP / count($cluster)
        ];
    }
    // Check for convergence
    $converged = true;
    for ($i=0; $i<$k; $i++) {
        if (abs($new_centroids[$i]['Quantity']-$centroids[$i]['Quantity'])>0.001 ||
            abs($new_centroids[$i]['Price']-$centroids[$i]['Price'])>0.001) {
            $converged = false;
            break;
        }
    }
    $centroids = $new_centroids;
    if ($converged) break;
}

// Cluster summary (centroid, count, 3 sample SaleIDs)
$cluster_summary = [];
for ($idx=0; $idx<$k; $idx++) {
    $cluster_points = $clusters[$idx];
    $sample_ids = [];
    foreach ($cluster_points as $i=>$p) {
        if ($i < 3) $sample_ids[] = $p['SaleID'];
    }
    $cluster_summary[] = [
        'Cluster' => $idx+1,
        'Centroid' => $centroids[$idx],
        'Count' => count($cluster_points),
        'SampleSaleIDs' => $sample_ids
    ];
}
echo json_encode([
    'cluster_summary'=>$cluster_summary,
    'info'=>"K-means clustering on Quantity and Price; k=$k"
]);
?>