<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/UsuarioController.php';

iniciar_sesion_segura();
$data = (new UsuarioController(conexion()))->editar();
extract($data);
include '../app/views/usuarios/editar.php';
