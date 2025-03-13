<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


require_once 'db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


$basePath = "/school_bus_system/php/backend/bus_session.php";
$endpoint = str_replace($basePath, "", strtok($uri, '?'));
$endpoint = trim($endpoint, "/");


switch ($method) {
    case "GET":
        switch ($endpoint) {
            case "available-buses":
                $results = getAvailableBuses($conn);
                response(200, "Buses fetched successfully", $results);
                break;
        }
        break;

    case "POST":
        switch ($endpoint) {
            case "create-session":
                // 'bus_id', 'conductor_id', 'direction'
                $result = createBusSession($conn, $_POST);
                if ($result) {
                    $_SESSION["USER_STATUS"] = 'ON GOING TRANSIT';
                    response(200, "New Session Created.", $result);
                }
                break;
            case "student/pick-up":
                //'session_id', 'student_id'
                $result = pickUpStudent($conn, $_POST);
                if ($result) {
                    response(200, "Student Picked Up Successfully.", $result);
                }
                break;
            case "student/drop-off":
                //'session_id', 'student_id'
                $result = dropOffStudent($conn, $_POST);
                if ($result) {
                    response(200, "Student Dropped Off Successfully.");
                }
                break;
            case "student/search":
                //student_id
                $row = searchStudent($conn, $_POST);
                if ($row > 0) {
                    response(200, "Student Found", $row);
                }
                break;
            case "student/add":
                //student_id, name
                $row = addStudent($conn, $_POST);
                if ($row > 0) {
                    response(200, "Student Added Successfully", $row);
                }
                break;
            case "end-session":
                //session_id
                $result = endBusSession($conn, $_POST);
                if ($result) {
                    $_SESSION["USER_STATUS"] = 'IDLE';
                    response(200, "Session Successfuly terminated.");
                }
                break;

        }
        break;
}

function getBusStatus($id, $isEndSession = false)
{
    global $conn;

    $where = $isEndSession ? "id = ?" : " bus_id = ?";
    $sql = "SELECT id FROM bus_records WHERE $where AND status = 'ONGOING' LIMIT 1";

    $stmt = $conn->prepare($sql);
    try {
        $stmt->execute([$id]);
        return $stmt->fetchColumn() ? true : false;
    } catch (mysqli_sql_exception $e) {
        response(500, $e->getMessage());
    }
}

function getAvailableBuses($conn)
{
    $sql = "
        SELECT buses.*
        FROM bus_table buses
        LEFT JOIN bus_records br ON buses.id = br.bus_id AND br.status = 'ONGOING'
        WHERE buses.status = 'ACTIVE' AND br.id IS NULL
    ";
    $stmt = $conn->prepare($sql);
    try {
        $stmt->execute();
        $rows = $stmt -> fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            return $rows;
        }else{
            response(200, "No Available Buses.");
        }

    } catch (PDOException $e) {
        response(500, $e->getMessage());
    }
}

function createBusSession($conn, $params)
{
    $required_fields = ['bus_id', 'conductor_id', 'direction'];
    $isSet = !array_diff_key(array_flip($required_fields), $params);

    if ($isSet) {
        $isBusOngoing = getBusStatus($params['bus_id']);
        if (!$isBusOngoing) {
            $sql = "INSERT INTO bus_records (bus_id, conductor_id, status, direction, time_start) VALUES(:bus_id, :conductor_id, 'ONGOING', :direction, NOW())";
            $stmt = $conn->prepare($sql);
            try {
                $stmt->execute([':bus_id' => $params['bus_id'], ':conductor_id' => $params['conductor_id'], ':direction' => $params['direction']]);

                return $conn->lastInsertId();
            } catch (PDOException $e) {
                response(500, $e->getMessage());
            }
        } else {
            response(400, 'Bus is currently en-route.');
        }
    }
}

function endBusSession($conn, $params): mixed
{
    $required_fields = ['session_id'];
    $isSet = !array_diff_key(array_flip($required_fields), $params);
    if ($isSet) {
        $isBusOngoing = getBusStatus($params['session_id'], true);
        if ($isBusOngoing) {
            $sql = "UPDATE bus_records SET status = 'IDLE', time_end = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            try {
                $stmt->execute([$params['session_id']]);
                return $stmt->rowCount();
            } catch (PDOException $e) {
                response(500, $e->getMessage());
                return false;
            }
        } else {
            response(400, 'Bus is already idle.');
            return false;
        }
    } else {
        response(400, 'Missing session_id.');
        return false;
    }
}

