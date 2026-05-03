<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
include 'dbconnect.php';
$type = $_GET['type'] ?? 'year';
$bars = [];
$topProducts = [];
$topMonths = [];

if (!$conn) {
    echo json_encode(['bars'=>[], 'topProducts'=>[], 'topMonths'=>[], 'error'=>'No DB connection']);
    exit;
}

function safeRupee($val) {
    return is_numeric($val) ? floatval($val) : 0;
}

try {
    if ($type == 'year') {
        // Show sales for each year
        $qry = "SELECT Year as label, SUM(Price*Quantity) as totalSale, SUM(Quantity) as quantitySold FROM sales GROUP BY Year ORDER BY Year DESC";
        $res = $conn->query($qry);
        while ($row = $res->fetch_assoc()) {
            $row['totalSale'] = safeRupee($row['totalSale']);
            $bars[] = $row;
        }
    } else if ($type == 'month') {
        // Show sales for each month in current year
        $currentYear = date('Y');
        for ($m = 1; $m <= 12; $m++) {
            $result = $conn->query("SELECT SUM(Price*Quantity) as totalSale, SUM(Quantity) as quantitySold FROM sales WHERE Year = $currentYear AND Month = $m");
            $row = $result->fetch_assoc();
            $bars[] = [
                'label' => $m,
                'totalSale' => safeRupee($row['totalSale']),
                'quantitySold' => safeRupee($row['quantitySold'])
            ];
        }
    } else {
        $qry = "SELECT Product as label, SUM(Price*Quantity) as totalSale, SUM(Quantity) as quantitySold FROM sales GROUP BY Product";
        $res = $conn->query($qry);
        while ($row = $res->fetch_assoc()) {
            $row['totalSale'] = safeRupee($row['totalSale']);
            $bars[] = $row;
        }
    }

    // Top 5 selling products
    $res = $conn->query("SELECT Product as name, SUM(Quantity) as quantity, AVG(Price) as rate, SUM(Quantity*Price) as total FROM sales GROUP BY Product ORDER BY total DESC LIMIT 5");
    if ($res) while ($row = $res->fetch_assoc()) {
        $row['rate'] = safeRupee($row['rate']);
        $row['total'] = safeRupee($row['total']);
        $topProducts[] = $row;
    }

    // Top 3 months by sales
    $currentYear = date('Y');
    $res = $conn->query("SELECT Month, SUM(Quantity*Price) as sales FROM sales WHERE Year=$currentYear GROUP BY Month ORDER BY sales DESC LIMIT 3");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $itemRes = $conn->query("SELECT Product FROM sales WHERE Year=$currentYear AND Month = {$row['Month']} GROUP BY Product ORDER BY SUM(Quantity) DESC LIMIT 1");
            $item = $itemRes ? $itemRes->fetch_assoc() : null;
            $row['mostSold'] = $item && isset($item['Product']) ? $item['Product'] : 'N/A';
            $row['sales'] = safeRupee($row['sales']);
            $topMonths[] = $row;
        }
    }

    echo json_encode(['bars'=>$bars, 'topProducts'=>$topProducts, 'topMonths'=>$topMonths]);
} catch (Exception $e) {
    echo json_encode(['bars'=>[], 'topProducts'=>[], 'topMonths'=>[], 'error'=>$e->getMessage()]);
}
?>