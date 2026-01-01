<?php
require 'config/database.php';

if (!isset($_GET['order_id'])) {
    die("Invalid Order");
}

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'] ?? 0;

// Fetch Order (Verify ownership or Admin)
$sql = "SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?";
if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
    $sql .= " AND o.user_id = $user_id";
}

$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found or access denied.");
}

// Fetch Items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bill #<?php echo $order_id; ?> - Scoops & Smiles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- html2pdf.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        @media print {
            .no-print { display: none; }
            .invoice-box { box-shadow: none; border: none; }
        }
    </style>
</head>
<body class="bg-light py-5">

    <div class="container text-center mb-4 no-print">
        <a href="index.php" class="btn btn-outline-secondary me-2">Back to Home</a>
        <button onclick="downloadPDF()" class="btn btn-primary">Download PDF</button>
    </div>

    <div class="invoice-box" id="invoice">
        <div class="row align-items-center mb-5">
            <div class="col-8">
                <h2 class="text-primary fw-bold">Scoops & Smiles</h2>
                <p class="mb-0 text-muted">123 Sweet Street, Flavor Town</p>
                <p class="text-muted">Email: contact@icecream.com</p>
            </div>
            <div class="col-4 text-end">
                <h1 class="text-muted opacity-50 display-6">INVOICE</h1>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <h6 class="fw-bold text-uppercase text-muted small">Bill To:</h6>
                <h5><?php echo htmlspecialchars($order['username']); ?></h5>
                <p class="mb-0"><?php echo htmlspecialchars($order['email']); ?></p>
            </div>
            <div class="col-6 text-end">
                <p class="mb-1"><strong>Invoice No:</strong> #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></p>
                <p class="mb-1"><strong>Date:</strong> <?php echo date('M d, Y', strtotime($order['order_date'])); ?></p>
                <p class="mb-1"><strong>Payment:</strong> Cash on Delivery</p>
            </div>
        </div>

        <table class="table table-striped mb-4">
            <thead class="bg-dark text-white">
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                    <td class="text-end">₹<?php echo number_format($item['price'], 2); ?></td>
                    <td class="text-end">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="row">
            <div class="col-6">
                <!-- Payment Info or simple text -->
            </div>
            <div class="col-6">
                <table class="table table-bordered">
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="text-end fw-bold bg-dark text-white">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="text-center mt-5 pt-4 border-top">
            <p class="fw-bold mb-1">Thank you for your business!</p>
            <p class="small text-muted">This is a computer-generated invoice.</p>
        </div>
    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('invoice');
            const opt = {
                margin:       0.5,
                filename:     'Invoice_#<?php echo $order['id']; ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
