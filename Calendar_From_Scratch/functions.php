<?php

// Function to add a calendar entry
if (!function_exists("addCalendarEntry")){
    function addCalendarEntry($id_users, $title, $notes, $month, $year){
        
        // //Get the current day, month, and year
        $day = isset($_POST['day']) ? intval($_POST['day']) : date('j');
        $month = isset($_POST['month']) ? intval($_POST['month']) : date('n');
        $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');

        // Prepare data for insertion
        $data = array(
            "id_users" => $id_users,
            "title" => $title,
            "notes" => $notes,
            "day" => $day,
            "month" => $month,
            "year" => $year,
            "modified" => time(),
            "created" => time()
        );

        // Insert data into the database
        if (sqliteInsert(MODULE_BYDAS_HR_DB, "calendar", $data)) {
            return true;
        } else {
            return false;
        }
    }
}


// Function to modify a calendar entry
if (!function_exists("modifyCalendarEntry")){
    function modifyCalendarEntry($entry_id, $title, $notes){
        // Prepare data for modification
        $data = array(
            "title" => $title,
            "notes" => $notes
        );

        // Update data in the database
        if (sqliteUpdate(MODULE_BYDAS_HR_DB, "calendar", $data, "id=".$entry_id)) {
            return true;
        } else {
            return false;
        }
    }
}


// Function to get all calendar entries or entries for a specific user
if (!function_exists("getCalendarEntries")){
    function getCalendarEntries($user_id=null){
        // If user_id is provided, fetch entries for that user, otherwise fetch all entries
        if ($user_id !== null) {
            $check = sqliteGet(MODULE_BYDAS_HR_DB, "calendar", "WHERE id_users=".$user_id);
        } else {
            $check = sqliteGet(MODULE_BYDAS_HR_DB, "calendar");
        }

        return $check;
    }
}


// Function to get a specific calendar entry
if (!function_exists("getCalendarEntry")){
    function getCalendarEntry($entry_id){
        // Fetch entry with the given entry_id
        $check = sqliteGet(MODULE_BYDAS_HR_DB, "calendar", "WHERE id=".$entry_id);

        return $check;
    }
}


// Function to retrieve events for a specific date
if (!function_exists("getEventsForDate")) {
    function getEventsForDate($date) {
        // Query database for events on the specified date
        $events = sqliteGet(MODULE_BYDAS_HR_DB, "calendar", "WHERE date = '$date'");
     

        return $events;
        
    }
}


// Function to retrieve events for the current month
if (!function_exists("getEventsForCurrentMonth")) {
    function getEventsForCurrentMonth() {
        // Get current month and year
        $month = date('n');
        $year = date('Y');

        // Query database for events in the current month
        $events = sqliteGet(MODULE_BYDAS_HR_DB, "calendar", "WHERE month = $month AND year = $year");
        
        return $events;
    }
}


// Função para recuperar eventos para um mês e ano específicos
if (!function_exists("getEventsForMonthAndYear")) {
    function getEventsForMonthAndYear($month, $year) {
        // Query database for events in the specified month and year
        $events = sqliteGet(MODULE_BYDAS_HR_DB, "calendar", "WHERE month = $month AND year = $year");
        
        return $events;
    }
}    


// Function to retrieve events for a specific day
if (!function_exists("getEventsForDay")) {
    function getEventsForDay($day, $month, $year) {
        // Query database for events on the specified date
        $events = sqliteGet(MODULE_BYDAS_HR_DB, "calendar", "WHERE day = $day AND month = $month AND year = $year");
        return $events;
       
    }
}


// Função para obter o nome do usuário pelo ID
if (!function_exists("getUserById")) {
    function getUserById($user_id) {
        // Obtém o nome do usuário com base no ID
        $users = sqliteGet(CORE_USERS_DB, "users", "WHERE id = $user_id");

        if (empty($users)) {
            return 'Usuário Desconhecido'; // Se não houver usuário com esse ID
        }

        return $users[0]['name']; // Retorna o nome do primeiro usuário encontrado
    }
}


// Function to delete a calendar entry by its ID
if (!function_exists("deleteCalendarEntry")) {
    function deleteCalendarEntry($entry_id) {
        // Validate that the entry ID is provided and is a number
        if (empty($entry_id) || !is_numeric($entry_id)) {
            return false; // Invalid ID
        }

        // Convert the ID to an integer (to ensure it's a valid number)
        $entry_id = intval($entry_id);

        // Delete the calendar entry with the given ID
        $deleted = sqliteDelete(MODULE_BYDAS_HR_DB, "calendar", "WHERE id = ".$entry_id);

        return $deleted; // Returns true if deletion was successful, false otherwise
    }
}


// Function to delete all calendar entries
if (!function_exists("deleteAllCalendarEntries")) {
    function deleteAllCalendarEntries() {
        // Delete all entries from the calendar table
        $deleted = sqliteDelete(MODULE_BYDAS_HR_DB, "calendar", "id = 4");

        return $deleted; // Returns true if deletion was successful, false otherwise
    }
}


// Function to get notes
if (!function_exists("getNotesForDate")) {
    function getNotesForDate($day, $month, $year) {
        // Converte os valores para inteiros para segurança
        $day = intval($day);
        $month = intval($month);
        $year = intval($year);

        // Validação dos valores para garantir que estejam dentro dos limites válidos
        if ($day < 1 || $day > 31 || $month < 1 || $month > 12 || $year < 1) {
            return null; // Retorna nulo se a data for inválida
        }

        // Consulta para obter notas do banco de dados para o dia específico
        $notes = sqliteGet(
            MODULE_BYDAS_HR_DB, 
            "calendar", 
            "WHERE day = $day AND month = $month AND year = $year"
        );

        return $notes; // Retorna as notas encontradas ou null se não houver
    }
}

// Adicione uma nova função para obter eventos de um mês específico
if (!function_exists("getEventsForMonth")) {
    function getEventsForMonth($month) {
        // Query database for events in the specified month
        $events = sqliteGet(MODULE_BYDAS_HR_DB, "calendar", "WHERE month = $month");

        return $events;
    }
}

if (!function_exists("bydas_hr_calendar_css")) {
    function bydas_hr_calendar_css() {
        header("Content-Type: text/css");
        include(__DIR__ . "/module_bydas_hr/calendar.css");
        exit;
    }
}


?>
