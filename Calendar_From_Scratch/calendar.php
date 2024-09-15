<?php 
include(PATH."/module_interface/header.php");
include(PATH."/module_bydas_hr/functions.php");

// Fetch events for the current month
// $events = getEventsForCurrentMonth();


// Gerar lista de opções para os dias (1 a 31)
$days = range(1, 31);
$currentYear = date("Y");
$current_month = date("n");
$years = range($currentYear, $currentYear + 10);

// Get the selected year from the URL parameter or use the current year
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : $currentYear;
// Fetch events for the selected month and year
$events = getEventsForMonthAndYear($current_month, $selectedYear);

// Gerar lista de opções para os meses (1 a 12)
$months = [
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
];
// echo "<pre>";print_r(sqliteGet(MODULE_BYDAS_HR_DB,"calendar"));echo "</pre>";
?>



<!-- Modal para Quando clicar no card -->
<div id="bydas_hr_calendar_card_modal" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDayTitle">Dia X</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalNotesBody">
                <!-- Aqui serão exibidas as notas -->
            </div>
            <div class="modal-footer">
                <!-- <button type="button" id="btn_add_entry" class="btn btn-primary">Adicionar Entrada</button> -->
                <button type="button" id="btn_select_range" class="btn btn-primary">Adicionar Entrada</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal para o card Marcacao -->
<div id="bydas_hr_calendar">
    <div id="bydas_hr_calendar_modal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Marcação</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="InputUsuario">Usuario</label>
                    <select name="id_users" class="form-control">
                        <?php $users=sqliteGet(CORE_USERS_DB,"users","ORDER BY name ASC");?>
                        <option value="0">Ninguém</option>
                        <?php foreach ($users as $item){?>
                            <option value="<?php echo $item["id"]?>"><?php echo $item["name"]?></option>
                        <?php } ?>
                    </select>
                    <label for="InputTitulo">TITULO</label>
                    <input type="text" name="title" class="form-control" placeholder="Titulo">
                    <label for="InputNota">NOTA</label>
                    <textarea name="notes" rows="3" class="form-control" placeholder="Escreve uma nota"></textarea>
                    <input type="hidden" name="day">
                    <input type="hidden" name="month">
                    <input type="hidden" name="year">

                    <!-- Parte do intervalo de datas -->
                    <label for="startDate">Data de Início:</label>
                    <input type="date" name="startDate" id="startDate" class="form-control">
                    <label for="endDate">Data de Término:</label>
                    <input type="date" name="endDate" id="endDate" class="form-control">


                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <!-- <button type="button" class="btn btn-primary">Adicionar</button> -->
                <button type="button" id="btn_confirm_range" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para o butao -->
<div id="bydas_hr_calendar_btn">
    <div id="bydas_hr_calendar_modal_btn" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Marcação</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="InputUsuario">Usuario</label>
                    <select name="id_users" class="form-control">
                        <?php $users=sqliteGet(CORE_USERS_DB,"users","ORDER BY name ASC");?>
                        <option value="0">Ninguém</option>
                        <?php foreach ($users as $item){?>
                            <option value="<?php echo $item["id"]?>"><?php echo $item["name"]?></option>
                        <?php } ?>
                    </select>
                    <label for="InputTitulo">TITULO</label>
                    <input type="text" name="title" class="form-control" placeholder="Titulo">
                    <label for="InputNota">NOTA</label>
                    <textarea name="notes" rows="3" class="form-control" placeholder="Escreve uma nota"></textarea>
                    <label for="InputDia">Dia</label>
                    <select name="day" class="form-control">
                        <?php foreach ($days as $day) { ?>
                            <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                        <?php } ?>
                    </select>
                    <label for="InputMes">Mes</label>
                    <select name="month" class="form-control">
                        <?php foreach ($months as $key => $value) { ?>
                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php } ?>
                    </select>
                    <label for="InputAno">Ano</label>
                    <select name="year" class="form-control">
                        <?php foreach ($years as $year) { ?>
                            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                        <?php } ?>
                    </select>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary">Adicionar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para exibir as notas -->
<div class="modal fade" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="notesModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="notesModalLabel">Notas do Dia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="notesContent">
        <!-- Conteúdo das notas será inserido aqui -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>


<section class="bg-light py-3">
    <div class="container-fluid">
        <?php echo headerTitle("Calendário de Férias", isset($_GET["view"]) ? $_GET["view"] : "");?>
    </div>
</section>





