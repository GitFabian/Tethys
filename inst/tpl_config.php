<?php
/**TPLDOCSTART
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 *
TPLDOCEND*/

define('TETHYSDB', ':db_name');

Database::set_main_connection(new Database(":server_addr",TETHYSDB,":username",":dbpass"));
