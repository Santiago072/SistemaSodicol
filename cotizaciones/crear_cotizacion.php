<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/CotizacionController.php';

iniciar_sesion_segura();
$data = (new CotizacionController(conexion()))->crear();
extract($data);
include '../app/views/cotizaciones/crear.php';
