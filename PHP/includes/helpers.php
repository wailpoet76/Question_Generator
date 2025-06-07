<?php
// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2024-07-15
// Description: HELPER FUNCTIONS 
// 
/*
 * Get the current language setting
 * @return string Current language code (Eng or Ar)
 */
function getCurrentLanguage() {
    global $langset;
    return isset($_GET['Langset']) ? $_GET['Langset'] : DEFAULT_LANGUAGE;
}

/**
 * Get the text direction based on language
 * @param string $lang Language code
 * @return string Text direction (ltr or rtl)
 */
function getTextDirection($lang) {
    return $lang === DEFAULT_LANGUAGE ? 'ltr' : 'rtl';
}

/**
 * Format user's display name
 * @param string $fullName Full name of the user
 * @return string Formatted name (first + last)
 */
function formatDisplayName($fullName) {
    $nameParts = explode(" ", $fullName);
    return $nameParts[0] . " " . end($nameParts);
}

/**
 * Get current page from URL parameters
 * @return string Current page name
 */
function getCurrentPage() {
    return isset($_GET['do']) ? $_GET['do'] : DEFAULT_PAGE;
}

/**
 * Safely get POST variable
 * @param string $key POST variable key
 * @param mixed $default Default value if key not found
 * @return mixed Value or default
 */
function getPostVar($key, $default = null) {
    return isset($_POST[$key]) ? filter_var($_POST[$key], FILTER_SANITIZE_STRING) : $default;
}

/**
 * Validate file upload
 * @param array $file $_FILES array element
 * @return array [bool success, string message]
 */
function validateFileUpload($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, 'File upload failed'];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_IMAGE_EXTENSIONS)) {
        return [false, 'Invalid file type'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return [false, 'File too large'];
    }

    return [true, ''];
}

/**
 * Generate a unique filename
 * @param string $originalName Original filename
 * @return string Unique filename
 */
function generateUniqueFilename($originalName) {
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    return uniqid() . '.' . $extension;
}

/**
 * Get dashboard box data
 * @param string $type Box type (urgent, myquestions, newquestion, database)
 * @param array $data Box data from database
 * @return array Box configuration
 */
function getDashboardBoxConfig($type, $data = []) {
    $configs = [
        'urgent' => [
            'id' => 'urgent',
            'icon' => 'fa-solid fa-skull-crossbones',
            'icon_color' => '#F00',
            'title' => 'URGENT',
            'count' => count($data),
            'date' => isset($data[0]) ? explode(" ", $data[0]['creation_date'])[0] : null,
            'active' => count($data) > 0
        ],
        'myquestions' => [
            'id' => 'myquestions',
            'icon' => 'fa-regular fa-folder-open',
            'icon_color' => '#0BC279',
            'title' => 'QUESTIONS',
            'count' => count($data),
            'date' => isset($data[0]) ? explode(" ", $data[0]['creation_date'])[0] : null,
            'active' => count($data) > 0
        ],
        'newquestion' => [
            'id' => 'newquestion',
            'icon' => 'fa-solid fa-folder-plus',
            'icon_color' => '#0BC279',
            'title' => 'CREATEQ',
            'count' => count($data),
            'date' => isset($data[0]) ? explode(" ", $data[0]['creation_date'])[0] : null,
            'active' => count($data) > 0
        ],
        'database' => [
            'id' => 'databasequestion',
            'icon' => 'fa-solid fa-chart-simple',
            'icon_color' => '#0BC279',
            'title' => 'DATAQ',
            'count' => 4, // Hardcoded for now
            'date' => '2024-09-27',
            'active' => false
        ]
    ];

    return $configs[$type];
}
