<?php
require_once 'config/conexion.php';
require_once 'config/seguridad.php';
require_once 'app/controllers/PanelController.php';

iniciar_sesion_segura();
$data = (new PanelController(conexion()))->index();
extract($data);
include 'app/views/panel/index.php';
