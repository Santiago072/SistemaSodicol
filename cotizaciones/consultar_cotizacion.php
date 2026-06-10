<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/CotizacionController.php';

iniciar_sesion_segura();
$data    = (new CotizacionController(conexion()))->consultar();
$urlBase = 'consultar_cotizacion.php?buscando=1';
extract($data);
include '../app/views/cotizaciones/consultar.php';
