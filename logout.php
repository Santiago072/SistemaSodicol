<?php
require_once 'config/conexion.php';
require_once 'config/seguridad.php';
require_once 'app/controllers/AuthController.php';

iniciar_sesion_segura();
(new AuthController(conexion()))->logout();