function addStudent($conn, $params)
{
    $required_fields = ['student_id', 'name'];
    $isSet = !array_diff_key(array_flip($required_fields), $params);
    if ($isSet) {
        $sql = "INSERT INTO students (name, student_id, status) VALUES(:name, :student_id, 'ACTIVE')";
        $stmt = $conn->prepare($sql);
        try {
            $stmt->execute($params);

            $id = $conn->lastInsertId();
            return searchStudent($conn, ['student_id' => $id], true);
        } catch (PDOException $e) {
            response(500, $e->getMessage());
        }
    } else {
        response(400, 'Invalid Parameters.');
    }

}

function searchStudent($conn, $params, $isFromAddStudents = false)
{
    $required_fields = ['student_id'];
    $isSet = !array_diff_key(array_flip($required_fields), $params);

    if ($isSet) {
        $where = $isFromAddStudents ? "id = ?" : "student_id = ?";
        $sql = "SELECT * FROM students WHERE $where LIMIT 1";


        $stmt = $conn->prepare($sql);
        try {
            $stmt->execute([$params['student_id']]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                if ($row['status'] === 'ACTIVE') {
                    return $row;
                } else {
                    response(400, 'Student is Inactive.');
                }
            } else {
                response(200, 'Student Not Found', []);
            }
        } catch (mysqli_sql_exception $e) {
            response(500, $e->getMessage());
        }
    } else {
        response(400, 'Missing student id.');
    }
}

function lookUpStudentLog($conn, $params, $isStudentDropOff = false)
{
    $required_fields = ['session_id', 'student_id'];
    $isSet = !array_diff_key(array_flip($required_fields), $params);

    if ($isSet) {

        $where = $isStudentDropOff ? "id = :id" : 'session_id = :session_id AND student_id = :student_id';
        $sql = "SELECT id, status FROM student_logs WHERE $where";
        $stmt = $conn->prepare($sql);


        try {
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;


        } catch (mysqli_sql_exception $e) {
            response(500, $e->getMessage());
        }
    } else {
        response(400, 'Missing Information.');
    }
}

function pickUpStudent($conn, $params)
{
    $required_fields = ['session_id', 'student_id'];
    $isSet = !array_diff_key(array_flip($required_fields), $params);

    if ($isSet) {
        $lookUpStudent = lookUpStudentLog($conn, $params);
        if ($lookUpStudent['status'] !== 'ONBOARD') {
            $sql = "INSERT INTO student_logs (session_id, student_id, pick_up_time, status) VALUES(:session_id, :student_id, NOW(), 'ONBOARD')";
            $stmt = $conn->prepare($sql);

            try {
                $stmt->execute($params);
                return $conn->lastInsertId();
            } catch (mysqli_sql_exception $e) {
                response(500, $e->getMessage());
            }

        } else {
            response(400, 'Student is already Onboard.');
        }

    } else {
        response(400, 'Missing Information.');
    }


}
function dropOffStudent($conn, $params)
{
    $required_fields = ['session_id', 'student_id'];
    $isSet = !array_diff_key(array_flip($required_fields), $params);

    if ($isSet) {
        $lookUpStudent = lookUpStudentLog($conn, $params);

        if ($lookUpStudent['status'] === 'ONBOARD') {
            $id = $lookUpStudent['id'];
            $sql = "UPDATE student_logs SET drop_off_time = NOW(), status = 'DROPPED OFF' WHERE id = $id";
            $stmt = $conn->prepare($sql);

            try {
                $stmt->execute();
                return $stmt->rowCount();
            } catch (mysqli_sql_exception $e) {
                response(500, $e->getMessage());
            }
        } else {
            response(400, 'Student is already Dropped Off.');
        }
    } else {
        response(400, 'Missing Information.');
    }


}

function response($status, $message, $data = [])
{
    http_response_code($status);
    echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
}