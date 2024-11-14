# Projet PRWB 2324 - Groupe d03 - Google Keep

## Notes de version itération 1 

### Liste des utilisateurs et mots de passes

  * boverhaegen@epfc.eu, password "Password1,", utilisateur
  * bepenelle@epfc.eu, password "Password1,", utilisateur
  * xapigeolet@epfc.eu, password "Password1,", utilisateur
  * mamichel@epfc.eu, password "Password1,", utilisateur
  * marie@gmail.com, password "Password1,", utilisateur

### Liste des bugs connus

  * Pas d'utilisation des param2
  * Erreur de variable quand il s'agit d'une checklist
  * Quand 2 items sont les mêmes (dans add_checkliqt_note) il affiche l'erreur sous tous les items et non juste en dessous des items concernés
  * Le menu déroulant se déroule vers le bas et non vèrs la gauche. 

### Liste des fonctionnalités supplémentaires
  * Les inputs gardent les valeurs encodées pour ne pas devoir réécrire si le formulaire n'est pas correctement envoyé.

### Divers

## Notes de version itération 2

### Liste des utilisateurs et mots de passes

  * boverhaegen@epfc.eu, password "Password1,", utilisateur
  * bepenelle@epfc.eu, password "Password1,", utilisateur
  * xapigeolet@epfc.eu, password "Password1,", utilisateur
  * mamichel@epfc.eu, password "Password1,", utilisateur

### Liste des bugs connus

  * delete_item : Si je rajoute un nouvel item je ne sais pas le delete car il ne connait pas l'id. 
  * Fenêtre modale pour delete une note archivée. Pas au bon endroit. On s'est trompé. Le delete est dans archive. On rajouetra une delete au bon endroit pour la prochaine itération
  * Constantes ne sont pas dans le fichier config
  * Drag and drog pas fonctionnel
    * Quand 2 items sont les mêmes (dans add_checkliqt_note) il affiche l'erreur sous tous les items et non juste en dessous des items concernés
  * Le menu déroulant se déroule vers le bas et non vèrs la gauche.

 

## Notes de version itération 3 

### Liste des utilisateurs et mots de passes

  * boverhaegen@epfc.eu, password "Password1,", utilisateur
  * bepenelle@epfc.eu, password "Password1,", utilisateur
  * xapigeolet@epfc.eu, password "Password1,", utilisateur
  * mamichel@epfc.eu, password "Password1,", utilisateur

### ### Liste des bugs connus

  * add_checklist : sans js: ajout item identique: message erreur apparait partout, ajout title exitant n'affiche pas erreur sinon envoie a une autre page, js: title en vert si on ajoute title similaire et n'affiche pas msg erreur
  * edit_checklist_note : erreur double cle prise en charge mais pas affichée dans l'appli
  * Drag and drop : Le poids est bien unique mais si je bouge une card qui à un poid de 10 vers la gauche d'une carde qui a un poids de 8. il met le poids à 9
  * delete item , delete label: Si je rajoute un nouvel item je ne sais pas le delete car il ne connait pas l'id. il faut rafraichir la page

