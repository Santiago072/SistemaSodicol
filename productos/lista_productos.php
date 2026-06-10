<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/ProductoController.php';

iniciar_sesion_segura();
$data    = (new ProductoController(conexion()))->listar();
$urlBase = 'lista_productos.php' . (!empty($data['busqueda']) ? '?busqueda=' . urlencode($data['busqueda']) : '');
extract($data);
include '../app/views/productos/lista.php';
