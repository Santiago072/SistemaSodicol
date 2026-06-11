<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../app/models/UsuarioModel.php';
$conexion = conexion();
$model = new UsuarioModel($conexion);

$id = 6;
$doc = "32";
$correo = "santiagolizcanosuarez@gmail.com";

echo "Testing existeDocumentoOCorreo($doc, $correo, $id)...\n";
$existe = $model->existeDocumentoOCorreo($doc, $correo, $id);
var_dump($existe);

echo "\nManual Query:\n";
$stmt = mysqli_prepare($conexion, "SELECT id FROM usuarios WHERE (documento = ? OR correo = ?) AND id != ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ssi", $doc, $correo, $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while($row = mysqli_fetch_assoc($result)) print_r($row);

