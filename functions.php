<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function clean_input($conn, $value) {
    return mysqli_real_escape_string($conn, trim($value));
}

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function current_user_id() {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function current_user_name() {
    return isset($_SESSION['complete_name']) ? $_SESSION['complete_name'] : "Guest";
}

function is_admin() {
    return isset($_SESSION['admin_id'], $_SESSION['admin_role']) && $_SESSION['admin_role'] === "admin";
}

function require_admin() {
    // Admin pages must not load unless an admin session exists.
    if (!is_admin()) {
        $_SESSION['login_notice'] = "Please log in as an administrator.";
        header("Location: ../login.php");
        exit;
    }
}

function require_buyer_login() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "buyer") {
        $_SESSION['login_notice'] = "Please log in or register before ordering instruments.";
        header("Location: login.php");
        exit;
    }
}

function audit_log($conn, $action, $details = "") {
    $user_id = current_user_id();
    $name = clean_input($conn, current_user_name());
    $action = clean_input($conn, $action);
    $details = clean_input($conn, $details);
    $user_sql = $user_id ? $user_id : "NULL";
    mysqli_query($conn, "INSERT INTO audit_logs (user_id, actor_name, action, details) VALUES ($user_sql, '$name', '$action', '$details')");
}

function cart_count() {
    if (!isset($_SESSION['cart'])) {
        return 0;
    }
    return array_sum($_SESSION['cart']);
}

function peso($amount) {
    return "PHP " . number_format((float)$amount, 2);
}

function philippine_regions() {
    return array_keys(philippine_address_options());
}

