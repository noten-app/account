<?php

require($_SERVER["DOCUMENT_ROOT"] . "/res/php/session.php");
destroy_session();

header('Location: /');
