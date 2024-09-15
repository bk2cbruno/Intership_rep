<?php

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Include necessary files
include_once("config.php");
include_once("functions.php");

if (isset($_POST["action"]) && $_POST["action"] == "bydas_hr_calendar_add") {

    if (isset($_SESSION["admin"])) {

        $post = $_POST;
        unset($post["action"]);
        foreach ($post as $key => $item) {
            if (is_array($item)) {
                $post[$key] = implode(",", $item);
            }
        }

        // Verifica e atribui 0 caso id_users não esteja definido
        if (isset($post["id_users"])) {
            $post["id_users"] = intval($post["id_users"]);
        } else {
            $post["id_users"] = 0;
        }

        // Verifica e atribui null para os campos que não estão definidos
        if (!isset($post["title"])) {
            $post["title"] = null;
        }
        if (!isset($post["notes"])) {
            $post["notes"] = null;
        }
        if (!isset($post["month"])) {
            $post["month"] = null;
        }
        if (!isset($post["year"])) {
            $post["year"] = null;
        }
        if (!isset($post["day"])) {
            $post["day"] = null;
        }

        // Prepare data for insertion
        $data = array(
            "id_users" => $post["id_users"],
            "title" => $post["title"],
            "notes" => $post["notes"],
            "day" => $post["day"],
            "month" => $post["month"],
            "year" => $post["year"],
            "modified" => time(),
            "created" => time()
        );

        // Check if any required fields are missing
        $missingFields = array();
        if ($post["id_users"] === null) {
            $missingFields[] = "id_users";
        }
        if (!$post["title"]) {
            $missingFields[] = "title";
        }
        if (!$post["notes"]) {
            $missingFields[] = "notes";
        }
        if (!$post["month"]) {
            $missingFields[] = "month";
        }
        if (!$post["year"]) {
            $missingFields[] = "year";
        }
        if (!$post["day"]) {
            $missingFields[] = "day";
        }

        if (!empty($missingFields)) {
            echo json_encode(array("status" => 0, "msg" => "Missing required fields: " . implode(", ", $missingFields)));
            exit;
        }

        // Perform addition
        $result = addCalendarEntry($post["id_users"], $post["title"], $post["notes"], $post["day"], $post["month"], $post["year"]);

        if ($result) {
            echo json_encode(array("status" => 1, "msg" => "Calendar entry added successfully"));
        } else {
            echo json_encode(array("status" => 0, "msg" => "Failed to add calendar entry"));
        }
        exit;
    }
}
   


if (isset($_GET["action"]) && $_GET["action"] == "bydas_hr_calendar_get_notes") {
    if (isset($_SESSION["admin"])) {
        // Verificar se todos os campos necessários para buscar notas estão presentes
        if (!isset($_GET["day"]) || !isset($_GET["month"]) || !isset($_GET["year"])) {
            echo json_encode(array(
                "status" => 0,
                "msg" => "Os campos day, month e year são necessários."
            ));
            exit;
        }

        // Obter os valores dos campos
        $day = intval($_GET["day"]);
        $month = intval($_GET["month"]);
        $year = intval($_GET["year"]);

        // Buscar notas do banco de dados com base no dia, mês e ano
        $notes = sqliteGet(MODULE_BYDAS_HR_DB, "calendar", "WHERE day = $day AND month = $month AND year = $year");

        if ($notes) {
            // Se encontrar notas, retornar sucesso com as notas
            echo json_encode(array(
                "status" => 1,
                "notes" => $notes
            ));
        } else {
            // Se não encontrar notas para o dia especificado
            echo json_encode(array(
                "status" => 0,
                "msg" => "Nenhuma nota encontrada para a data especificada."
            ));
        }
        exit;
    } else {
        echo json_encode(array(
            "status" => 0,
            "msg" => "Sessão expirada ou usuário não autorizado."
        ));
        exit;
    }
}



// Handle action to delete all calendar entries
if (isset($_POST["action"]) && $_POST["action"] == "deleteAllCalendarEntries") {
    // Check if the user has permission to delete (e.g., admin)
    if (isset($_SESSION["admin"])) {
        // Perform the deletion of all calendar entries
        $delete_success = deleteAllCalendarEntries();

        if ($delete_success) {
            echo json_encode(array("status" => 1, "msg" => "All calendar entries deleted successfully."));
        } else {
            echo json_encode(array("status" => 0, "msg" => "Failed to delete all calendar entries."));
        }
        exit;
    } else {
        echo json_encode(array("status" => 0, "msg" => "Session expired or unauthorized user."));
        exit;
    }
}


// Handle action to delete a specific calendar entry
if (isset($_POST["action"]) && $_POST["action"] == "deleteCalendarEntry") {
    // Check if the user has permission to delete (e.g., admin)
    if (isset($_SESSION["admin"])) {
        // Validate and sanitize input
        $entry_id = isset($_POST["entry_id"]) ? intval($_POST["entry_id"]) : null;

        // If the ID is missing or invalid
        if (empty($entry_id) || !is_numeric($entry_id)) {
            echo json_encode(array("status" => 0, "msg" => "Invalid or missing entry ID."));
            exit;
        }

        // Perform the deletion
        $delete_success = deleteCalendarEntry($entry_id);

        if ($delete_success) {
            echo json_encode(array("status" => 1, "msg" => "Calendar entry deleted successfully."));
        } else {
            echo json_encode(array("status" => 0, "msg" => "Failed to delete calendar entry."));
        }
        exit;
    } else {
        echo json_encode(array("status" => 0, "msg" => "Session expired or unauthorized user."));
        exit;
    }
}

if (isset($_GET["action"]) && $_GET["action"] == "bydas_hr_calendar_css") {
    bydas_hr_calendar_css();
}

// Handle other views or actions
// Display the calendar view
if (isset($_GET["view"]) && $_GET["view"] == "bydas_hr_calendar") {
    // Fetch all calendar entries to pass to the view
    $calendarEntries = getCalendarEntries();

    // Pass calendar entries to the view (e.g., using a template system or direct PHP output)
    include(PATH . "/module_bydas_hr/calendar.php");
}

// // Trate a requisição GET para obter os eventos de um mês específico
// if (isset($_GET["view"]) && $_GET["view"] == "month") {
//     // Verificar se o número do mês foi fornecido na solicitação GET
//     if (isset($_GET["month"])) {
//         $month = intval($_GET["month"]);
//         // Obter os eventos para o mês especificado
//         $events = getEventsForMonth($month);
//         // Retornar os eventos como resposta JSON
//         echo json_encode($events);
//         exit;
//     }
//     include(PATH . "/module_bydas_hr/calendar.php");
// }


// ?>
