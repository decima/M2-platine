@title:Créer un module

##Présentation des modules##
Deux types de modules existent : 
* Les Modules Systèmes
  
  Ces modules, une fois créés et installés, ne peuvent être retirés. Ils composent ainsi le coeur du CMS. Jinn dépend entièrement de ces modules et sans eux, il est impossible de faire fonctionner le système.




* Les modules classiques
  
  Ces modules sont exportables et installables sur toutes les installations de jinn. Ce tutoriel va vous expliquer comment créer ces modules.



##Exemple de création de module##

Créons un module d'exemple qui s'appelera Sondage. 
Le but de ce module est de permettre aux utilisateurs connectés de répondre à la question : "Quelle est votre couleur préférée ?" de manière anonyme.

###Structure du module###
```http
/Sondage
    /module.php
```

Il suffit d'ajouter tous les éléments nécessaires au module et les inclure directement dans module.php pour les utiliser.

Voici le code par défaut du module :
```php
<?php
/*
 * @moduleName Sondage
 */

class Sondage implements Module {

    public function info() {
        return array(
            "name" => "Sondage", //nom réel du module
            "readablename" => "Sondage de couleur", //Nom affiché du module
            "dependencies" => array("User"), //modules dépendants de ce module
        );
    }
}
```
Le commentaire @moduleName est un commentaire utilisé pour retrouver le module plus rapidement.
Chaque module doit implémenter l'interface Module.
```
interface Module {
    public function info();
}
```

###Base de données##
Pour enregistrer les résultats, nous avons besoin d'une base de données.
Pour cela, il suffit d'ajouter une méthode _schema_ contenant la définition des tables nécessaires au module :
```php
public function schema($tables = array()) {
    $tables['resultat_sondage'] = array(
        //Entier en clé primaire avec auto incrémentation
        "resultat_id" => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY + Database::AUTOINCREMENT,
            
        //entier non null
        "participant" => Database::FIELD_TYPE_INT + Database::NOTNULLVAL,
            
        //chaine de caractère non null
        "vote" => Database::FIELD_TYPE_STRING + Database::NOTNULLVAL,
            
        //entier
        "age_participant" => Database::FIELD_TYPE_INT,
    );
    return $tables;
}
```

&Agrave; l'installation du module cette méthode sera appelée pour créer la base de données.
Pour une manipulation plus simple de la table que nous avons, on peut créer une autre classe : 
```php
class SondageObject extends DataObject {
    public function index() { return array("resultat_id");} //tableau des noms des clés primaires
    public function tableName() { return "resultat_sondage";} //nom de la table
}
```
Avec cet objet *SondageObject*, on peut maintenant manipuler de manière simple la table : 
* Insertion d'un nouveau résultat au sondage : 
  ```php
<?php
    $sondage = new SondageObject();
    
    $sondage->participant = 1;
    
    $sondage->vote = "bleu";

    $sondage->age_participant = 22;

    $sondage->save();
```  
* Modification d'un résultat au sondage : 
  ```php
<?php
    $sondage = new SondageObject();

    //chargement du résultat result_id 27
    $loaded = $sondage->load(array('resultat_id'=>27);

    if($loaded){

        $sondage->vote = "vert";

        $sondage->save();
    }
```
* Supprimer un résultat au sondage :
  ```php
<?php
        $s = new SondageObject();
        if( $s->load("resultat_id"=>27) )
            $s->delete();
```

* Récupérer tous les éléments de la table :
   ```php
<?php
        SondageObject()::loadAll();
```

D'autres exemples d'utilisations de cette classe : 
* Donner des valeurs par défaut aux champs de la table en les passant dans le constructeur : 
  ```php
    public function __construct(){
        $sondage->age_participant = 18;
    }
```
* Vérifier que le champs contient bien une valeur particulière (ou est unique) :
  ```php
    public function __set($key , $value){
        if($key == "age_participant"){
            if($value < 1){
                throw new Exception();
            }
        }
        parent::__set($key, $value);
    }
```
* On peut également définir ses propres méthodes de chargement : 
  ```php
    public function load($id){
        return parent::load(array('resultat_id' => $id);    
    }
    public function load_by_user($user_id){
        return parent::load(array('participant' => $user_id));
    }
```    