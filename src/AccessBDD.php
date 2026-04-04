<?php
include_once("Connexion.php");

/**
 * Classe qui sollicite ConnexionBDD pour l'accès à la BDD MySQL
 * Elle contient les méthodes appelées par Controle
 * et les méthodes abstraites que MyAccessBDD doit redéfinir pour construire les requêtes
 */
abstract class AccessBDD {
	
    /**
     * Instance de la connexion à la base de données
     * @var Connexion
     */
    protected $conn = null;	

    /**
     * constructeur : récupère les variables d'environnement 
     * et récupère l'instance de connexion à la BDD
     * @throws Exception En cas d'erreur de connexion à la base de données
     */
    protected function __construct(){
        try{
            // récupération des variables d'environnement de l'accès à la BDD 
            $login = htmlspecialchars($_ENV['BDD_LOGIN'] ?? '');
            $pwd = htmlspecialchars($_ENV['BDD_PWD'] ?? '');
            $bd = htmlspecialchars($_ENV['BDD_BD'] ?? '');
            $server = htmlspecialchars($_ENV['BDD_SERVER'] ?? '');
            $port = htmlspecialchars($_ENV['BDD_PORT'] ?? '');    
            // création de la connexion à la BDD
            $this->conn = Connexion::getInstance($login, $pwd, $bd, $server, $port);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Point d'entrée pour traiter les demandes de données
     * Oriente vers la méthode de traitement spécifique selon le verbe HTTP
     * @param string $methodeHTTP La méthode HTTP (GET, POST, PUT, DELETE)
     * @param string $table Le nom de la table concernée
     * @param string|null $id L'identifiant de la ressource (optionnel)
     * @param array|null $champs Liste des champs et valeurs à traiter
     * @return array|int|null Retourne un tableau de résultats (GET), un entier (ID inséré ou lignes impactées), ou null en cas d'échec
     */
    public function demande(string $methodeHTTP, string $table, ?string $id, ?array $champs) : array|int|null {
        if(is_null($this->conn)){
            return null;
        }
        switch ($methodeHTTP){
            case 'GET' : 
                return $this->traitementSelect($table, $champs);
            case 'POST' : 
                return $this->traitementInsert($table, $champs);
            case 'PUT' : 
                return $this->traitementUpdate($table, $id, $champs);
            case 'DELETE' : 
                return $this->traitementDelete($table, $champs);
            default :
                return null;
        }       
    }

    abstract protected function traitementSelect(string $table, ?array $champs) : ?array;
    abstract protected function traitementInsert(string $table, ?array $champs) : ?int;
    abstract protected function traitementUpdate(string $table, ?string $id, ?array $champs) : ?int;
    abstract protected function traitementDelete(string $table, ?array $champs) : ?int;

}
