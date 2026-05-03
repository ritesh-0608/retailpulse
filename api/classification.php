<?php
header('Content-Type: application/json');
include 'dbconnect.php';

// Fetch data for classification
$res = $conn->query("SELECT SaleID, Quantity, Price, CustomerType FROM sales WHERE Quantity IS NOT NULL AND Price IS NOT NULL");
$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = [
        'SaleID'=>$row['SaleID'],
        'Quantity'=>floatval($row['Quantity']),
        'Price'=>floatval($row['Price']),
        'CustomerType'=>$row['CustomerType']
    ];
}

// Simple "Rule-based" Classification Logic
$correct = 0;
$wrong = 0;
$misclassified = [];
foreach ($data as $row) {
    if ($row['Quantity'] > 3 && $row['Price'] > 500) {
        $predicted = 'Member';
    } else {
        $predicted = 'Regular';
    }
    if ($predicted == $row['CustomerType']) {
        $correct++;
    } else {
        $wrong++;
        if (count($misclassified) < 5) {
            $misclassified[] = [
                'SaleID' => $row['SaleID'],
                'Actual' => $row['CustomerType'],
                'Predicted' => $predicted,
                'Quantity' => $row['Quantity'],
                'Price' => $row['Price']
            ];
        }
    }
}
echo json_encode([
    'rule'=>'If Quantity > 3 and Price > 500 then Member, else Regular',
    'accuracy'=>round(100 * $correct / max(1, count($data)),2),
    'correct'=>$correct,
    'wrong'=>$wrong,
    'misclassified'=>$misclassified
]);
?>