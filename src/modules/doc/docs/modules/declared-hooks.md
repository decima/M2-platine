@title:Hooks de référence

###Modules###

Méthode appelée à l'installation du module
* retourner **TRUE** si le module a bien été installé
* retourner **FALSE** dans le cas contraire
```php
    public function install();
```

-------------------------------------------------------------------
Méthode appelée à l'activation du module
* retourner **TRUE** si le module a bien été activé
* retourner **FALSE** dans le cas contraire
```php
    public function enable();
```