function philippine_address_options() {
    return [
        "National Capital Region (NCR)" => [
            "Metro Manila" => ["Caloocan", "Las Pinas", "Makati", "Malabon", "Mandaluyong", "Manila", "Marikina", "Muntinlupa", "Navotas", "Paranaque", "Pasay", "Pasig", "Pateros", "Quezon City", "San Juan", "Taguig", "Valenzuela"]
        ],
        "Cordillera Administrative Region (CAR)" => [
            "Abra" => ["Bangued", "Bucay", "Dolores", "Lagangilang"],
            "Apayao" => ["Kabugao", "Conner", "Luna", "Pudtol"],
            "Benguet" => ["Baguio City", "La Trinidad", "Itogon", "Tuba"],
            "Ifugao" => ["Lagawe", "Banaue", "Kiangan", "Alfonso Lista"],
            "Kalinga" => ["Tabuk City", "Rizal", "Pinukpuk", "Lubuagan"],
            "Mountain Province" => ["Bontoc", "Bauko", "Sagada", "Tadian"]
        ],
        "Region I - Ilocos Region" => [
            "Ilocos Norte" => ["Laoag City", "Batac City", "Bacarra", "Paoay"],
            "Ilocos Sur" => ["Vigan City", "Candon City", "Narvacan", "Santa Maria"],
            "La Union" => ["San Fernando City", "Bauang", "Agoo", "Naguilian"],
            "Pangasinan" => ["Lingayen", "Dagupan City", "Urdaneta City", "Alaminos City"]
        ],
        "Region II - Cagayan Valley" => [
            "Batanes" => ["Basco", "Itbayat", "Ivana", "Sabtang"],
            "Cagayan" => ["Tuguegarao City", "Aparri", "Lal-lo", "Solana"],
            "Isabela" => ["Ilagan City", "Cauayan City", "Santiago City", "Roxas"],
            "Nueva Vizcaya" => ["Bayombong", "Solano", "Bambang", "Aritao"],
            "Quirino" => ["Cabarroguis", "Diffun", "Maddela", "Aglipay"]
        ],
        "Region III - Central Luzon" => [
            "Aurora" => ["Baler", "Casiguran", "Dingalan", "Maria Aurora"],
            "Bataan" => ["Balanga City", "Dinalupihan", "Mariveles", "Orani"],
            "Bulacan" => ["Malolos City", "Meycauayan City", "San Jose del Monte City", "Baliwag City"],
            "Nueva Ecija" => ["Palayan City", "Cabanatuan City", "Gapan City", "San Jose City"],
            "Pampanga" => ["San Fernando City", "Angeles City", "Mabalacat City", "Guagua"],
            "Tarlac" => ["Tarlac City", "Capas", "Concepcion", "Paniqui"],
            "Zambales" => ["Iba", "Olongapo City", "Subic", "Botolan"]
        ],
        "Region IV-A - CALABARZON" => [
            "Batangas" => ["Batangas City", "Lipa City", "Tanauan City", "Nasugbu"],
            "Cavite" => ["Trece Martires City", "Bacoor City", "Dasmarinas City", "Tagaytay City"],
            "Laguna" => ["Santa Cruz", "Calamba City", "San Pablo City", "Santa Rosa City"],
            "Quezon" => ["Lucena City", "Tayabas City", "Candelaria", "Sariaya"],
            "Rizal" => ["Antipolo City", "Cainta", "Taytay", "Binangonan"]
        ],
        "MIMAROPA Region" => [
            "Marinduque" => ["Boac", "Gasan", "Mogpog", "Santa Cruz"],
            "Occidental Mindoro" => ["Mamburao", "San Jose", "Sablayan", "Abra de Ilog"],
            "Oriental Mindoro" => ["Calapan City", "Puerto Galera", "Roxas", "Naujan"],
            "Palawan" => ["Puerto Princesa City", "Coron", "El Nido", "Brooke's Point"],
            "Romblon" => ["Romblon", "Odiongan", "San Agustin", "Cajidiocan"]
        ],
        "Region V - Bicol Region" => [
            "Albay" => ["Legazpi City", "Ligao City", "Tabaco City", "Daraga"],
            "Camarines Norte" => ["Daet", "Labo", "Jose Panganiban", "Paracale"],
            "Camarines Sur" => ["Pili", "Naga City", "Iriga City", "Libmanan"],
            "Catanduanes" => ["Virac", "Bato", "San Andres", "Pandan"],
            "Masbate" => ["Masbate City", "Aroroy", "Cataingan", "Mandaon"],
            "Sorsogon" => ["Sorsogon City", "Bulan", "Gubat", "Irosin"]
        ],
        "Region VI - Western Visayas" => [
            "Aklan" => ["Kalibo", "Malay", "New Washington", "Banga"],
            "Antique" => ["San Jose de Buenavista", "Culasi", "Pandan", "Sibalom"],
            "Capiz" => ["Roxas City", "Pontevedra", "Panay", "Mambusao"],
            "Guimaras" => ["Jordan", "Buenavista", "Nueva Valencia", "Sibunag"],
            "Iloilo" => ["Iloilo City", "Passi City", "Oton", "Pavia"]
        ],
        "Negros Island Region (NIR)" => [
            "Negros Occidental" => ["Bacolod City", "Bago City", "Kabankalan City", "Talisay City"],
            "Negros Oriental" => ["Dumaguete City", "Bais City", "Bayawan City", "Tanjay City"],
            "Siquijor" => ["Siquijor", "Larena", "Lazi", "San Juan"]
        ],
        "Region VII - Central Visayas" => [
            "Bohol" => ["Tagbilaran City", "Talibon", "Tubigon", "Ubay"],
            "Cebu" => ["Cebu City", "Lapu-Lapu City", "Mandaue City", "Talisay City"]
        ],
        "Region VIII - Eastern Visayas" => [
            "Biliran" => ["Naval", "Almeria", "Caibiran", "Kawayan"],
            "Eastern Samar" => ["Borongan City", "Guiuan", "Dolores", "Oras"],
            "Leyte" => ["Tacloban City", "Ormoc City", "Baybay City", "Palo"],
            "Northern Samar" => ["Catarman", "Laoang", "Allen", "Las Navas"],
            "Samar" => ["Catbalogan City", "Calbayog City", "Basey", "Samar"],
            "Southern Leyte" => ["Maasin City", "Sogod", "Liloan", "Hinunangan"]
        ],
        "Region IX - Zamboanga Peninsula" => [
            "Zamboanga del Norte" => ["Dipolog City", "Dapitan City", "Sindangan", "Polanco"],
            "Zamboanga del Sur" => ["Pagadian City", "Zamboanga City", "Molave", "Aurora"],
            "Zamboanga Sibugay" => ["Ipil", "Kabasalan", "Titay", "Buug"]
        ],
        "Region X - Northern Mindanao" => [
            "Bukidnon" => ["Malaybalay City", "Valencia City", "Manolo Fortich", "Maramag"],
            "Camiguin" => ["Mambajao", "Catarman", "Mahinog", "Sagay"],
            "Lanao del Norte" => ["Tubod", "Iligan City", "Kapatagan", "Lala"],
            "Misamis Occidental" => ["Oroquieta City", "Ozamiz City", "Tangub City", "Jimenez"],
            "Misamis Oriental" => ["Cagayan de Oro City", "Gingoog City", "El Salvador City", "Tagoloan"]
        ],
        "Region XI - Davao Region" => [
            "Davao de Oro" => ["Nabunturan", "Monkayo", "Pantukan", "Maco"],
            "Davao del Norte" => ["Tagum City", "Panabo City", "Samal City", "Carmen"],
            "Davao del Sur" => ["Digos City", "Davao City", "Bansalan", "Santa Cruz"],
            "Davao Occidental" => ["Malita", "Santa Maria", "Don Marcelino", "Jose Abad Santos"],
            "Davao Oriental" => ["Mati City", "Baganga", "Lupon", "Governor Generoso"]
        ],
        "Region XII - SOCCSKSARGEN" => [
            "Cotabato" => ["Kidapawan City", "Midsayap", "Kabacan", "M'lang"],
            "Sarangani" => ["Alabel", "Glan", "Malungon", "Kiamba"],
            "South Cotabato" => ["Koronadal City", "General Santos City", "Polomolok", "Tupi"],
            "Sultan Kudarat" => ["Isulan", "Tacurong City", "Lebak", "Kalamansig"]
        ],
        "Region XIII - Caraga" => [
            "Agusan del Norte" => ["Cabadbaran City", "Butuan City", "Buenavista", "Nasipit"],
            "Agusan del Sur" => ["Prosperidad", "Bayugan City", "San Francisco", "Trento"],
            "Dinagat Islands" => ["San Jose", "Basilisa", "Cagdianao", "Dinagat"],
            "Surigao del Norte" => ["Surigao City", "Dapa", "Claver", "Mainit"],
            "Surigao del Sur" => ["Tandag City", "Bislig City", "Hinatuan", "Lianga"]
        ],
        "Bangsamoro Autonomous Region in Muslim Mindanao (BARMM)" => [
            "Basilan" => ["Isabela City", "Lamitan City", "Maluso", "Sumisip"],
            "Lanao del Sur" => ["Marawi City", "Malabang", "Wao", "Balindong"],
            "Maguindanao del Norte" => ["Datu Odin Sinsuat", "Sultan Kudarat", "Parang", "Matanog"],
            "Maguindanao del Sur" => ["Buluan", "Datu Paglas", "General Salipada K. Pendatun", "Ampatuan"],
            "Sulu" => ["Jolo", "Indanan", "Patikul", "Maimbung"],
            "Tawi-Tawi" => ["Bongao", "Panglima Sugala", "Sitangkai", "Mapun"]
        ]
    ];
}

function valid_philippine_address_selection($region, $province, $city) {
    $options = philippine_address_options();
    return isset($options[$region])
        && isset($options[$region][$province])
        && in_array($city, $options[$region][$province], true);
}

function format_complete_address($street, $barangay, $city, $province, $region, $postal_code) {
    return $street . ", Barangay " . $barangay . ", " . $city . ", " . $province . ", " . $region . ", " . $postal_code;
}
?>
