
<?php
//require_once('../../PHPMailer/PHPMailerAutoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once('./PHPMailer/src/Exception.php');
require_once('./PHPMailer/src/PHPMailer.php');
require_once('./PHPMailer/src/SMTP.php');
//include_once('./config.php');

header('Content-type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}
$user = isset($_POST['user']) ? $_POST['user'] : '';
$pin = isset($_POST['pin']) ? $_POST['pin'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';

//ENVIO DE CORREO PARA NOTIFICACION DE ASIGNACION
ini_set("sendmail_from", "noreply@gi-sv.com");
//extracion de la informacion que se quiere enviar

/*********************************************************/
//Create a new PHPMailer instance
//if (isset($_GET['accion'])=='M') {
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    //Ask for HTML-friendly debug output
    $mail->Debugoutput = 'html';

    $mail->setFrom("soportesaar@gmail.com"); //$email; //remitente
    //$mail->setFrom($email, "MUSA"); //$email; //remitente
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl'; // 'STARTTLS'; //seguridad
    //Set the hostname of the mail server
    $mail->Host = "smtp.gmail.com"; //"smtp.office365.com"; // servidor smtp, para este caso es el de office, se podria cambiar al de gmail o yahoo
    //Set the SMTP port number - likely to be 25, 465 or 587
    $mail->Port = 465; // 587; //puerto
    $mail->Username = "soportesaar@gmail.com"; //nombre usuario
    $mail->Password = "grmhcnhakxcqfggk"; //contraseña

    //Set who the message is to be sent to
    $mail->addAddress($email/*"email de los destinatarios", "nombre para que se refleje en el correo"*/); //destinatario
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER; //sirve para que envie la traza de las acciones que realiza el API

    $mail->Subject = 'Recuperacion de contraseña';

    $mail->isHTML(true); //permite que el contenido del correo sea HTML
    $mail->CharSet = 'utf-8'; //para que acepte caracteres en español
    $html = "
<html>
<head>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #000000;
            color: #333;
            text-align: center;
            padding: 50px;
            margin: 0;
        }
        .container {
            background: #000;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            max-width: 450px;
            margin: auto;
            border: 1px solid #e1cbb8;
        }
        .header {
            font-size: 26px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 20px;
        }
        .icon {
            margin: 20px 0;
        }
        .icon img {
            width: 120px;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .greeting {
            font-size: 18px;
            color: #fff;
            margin-bottom: 10px;
        }
        .pin {
            font-size: 42px;
            letter-spacing: 8px;
            padding: 15px;
            background: #000;
            color: #fff;
            border-radius: 8px;
			border-width: 5px;
			border-color: #333;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            display: inline-block;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .footer {
            font-size: 18px;
            color: #888;
            font-weight: bold;
            margin-top: 10px;
        }
        .footer img {
            width: 120px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>Recuperación de clave</div>
        <div class='greeting'>Hola $user, aquí tienes tu código de recuperación para que puedas recuperar el acceso rápidamente.</div>
        <div class='greeting'>Código de recuperación</div>
        <div class='pin'>$pin</div>
        <div class='footer'>SAAR - Sistema de Administración Automotriz Rodríguez</div>
    </div>
</body
</html>
";
    $mail->msgHTML($html);
    //echo $html;

    //envío del correo y captura de errores
    if (!$mail->send()) {
        $json = array("status" => 0, "info" => "Correo no se pudo enviar.<br>" . $mail->ErrorInfo);
    } else {
        $json = array("status" => 1, "info" => "Correo enviado.");
    }
} catch (Exception $e) { //manejo de errores
    if (strstr(strtoupper($conn->error), "ERROR")) {
        $json = array("status" => 0, "info" => $e->getMessage() . "(90003)");
        //sendMsg('[MyPets][Nombre del servicio-opcion]ERROR: colocar un error entendible (90032) (colocar las variables del error)');
    } else {
        $json = array("status" => 0, "info" => $e->getMessage() . " No se encontró la cuenta de correo (90004)");
        //sendMsg('[MyPets][Nombre del servicio-opcion]ERROR: colocar un error entendible (90032) (colocar las variables del error)');
    }
}
/*} else {

}*/