<section class="py-3">
    <div class="container-fluid">
        <?php 
        // Define an array of days of the week
        $days = array(
            "Domingo",
            "Segunda",
            "Terça",
            "Quarta",
            "Quinta",
            "Sexta",
            "Sábado"
        );

        // Define an array of month names
        $month_names = array(
            "Janeiro",
            "Fevereiro",
            "Março",
            "Abril",
            "Maio",
            "Junho",
            "Julho",
            "Agosto",
            "Setembro",
            "Outubro",
            "Novembro",
            "Dezembro"
        );

        // Get the current year
        $current_year = date("Y");
        
        // Para poder utilizar na no titulo
        $current_month_for_Title = date("n");
        
        // Get the current month
        $current_month = date("n");
        if (isset($_GET['month'])) {
            $current_month = intval($_GET['month']);
        }

        // Get the first day of the current month
        $first_day = mktime(0, 0, 0, $current_month, 1, $current_year);

        // Calculate the start day of the week for the first day of the current month
        $start_day = date("w", $first_day); // 0 (for Sunday) through 6 (for Saturday)

        // Get the number of days in the current month
        $num_days = date("t", $first_day); // Number of days in the month
        ?>


        <h3 id="monthYear">Mês Atual: <?php echo $month_names[$current_month_for_Title - 1] . ' ' . $current_year; ?></h3>
        <form id="filterForm" class="form-inline" >
            <div class="form-group">
                <label for="month" class="mr-2">Selecione o mês:</label>
                <select name="month" id="month" class="form-control">
                    <?php 
                    // Loop through each month and create an option for each
                    // echo '<option value="all">Todos os Meses</option>';
                    for ($month = 1; $month <= 12; $month++) {
                        $selected = ($month == $current_month) ? 'selected' : ''; // Check if it's the current month
                        echo '<option value="' . $month . '" ' . $selected . '>' . $month_names[$month - 1] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group ml-2">
                <label for="year" class="mr-2">Selecione o ano:</label>
                <select name="year" id="year" class="form-control">
                    <?php 
                    // Loop through each year and create an option for each
                    foreach ($years as $year) {
                        $selected = ($year == $selectedYear) ? 'selected' : ''; // Check if it's the selected year
                        echo '<option value="' . $year . '" ' . $selected . '>' . $year . '</option>';
                    }
                    ?>
                </select>
            </div>
            <!-- <button type="submit" class="btn btn-primary">Mostrar Mês</button> -->
        </form>

        <?php 
        // Loop through each month and create a calendar for each
        for ($month = 1; $month <= 12; $month++) {
        ?>
            <div class="calendar-month" data-month="<?php echo $month; ?>" style="<?php echo ($month != $current_month) ? 'display: none;' : ''; ?>">
                <!-- Display the month name -->
                <h4><?php echo $month_names[$month - 1]; ?></h4>
                
                <!-- Display the days of the week -->
                <div class="row ">
                    <?php foreach ($days as $day) : ?>
                        <div class="col calendar-day"><?php echo substr($day, 0, 3); ?></div>
                    <?php endforeach; ?>
                </div>

                <!-- Display the calendar grid -->
                <div class="row">
                    <?php
                    // Fill in the empty cells before the first day of the month
                    for ($i = 0; $i < $start_day; $i++) {
                        echo '<div class="col calendar-day">&nbsp;</div>';
                    }

                    // Display the days of the month
                    for ($day = 1; $day <= $num_days; $day++) {
                        // Get events for this day
                        $events_for_day = getEventsForDay($day, $month, $selectedYear);
                    
                        echo '<div class="col calendar-day">';
                        echo '<div class="card border border-info p-5 day-card shadow p-3 mb-5 bg-body-tertiary rounded d-flex flex-column h-75 position-relative" data-day="' . $day . '" data-month="' . $month . '" data-year="' . $selectedYear . '">';
                        echo '<div class="day-number">' . $day . '</div>'; // Display the day number
                        
                        // Display events for this day
                        if (!empty($events_for_day)) {
                            foreach ($events_for_day as $event) {
                                $user_name = getUserById($event['id_users']);
            
                                echo '<span class="event-text-user" >' . $user_name . '</span>'; // Make user name smaller
                                echo '<span class="event-text-note" >' . $event['title'] . '</span>'; // Make event title smaller
                
                            }
                        }
                        echo '</div>';
                        echo '</div>';
                    
                        // Check if we need to start a new row
                        if (($start_day + $day) % 7 == 0 && $day != $num_days) {
                            echo '</div><div class="row">';
                        }
                    }

                    // Fill in the empty cells after the last day of the month
                    $remaining_days = (7 - (($start_day + $num_days) % 7)) % 7;
                    for ($i = 0; $i < $remaining_days; $i++) {
                        echo '<div class="col calendar-day">&nbsp;</div>';
                    }
                    ?>
                </div>
            </div>
        <?php } ?>
    </div>
</section>


</div>
<?php include(PATH."/module_interface/footer.php");?>

</html>


