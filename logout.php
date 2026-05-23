<?php
require_once 'config/seguridad.php';

iniciar_sesion_segura();
session_unset();
session_destroy();
header("location: index.php");
exit();
?>