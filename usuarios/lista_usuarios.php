<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/UsuarioController.php';

iniciar_sesion_segura();
$data    = (new UsuarioController(conexion()))->listar();
$urlBase = 'lista_usuarios.php' . (!empty($data['busqueda']) ? '?busqueda=' . urlencode($data['busqueda']) : '');
extract($data);
include '../app/views/usuarios/lista.php';
