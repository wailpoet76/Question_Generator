<?php
// Auther: Walid Bakr
// Date: 2025-06-17
// Last Update: 2025-06-17
// Description: PHP API FOR OUTCOME CLICK
$pageTitle = 'Outcome caller';
// $nonavbar='';
$langset = isset($_GET['Langset']) ? $_GET['Langset'] : "Eng"; //default langauges is English each time
include '../../../DBconnect.php'; // initiate DB connection

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    header("Content-Type: application/json");
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["error" => "Invalid JSON"]);
        exit;
    }

    $value = $data['value'] ?? null;

    if ($value === null) {
        echo json_encode(["error" => "Missing value"]);
        exit;
    }
    $dbo=$con->prepare("SELECT COUNT(*) FROM questions_create
    WHERE
        outcome_id = ?
    ");
    $dbo->execute(array($value));
    $dbresult=$dbo->fetchColumn();

    // Simulated success response
    $response = [
    "success" => true,
    "message" => $dbresult
];
echo json_encode($response);
    

}else{
    fn_redirect("WRONG ACCESS TO PAGE","danger", 3,"../../../homepage.php?do=Dash&Langset=$langset");
}
?>