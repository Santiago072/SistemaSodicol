<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/TareaController.php';

iniciar_sesion_segura();
$data = (new TareaController(conexion()))->editar();
extract($data);
include '../app/views/tareas/editar.php';
