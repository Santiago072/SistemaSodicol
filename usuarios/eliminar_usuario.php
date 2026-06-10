<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/UsuarioController.php';

iniciar_sesion_segura();
$conexion   = conexion();
$controller = new UsuarioController($conexion);
$controller->eliminar();
