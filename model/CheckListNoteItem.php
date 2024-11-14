<?php
require_once "framework/Model.php"; // C'est lui qui se connecte à la db
// require_once "model/Notes.php";
require_once "model/CheckListNotes.php";

class CheckListNoteItem extends Model {
    public function __construct(public ?int $id, public int $checkListNote, public string $content, public bool $checked){}

    public static function checkNoteId(int $checkId) : array{
        $query = self::execute("SELECT id, checklist_note, content, checked
            FROM checklist_note_items
            WHERE checklist_note = :checkId", ["checkId" => $checkId]);
        $reponse = $query->fetchAll();
        $checkTab = [];
        foreach($reponse as $row) {
            $checkTab[] = new CheckListNoteItem($row["id"], $row['checklist_note'], $row['content'], boolval($row['checked']));
        }
        return $checkTab;
    }
    public static function get_by_id($id) : CheckListNoteItem|false {
        $query = self::execute("SELECT * FROM FROM checklist_note_items
        WHERE checklist_note = :checkId", ["checkId" => $id]);
        $data = $query->fetch();

        if ($query->rowCount() != 0) {
            return new CheckListNoteItem( 
                $data["checkId"],
                $data["checklist_note"],
                $data["content"],
                $data['checked']);
        }
        return false;
    }
    // méthode pour avoir toutes les lignes de notes
    public static function getAllNote(int $checklistNoteId) {
        $query = self::execute("SELECT * FROM checklist_note_items WHERE checklist_note = :checklistNoteId", ["checklistNoteId" => $checklistNoteId]);
        $reponse = $query->fetchAll();
        $checkTab = [];

        foreach ($reponse as $row) {
            $checkTab[] = new CheckListNoteItem($row["id"], $row['checklist_note'], $row['content'], boolval($row['checked']));
        }
        return $checkTab;
    }

    public static function validateTitle(string $title) : array {
        $errors = [];
        if(strlen($title) == 0) {
            $errors["emptyTitle"] = "Title is empty";
        }
        if(( strlen($title) > 0 && strlen($title) < Configuration::get("title_min_size")) || strlen($title) > 25 ) {
            $errors["titleLenght"] = "The title must be between 3 and 25";
        }
        return $errors;
    }
    public static function validateInputCheck(array $inCheck) : array {
        $errors = [];
        $values = []; //tableau avec chaque valeur de chaque input
        foreach($inCheck as $v) {
            if (in_array($v, $values)) {
                $errors["sameInput"] = "This input must be unique";
            } 
            else {
                $values[] = $v;
                //var_dump($values);
            }
        }

        return $errors;
    }

    public function saveNote()  {
        if($this->id) { // Vérifie si la note existe dans la db
            // Alors je mets à jour la note
            self::execute("UPDATE checklist_note_items SET  checklist_note = :checlistNote, content = :content, checked =:checked", 
            ["checlistNote" => $this->checkListNote, "content"=> $this->content, "checked"=> $this->checked] );
        } else {
            self::execute("INSERT INTO checklist_note_items(checklist_note, content, checked) VALUES(:checklistNote, :content, :checked)", [
                "checklistNote" => $this->checkListNote,
                "content" => $this->content,
                "checked" => $this->checked
            ]);
            // Récupérer l'identifiant généré lors de l'insertion dans la table 'checklist_note_items'
            $this->id = self::lastInsertId();
        }
    }

    public static function saveStatutCheck(int $itemId) : void {

            // Récupérer la valeur actuelle de checked dans la base de données
    $currentCheckedValue = self::getCheckedValue($itemId);

    // Inverser la valeur de checked
    $newCheckedValue = ($currentCheckedValue == 1) ? 0 : 1;
        self::execute("UPDATE checklist_note_items SET checked =:checked WHERE id =:itemId",
        ["checked"=> $newCheckedValue, "itemId" => $itemId]);
    }

    private static function getCheckedValue(int $itemId): int {
        // Récupérer la valeur actuelle de checked depuis la base de données
        $result = self::execute("SELECT checked FROM checklist_note_items WHERE id = :itemId",
            ["itemId" => $itemId]);
        $row = $result->fetch();
        // Retourner la valeur actuelle de checked
        return ($result) ? (int)$row['checked'] : 0;
    }

    public static function deleteChecklistItem(int $id): void {
        self::execute("DELETE FROM checklist_note_items WHERE id = :id", ["id" => $id]);
    }
    public function validateItem(string $item) : array {
        $errors = [];
        if(!isset($item) || trim($item) == "") {
            $errors['itemEmpty'] = "The item must be bigger than 0 ";
        }
        if(strlen($item) > Configuration::get("item_max_length")) {
            $errors['itemBigSize'] = "Title is too big. Max 60a";
        }
        return $errors;
    }

}    