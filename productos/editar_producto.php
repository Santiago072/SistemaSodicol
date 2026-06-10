<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/ProductoController.php';

iniciar_sesion_segura();
$data = (new ProductoController(conexion()))->editar();
extract($data);
include '../app/views/productos/editar.php';
