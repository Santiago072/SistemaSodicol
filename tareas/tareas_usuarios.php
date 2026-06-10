<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/TareaController.php';

iniciar_sesion_segura();
$data    = (new TareaController(conexion()))->gestion();
$urlBase = 'tareas_usuarios.php';
extract($data);
include '../app/views/tareas/gestion.php';
