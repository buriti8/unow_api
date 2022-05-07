<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../class/Appointment.php';

$database = new Database();
$db = $database->getConnection();

$item = new Appointment($db);

/* Getting the data from the request body. */
$data = json_decode(file_get_contents("php://input"));

/* This is the data that is being sent to the database. */
$item->id = $data->id;
$item->status = $data->status;
$item->updated_at = date('Y-m-d H:i:s');

if ($item->updateAppointment()) {
    http_response_code(201);

    $response = [
        'message' => "Success"
    ];

    echo json_encode($response);
} else {
    http_response_code(400);

    $response = [
        'message' => "Error, the appointment can't be updated"
    ];

    echo json_encode($response);
}
