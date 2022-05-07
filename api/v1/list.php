<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../class/Appointment.php';

$database = new Database();
$db = $database->getConnection();

$items = new Appointment($db);

$getAppointments = $items->getAppointments();
$count = $getAppointments->rowCount();

/* This is a PHP code that is used to get the data from the database and return it in JSON format. */
if ($count > 0) {
    $appointments = [];
    $appointments["data"] = [];
    $appointments["total"] = $count;

    while ($row = $getAppointments->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $a = [
            "id" => $id,
            "name" => $name,
            "document" => $document,
            "appointment_date" => $appointment_date,
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at,
        ];

        array_push($appointments["data"], $a);
    }

    http_response_code(200);
    echo json_encode($appointments);
} else {
    http_response_code(404);

    echo json_encode(
        [
            "message" => "No records found."
        ]
    );
}
