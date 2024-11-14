<?php

require_once "framework/Model.php"; // C'est lui qui se connecte à la db
require_once "model/Notes.php";
require_once "model/CheckListNoteItem.php";

class CheckListNotes extends Notes {

    // Propriété pour stocker les éléments de la checklist (contains)
    public array $checkListItems = [];
    
    public function __construct(
        public string $title,
        public Users $owner,
        public string $createdAt,
        public ?string $editedAt,
        public bool $pinned,
        public bool $archived,
        public int $weight,
        public ?int $noteId = null,
        public ?int $id = null
    ){
        parent::__construct($title, $owner, $createdAt, $editedAt, $pinned, $archived, $weight, $noteId);
    }


    // ---------------Méthode parent / commune -------------------
    public function openNote() : Notes{
        return $this; // Retourne l'instance actuelle de TextNotes
    }

    // Récupérer une note. Et les infos de son parent
     // ORDER BY notes.created_at ASC,  checked ASC
    public static function getChecListkNoteId(int $noteId) : CheckListNotes|false{
        $query = self::execute("SELECT * FROM checklist_notes join notes on notes.id = checklist_notes.id 
        where notes.id = :noteId "  , ["noteId" => $noteId]);
        $reponse = $query->fetch();
        if($query->rowCount() > 0) {
            $id = $reponse['id'];
            if($id !== null) {
                $checkListNote = new CheckListNotes(
                    $reponse["title"],
                    Users::getUserById($reponse["owner"]),
                    $reponse["created_at"],
                    $reponse["edited_at"],
                    (bool) $reponse["pinned"],
                    (bool) $reponse["archived"],
                    $reponse["weight"],
                    $id
                );
                $checkListItems = CheckListNoteItem::getAllNote($noteId);
                foreach ($checkListItems as $checkListItem) {
                    $checkListNote->addCheckListItem($checkListItem);
                }
                return $checkListNote;
            }
        }
        return false;
    }
    

    // J'ai besoin d'une méthode qui va remplir mon tableau checkListItems
    public function addCheckListItem(CheckListNoteItem $checkListItem) : void {
        $this->checkListItems[] = $checkListItem;
    }
     // Méthode pour récupérer tous les éléments de la checklist
     public function getAllCheckListNoteItems(): array {
        $checkListItems = CheckListNoteItem::getAllNote($this->noteId);
        // Ajouter les éléments de la checklist à l'instance de CheckListNotes
        foreach ($checkListItems as $checkListItem) {
            $this->addCheckListItem($checkListItem);
        }

        return $checkListItems;
    }

    // sauver la note et l'id dans cette table intermédiaire
    public function saveNote() : void{

        if($this->noteId !== null) {
            self::execute("UPDATE notes SET title = :title, edited_at = :editedAt WHERE id = :noteId", ["title" => $this->title, "noteId" => $this->noteId, "editedAt" => $this->editedAt]);
        } else {
            $existingNote = self::execute("SELECT COUNT(*) as count FROM notes WHERE owner = :owner AND title = :title", ["owner" => $this->owner->id, "title" => $this->title])->fetch();
            if ($existingNote["count"] > 0) {
                throw new Exception("A note with the same owner and title already exists.");
            }
            //if($existingNote == false) {
                if($this->weight==1){
                

                    $addWeight = $this->weight + 1;
    
                    // Vérifier si le poids n'existe pas déjà
                    $unique = self::execute("SELECT COUNT(*) as count FROM notes WHERE  weight = :addWeight", [ "addWeight" => $addWeight]);
                    $resultUnique = $unique->fetch();
                
                    // Parcourir les poids jusqu'à trouver la "place disponible"
                    while($resultUnique["count"] > 0) {
                        $addWeight++;
                        // Revérifier que le nouveau poids est unique
                        $unique = self::execute("SELECT COUNT(*) as count FROM notes WHERE weight = :addWeight", ["addWeight" => $addWeight]);
                        $resultUnique = $unique->fetch();
                    }
                    $this->weight = $addWeight;
                    
                }
                self::execute("INSERT INTO notes(owner, title, weight) VALUES(:owner, :title, :weight)", ["owner" => $this->owner->id, "title" => $this->title, "weight" => $this->weight]);
                // Récupérer l'identifiant généré lors de l'insertion dans la table 'notes'
                $this->noteId = self::lastInsertId();
                // insertion dans la table checklistNote de l'i
                self::execute("INSERT into checklist_notes(id) VALUES(:noteId)", ["noteId" => $this->noteId]);

                Notes::newWeightAdd($this->noteId);
           // }

        }
    }

    public function validateTitle(string $title) : array {
        $errors = [];
        if(strlen($title) < Configuration::get("title_min_size") && strlen($title) > 0) {
            $errors['titleEmpty'] = "The title must be bigger than 2  ";
        }
        if(empty($title) ) {
            $errors['titleSize'] = "Title cannot be empty";
        }
        if(strlen($title) >= Configuration::get("title_max_size") ) {
            $errors['titleBigSize'] = "Title is too big. Max 25";
        }
        return $errors;
    }

  
    public function delete() : void {
        if($this->noteId !== null) {
            self::execute("DELETE FROM checklist_note_items WHERE checklist_note IN (SELECT id FROM checklist_notes WHERE id = :noteId)", ["noteId" => $this->noteId]);
            self::execute("DELETE FROM checklist_notes WHERE id = :noteId", ["noteId" => $this->noteId]);
            self::execute("DELETE FROM notes WHERE id = :noteId;", ["noteId" => $this->noteId]);
        }
  } 

  public static function updatePinnedStatus(int $noteId, bool $pinned) : void {
    self::execute("UPDATE notes SET pinned = :pinned WHERE id = :noteId", ["noteId" => $noteId, "pinned" => $pinned ? 1 : 0]);
    }

    public function isPinned(): bool {
        return $this->pinned;
    }

}