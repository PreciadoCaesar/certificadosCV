# conexion para uson en LOCAL
<?php
session_start();
class Conectar {
    protected $dbh;
    protected function conexion() {
        $this->dbh = new PDO(
            "mysql:host=localhost;dbname=u466999701_Certificates;charset=utf8",
            "root",
            ""
        );
        return $this->dbh;
    }
    public function set_names() {
        $this->dbh->exec("SET NAMES 'utf8'");
    }
    public static function ruta(): string {
        return "http://localhost/certifiaco/";
    }
}

if (!defined('BASE_URL')) {
    define('BASE_URL', Conectar::ruta());
}
if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/..'));
}