<?php
// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2024-07-15
// Description: ADJUST DEFAULT VARS CONSTANTS

// Language settings
define('DEFAULT_LANGUAGE', 'Eng');
define('SUPPORTED_LANGUAGES', ['Eng', 'Ar']);

// Directory settings
define('TPL_DIR', 'includes/templates/');
define('FUNC_DIR', 'includes/functions/');
define('LANG_DIR', 'includes/langauges/');
define('CSS_DIR', 'layout/css/');
define('JS_DIR', 'layout/js/');
define('IMG_DIR', 'layout/images/');

// Database constants
define('QUESTION_STATUS_PENDING', 0);
define('QUESTION_STATUS_REVIEW', 1);
define('QUESTION_STATUS_URGENT', 1);

// Page settings
define('DEFAULT_PAGE', 'Dash');

// Security settings
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
