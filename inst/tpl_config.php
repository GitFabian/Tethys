<?php

define('TETHYSDB', ':db_name');

Database::set_main_connection(new Database(":server_addr",TETHYSDB,":username",":dbpass"));
