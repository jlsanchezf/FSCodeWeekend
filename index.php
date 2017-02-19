<?php


/* FUENTES:
 * 
 * https://github.com/marcj/php-rest-service
 */

ini_set('display_errors', 1);
error_reporting(-1);

// set up Composer autoloader
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/api/v1.php';


// use Eloquent ORM
use Illuminate\Database\Capsule\Manager as Capsule;


class Product extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
}

class Inmuebles extends Illuminate\Database\Eloquent\Model {
	public $timestamps = false;
}

class InmueblesDistancias extends Illuminate\Database\Eloquent\Model {
	public $timestamps = false;
}


$cadenaENV = '{
    "cleardb": [
        {
            "credentials": {
                "jdbcUrl": "jdbc:mysql://us-cdbr-iron-east-04.cleardb.net/ad_b45fab7778ffc10?user=b748c1e518a4c0&password=aeec4842",
                "uri": "mysql://b748c1e518a4c0:aeec4842@us-cdbr-iron-east-04.cleardb.net:3306/ad_b45fab7778ffc10?reconnect=true",
                "name": "productos",
                "hostname": "localhost",
                "port": "3306",
                "username": "root",
                "password": ""
            },
            "syslog_drain_url": null,
            "label": "cleardb",
            "provider": null,
            "plan": "spark",
            "name": "ClearDB MySQL Database-7z",
            "tags": [
                "DBA",
                "(S) Cloud",
                "(P) Analytics",
                "Bluemix",
                "Platform",
                "ibm_dedicated_public",
                "Infrastructure",
                "Service",
                "data_management",
                "Analytics",
                "IT Operations",
                "ibm_third_party"
            ]
        }
    ]
}';


// get MySQL service configuration from Bluemix
$services = (!isset ($cadenaENV)) ? getenv("VCAP_SERVICES") : $cadenaENV;
$services_json = json_decode($services, true);
$mysql_config = $services_json["cleardb"][0]["credentials"];
$db = $mysql_config["name"];
$host = $mysql_config["hostname"];
$port = $mysql_config["port"];
$username = $mysql_config["username"];
$password = $mysql_config["password"];

// initialize Eloquent ORM
$capsule = new Capsule;
 
$capsule->addConnection(array(
  'driver'    => 'mysql',
  'host'      => $host,
  'port'      => $port,
  'database'  => $db,
  'username'  => $username,
  'password'  => $password,
  'charset'   => 'utf8',
  'collation' => 'utf8_unicode_ci',
  'prefix'    => ''
));

$capsule->setAsGlobal();
$capsule->bootEloquent();



use RestService\Server;


Server::create('/v1', new API\V1) //base entry points `/admin`
	->setDebugMode(true) //prints the debug trace, line number and file if a exception has been thrown.

	->addGetRoute('hola', 'holaMundo') 									// => /v1/hola

	->addSubController('inmueble', new API\V1\Inmuebles) //
		->addGetRoute('todos', 'obtenerInmuebles') 						// => /v1/inmueble/todos/
		->addGetRoute('([0-9]+)', 'obtenerInmueble') 					// => /v1/inmueble/:idInmueble
		->addGetRoute('([0-9]+)/cercanos', 'obtenerCercanos') 			// => /v1/inmueble/:idInmueble/cercanos
		->addGetRoute('([0-9]+)/cercanos/([0-9]+)', 'obtenerCercanosDistancia') // => /v1/inmueble/:idInmueble/cercanos/:distancia
		->done()
	
	->addSubController('prestamo', new API\V1\Prestamos) //
		->addGetRoute('([0-9]+)/([0-9]+)/([0-9]+)', 'calcularPrestamo') // => /v1/prestamo/importe/meses/interes
	->done()

->run();


?>
