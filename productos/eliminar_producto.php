<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/ProductoController.php';

iniciar_sesion_segura();
(new ProductoController(conexion()))->eliminar();
