<?php
class Appointment
{
    /* A variable that is used to store the connection to the database. */
    private $conn;

    /* A variable that is used to store the name of the table. */
    private $db_table = "appointments";

    /* These are the properties of the class. */
    public $id;
    public $name;
    public $document;
    public $appointment_date;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * It returns all the appointments from the database that have the same date as today
     * 
     * @return The query is being returned.
     */
    public function getAppointments()
    {
        try {
            $today = date('Y-m-d');
            $sqlQuery = "SELECT id, name, document, DATE(appointment_date) AS appointment_date, status, created_at, updated_at FROM " . $this->db_table
                . " WHERE DATE(appointment_date) = '$today'";

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * It creates an appointment
     * 
     * @return Int boolean value.
     */
    public function createAppointment()
    {
        try {
            $exists = $this->getExistAppointment();

            if ($exists > 0) {
                $response = [
                    'message' => "There is an appointment for today."
                ];

                echo json_encode($response);
                exit;
            }

            $sqlQuery = "INSERT INTO " . $this->db_table .
                " SET name = :name, document = :document, appointment_date = :appointment_date, created_at = :created_at, updated_at = :updated_at";

            $stmt = $this->conn->prepare($sqlQuery);

            /* Used to prevent SQL injection. */
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->document = htmlspecialchars(strip_tags($this->document));
            $this->appointment_date = htmlspecialchars(strip_tags($this->appointment_date));
            $this->created_at = htmlspecialchars(strip_tags($this->created_at));
            $this->updated_at = htmlspecialchars(strip_tags($this->updated_at));

            /* Used to prevent SQL injection. */
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":document", $this->document);
            $stmt->bindParam(":appointment_date", $this->appointment_date);
            $stmt->bindParam(":created_at", $this->created_at);
            $stmt->bindParam(":updated_at", $this->updated_at);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * It updates the status of an appointment in the database.
     * 
     * @return Int boolean value.
     */
    public function updateAppointment()
    {
        try {
            $sqlQuery = "UPDATE " . $this->db_table . " SET status = :status, updated_at = :updated_at WHERE id = :id";

            $stmt = $this->conn->prepare($sqlQuery);

            /* Used to prevent SQL injection. */
            $this->id = htmlspecialchars(strip_tags($this->id));
            $this->status = htmlspecialchars(strip_tags($this->status));
            $this->updated_at = htmlspecialchars(strip_tags($this->updated_at));

            /* Used to prevent SQL injection. */
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":status", $this->status);
            $stmt->bindParam(":updated_at", $this->updated_at);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function showAppointment()
    {
        try {
            $sqlQuery = "SELECT id, name, document, appointment_date, status, created_at, updated_at FROM " . $this->db_table . " WHERE id = ? LIMIT 0,1";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $this->id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                $this->name = $dataRow['name'];
                $this->document = $dataRow['document'];
                $this->appointment_date = $dataRow['appointment_date'];
                $this->status = $dataRow['status'];
                $this->created_at = $dataRow['created_at'];
                $this->updated_at = $dataRow['updated_at'];
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * It checks if the user has an appointment for the current day and confirmed apppointments.
     * 
     * @return The number of rows that match the query.
     */
    private function getExistAppointment()
    {
        $appointment_date = date("Y-m-d", strtotime($this->appointment_date));

        $sqlQuery = "SELECT document, DATE(appointment_date) AS appointment_date FROM " . $this->db_table
            . " WHERE document = {$this->document} AND DATE(appointment_date) = '{$appointment_date}' AND (status = 1 OR status IS NULL)";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        $count = $dataRow ? count($dataRow) : 0;

        return $count;
    }

    public static function getStatusNameAttribute($status){
        switch ($status) {
            case null:
                return 'Pendiente';
                break;
            case 0:
                return 'Rechazada';
                break;
            case 1:
                return 'Confirmada';
                break;
            default:
                return '';
                break;
        }
    }
}
