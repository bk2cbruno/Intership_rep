<?php

include(PATH."/module_bydas_hr/functions.php");

configSetField("bydas_hr","enabled",1,"Módulo Ativo","boolean",1);

define("MODULE_BYDAS_HR_DB",PATH."/data/bydas_hr.db");

define("MODULE_BYDAS_HR_DB_BACKUP",PATH."/backup/bydas_hr-".date("d")."-".date("m").".db");

if (!is_file(MODULE_BYDAS_HR_DB)){

    $data=array(
        "id INTEGER PRIMARY KEY AUTOINCREMENT",
        "id_users INTEGER DEFAULT '0'",
        "year INTEGER DEFAULT '0'",
        "month INTEGER DEFAULT '0'",
        "day INTEGER DEFAULT '0'",
        "title TEXT NULL",
        "notes TEXT NULL",
        "modified INTEGER DEFAULT '0'",
        "created INTEGER DEFAULT '0'"
    );

    sqliteCreate(MODULE_BYDAS_HR_DB,"calendar",$data);

}

if (defined("MODULE_BYDAS_HR_DB_BACKUP") && !is_file(MODULE_BYDAS_HR_DB_BACKUP)){
    copy(MODULE_BYDAS_HR_DB,MODULE_BYDAS_HR_DB_BACKUP);
}

?>
