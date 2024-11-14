<?php
require_once 'framework/Controller.php';
require_once "model/Notes.php";
require_once "model/TextNotes.php";
require_once "model/CheckListNotes.php";
require_once "model/Labels.php";

class ControllerNotes extends Controller {
    
    public function index() : void {
        $user = $this->get_user_or_redirect(); // Récupérer l'utilisateur connecté
        $autheur = $this->getAuthorNote($user); // récupéer l'autheur de la note
        $notes = $autheur->getNotes(); // récupérer un tab note par rapport à son auteur
        $checklistItems = []; // les checklists
        
        $pinnedNotes = $autheur->getPinnedNotes($autheur);
        $otherNotes = $autheur->getOtherNotes($user);
        $labels = [];
        // Les labels -----------
        foreach($pinnedNotes as $pn) {
            // prends le label correspondant à l'id de la note
            $labels[$pn->noteId]  = labels::getLabelsByNote($pn->noteId);
        }
        foreach($otherNotes as $on) {
            $labels[$on->noteId]  = labels::getLabelsByNote($on->noteId);
        }
        (new View("notes"))->show(["notes" => $notes, "checkList" => $checklistItems, "pinnedNotes" => $pinnedNotes, "others" => $otherNotes, "userId" => $autheur->id, "labels" => $labels ]);
    }

    // Gestion du parametre dans l'url
    private function getAuthorNote(Users $moi) : Users|false {
        if(!isset($_GET["param1"]) || $_GET["param1"] == "") {
            return $moi;
        } else {
            return Users::getUserById($_GET["param1"]);
        }
    }

    // Page qui ajoute des checking notes
    public function addChecklistgNote() : void {

        (new View("add_checklist_note"))->show();
    }

    // page pour les archives
    public function archives() : void {
        $user = $this->get_user_or_redirect(); // Récupérer l'utilisateur connecté
        $autheur = $this->getAuthorNote($user); // récupéer l'autheur de la note
        $archives = Notes::getArchives($autheur);

        // les labels ------
        $labels = [];
        // pour chaque note archivee
        foreach($archives as $a) {
            // récupérer la valeur associée à la clé qui est l'id de la note
            $labels[$a->noteId] = Labels::getLabelsByNote($a->noteId);
        }
        (new View("archives"))->show(["archives" => $archives, "labels" => $labels]);
    }

    public function add_text_note() : void {
       $user = $this->get_user_or_redirect(); // Récupérer l'utilisateur connecté
       $userId = Users::getUserById($user->id);
        (new View("add_text_note"))->show(["user"=>$user, "userId" =>$userId]);
    }
    // Sauvegarder un textNote
    public function saveNote() : void {
        $title = $_POST["title"];
        $text = $_POST["text"];
        $user = $this->get_user_or_redirect();
        $currentDateTime = date("Y-m-d H:i:s");
        $errors = [];
        
        $textNote = new TextNotes($title,$user,$currentDateTime,null,0,0,0,$text);
        $errors = array_merge($errors, $textNote::validateTitle($title));
        if(empty($errors)) {
            $textNote->saveNote();
            self::redirect("Notes", "index");
        }
        (new View("add_text_note"))->show(["errors" => $errors] );
    }
    public function add_checkList_note() : void {
        $errors = [];
        $user = $this->get_user_or_redirect();
        $memoriseInput = []; // tableau pour garder les input rempli si il y a une erreur
        $currentDateTime = date("Y-m-d H:i:s");

        if(isset($_POST["title"])) {
            $title = $_POST["title"];

            // Vérifier les règles métier du titre
            $errors = array_merge($errors, CheckListNoteItem::validateTitle($title));   
            
            // Les inputs 
            $inputValues = [];
            for ($i = 0; $i <= 6; $i++) {
                if(isset($_POST["items".$i]) && strlen($_POST["items".$i]) > 0){
                    //var_dump($_POST["items".$i]);
                    $inputValues[] = $_POST["items".$i];
                    // Vérifier les règles métier des input cpour checkbox
                    $errors = array_merge($errors, CheckListNoteItem::validateInputCheck($inputValues));
                    // retenir ce qu'il y a dans les input pour pas devoir réécrire
                    $memoriseInput["items".$i] = $_POST["items".$i];
                }
            }
            // Si pas d'erreur on sauvegarde
            if(empty($errors)) { 
                $essai = new CheckListNotes($title, $user,$currentDateTime,null, 0, 0, 1);
                $essai->saveNote();

                 // Ajouter les éléments de checklist à la checklist principale
                for ($i = 0; $i < count($inputValues); $i++) {
                    $checkListItem = new CheckListNoteItem(null, $essai->noteId, $inputValues[$i], false);
                    $checkListItem->saveNote();
                }
                // controller, méthode, param
                self::redirect("Notes", "index");

                // pour gérer le non renvoi du formulaire
                if(isset($_GET["param1"]) && $_GET["param1"] == $checkListItem->id ) {
                    $success = "A finir";
                }
            } else { // si il reste des erreurs je retiens ce que j'avais écris dans mes inputs
                $memoriseInput["title"] = $_POST["title"];
            }        
        }
        (new View("add_checklist_note"))->show(["user"=>$user, "errors"=>$errors, "memoriseInput" => $memoriseInput]);
    }

    

    //  Pour montrer un TextNote
    public function open_text_note() : void {
        $user = $this->get_user_or_redirect();
        $user->hasAccessToNoteOrAbort(isset($_GET["param1"]) ? $_GET['param1'] : ""); // valider para<<<<<<<<<m1

       if(isset($_GET["param1"])) { 
        $noteId = $_GET["param1"];
        $maNote = TextNotes::getContentNoteById($noteId);
        $newPinnedStatus = $maNote->pinned;
        $estPartagee = Notes::noteArchivee($noteId);

    }
        (new View("open_text_note"))->show(["maNote" => $maNote, "newPinnedStatus"=> $newPinnedStatus, "estPartagee"=> $estPartagee] );
    }

    // Uand c'est une checklist
    public function open_checklist() : void {
        $user = $this->get_user_or_redirect(); // récuperer l'objet user connecté 
        $user->hasAccessToNoteOrAbort(isset($_GET["param1"]) ? $_GET['param1'] : "");
        
        if(isset($_GET["param1"])) {
            $noteId = $_GET["param1"];
            $mesCheckList = CheckListNotes::getChecListkNoteId($noteId);
            $newPinnedStatus = $mesCheckList->pinned;
            $estPartagee = Notes::noteArchivee($noteId);
            // Quand je coche une checkbox
            if(isset($_POST["item_id"])) {
                $itemId = $_POST['item_id'];

                CheckListNoteItem::saveStatutCheck($itemId);

                self::redirect("Notes", "open_checklist", $noteId);  
            }
        }
        (new View("open_checklist_note"))->show(["maNote"=> $mesCheckList,  "id"=>$noteId, "newPinnedStatus"=> $newPinnedStatus, "estPartagee"=> $estPartagee]); 
    }
    // Pour éditier un textNote
    public function edit_text_note() : void {
        $user = $this->get_user_or_redirect();
        $user->hasAccessToNoteOrAbort($_GET["param1"]);
        $errors = [];
        $maNote = null;

        $maNote = TextNotes::getContentNoteById($_GET["param1"]);

        if(isset($_POST) && $_POST != "" ){
        if(isset($_POST["title"]) && isset($_POST["texte"]) && isset($_POST["idNote"])) {
            $title = Tools::sanitize($_POST["title"]);
            $texte = Tools::sanitize($_POST["texte"]);
            $currentDateTime = date("Y-m-d H:i:s");
            // je récupère la note et je garde sa date de creation
            $updateMaNote = new TextNotes($title,
                            $maNote->owner,
                            $maNote->createdAt,
                            $currentDateTime
                            ,0,0,0,
                            $texte,
                            $_POST["idNote"]);
            // Je vérifie les règles métier du titre
            $errors = $updateMaNote->validateTitle($title);
            
            if(empty($errors)) {
                $updateMaNote->saveNote(); // update la note dans la db
                self::redirect("Notes", "open_text_note", $maNote->noteId);
            }   
        }
    }
        // Je peux éditer
         (new View("edit_text_note"))->show(["maNote" => $maNote, "errors"=>$errors]);
    }

    // archiver une note
    public function archive_text_note() : void {
        if (!isset($_GET["param1"]) || !is_numeric($_GET["param1"])) {
            Tools::abort("Invalid URL parameter. Parameter must be numeric.");
        }
        $noteId = $_GET["param1"];

        $maNote = TextNotes::getContentNoteById($noteId);
        $checkListNote = CheckListNotes::getChecListkNoteId($noteId);
        if($maNote) {
            $maNote->archiveNote();
            self::redirect("Notes", "open_text_note", $maNote->noteId );
        }
        if($checkListNote) {
            $checkListNote->archiveNote();
            self::redirect("Notes", "open_checklist", $checkListNote->noteId);
        }
    }

    // Aller vers la page qui archive une note en particulier
    public function open_archived_note() : void {
        $user = $this->get_user_or_redirect();
        $user->hasAccessToNoteOrAbort(isset($_GET["param1"]) ? $_GET['param1'] : "");

        $noteId = $_GET["param1"];
        $maNote = TextNotes::getContentNoteById($noteId);
        // pour afficher le contenu
        $maChecklistNote = CheckListNotes::getChecListkNoteId($noteId);
        $estPartagee = Notes::notePartage($noteId);
        
        (new View("archive"))->show(["maNote" => $maNote, "maChecklistNote" => $maChecklistNote, "estPartagee" => $estPartagee]);
    }
    // désarchiver une note
    public function un_archive_text_note() : void {
        if (!isset($_GET["param1"]) || !is_numeric($_GET["param1"])) {
            Tools::abort("Invalid URL parameter. Parameter must be numeric.");
        }
        $noteId = $_GET["param1"];
        $maNote = TextNotes::getContentNoteById($noteId);
        $maChecklistNote = CheckListNotes::getChecListkNoteId($noteId);

        if($maNote) {
            $maNote->unArchiveNote();
            self::redirect("Notes", "index");
        }
        if($maChecklistNote) {
            $maChecklistNote->unArchiveNote();
            self::redirect("Notes", "index");
        }
    }


    public function delete_note() : void {
        $user = $this->get_user_or_redirect();
        $user->hasAccessToNoteOrAbort(isset($_GET["param1"]) ? $_GET['param1'] : "");
        $noteId = $_GET["param1"];
        $maNote = TextNotes::getContentNoteById($noteId);
        $maChecklistNote = CheckListNotes::getChecListkNoteId($noteId);

        if($maNote || $maChecklistNote) {  }
            // confirmation 
            if(isset($noteId)) {
                $idNote = $_GET["param1"];
                if(isset($_POST["delete"])) {
                    if($maNote) {
                        $maNote->delete(); 
                        $maNote->unArchiveNote();
                        self::redirect("Notes", "index"); 
                    }
                    else if($maChecklistNote) {
                        $maChecklistNote->delete_checkList();
                        $maChecklistNote->unArchiveNote();
                        self::redirect("Notes", "index"); 
                    }
                }
            } else {
                Tools::abort("Pas d'id");
            }
    }

    public function delete() : void {
        $user = $this->get_user_or_redirect();
        $user->hasAccessToNoteOrAbort(isset($_GET["param1"]) ? $_GET['param1'] : ""); 
        $noteId = $_GET["param1"];
        $maNote = TextNotes::getContentNoteById($noteId);
        $maChecklistNote = CheckListNotes::getChecListkNoteId($noteId);

        (new View("delete_confirm"))->show(["maNote" => $maNote, "maCheck" => $maChecklistNote, "noteId" => $noteId] );
    }

    
    public function labels() : void {
        $user = $this->get_user_or_redirect(); // récuperer l'objet user connecté 
        $user->hasAccessToNoteOrAbort(isset($_GET["param1"]) ? $_GET['param1'] : ""); 
        $noteId = $_GET["param1"];
        $maNote = TextNotes::getContentNoteById($noteId);

        $errors = [];
        $maChecklistNote = CheckListNotes::getChecListkNoteId($noteId);
       // Tous les label d'une note
        $LabelsByNote = Labels::getLabelsByNote($noteId);
        // le texte d'un label en particulier
        $oneLabel = Labels::getLabelsById($noteId);
        if($maChecklistNote) {
            if(isset($_POST['newItem'])) {
                $label = Tools::sanitize($_POST['newItem']);

                $errors = $oneLabel->validate($label,$noteId);
                if(empty($errors)) {
                    $oneLabel->saveLabel($label,$noteId); 
                    self::redirect("Notes", "labels", $noteId);  
                } 
            }
            if(isset($_GET["param2"])) {
                $label = $_GET["param2"]; 
                $this->redirect("Notes","deleteLabel", $noteId, $label);
            }
        }
        if($maNote) {

            if(isset($_POST['newItem'])) {
                $label = Tools::sanitize($_POST['newItem']);
                $errors = $oneLabel->validate($label,$noteId);
                if(empty($errors)) {
                    $oneLabel->saveLabel($label,$noteId); 
                    self::redirect("Notes", "labels", $noteId);
                }
            }

            if(isset($_GET["param2"])) {
                $label = $_GET["param2"]; 
                $this->redirect("Notes","deleteLabel", $noteId, $label);
            }
        }

        (new View("label"))->show(["maNote" => $maNote, "errors" => $errors, "myLabel" =>$oneLabel, "LabelsByNote" => $LabelsByNote, "noteId" => $noteId]);
    }
        // delete dans la db
        public function deleteLabel( ) {
            if(!empty($_GET["param1"]) && $_GET["param1"] != "") {
               if($_GET["param2"] && $_GET["param2"] != "") {
                   $noteId = (int)$_GET["param1"];
                   $label = $_GET["param2"];
                   labels::deleteLabel($noteId, $label);
                   self::redirect("Notes", "labels", $noteId);
               }
            }
       }

    public function search() : void {
        $user = $this->get_user_or_redirect(); // Récupérer l'utilisateur connecté
        $autheur = $this->getAuthorNote($user); // récupérer l'auteur de la note
        $notes = $autheur->getNotes(); // récupérer un tableau de notes par rapport à son auteur
        $labels = $autheur->getLabels($notes);

         $pinnedNotes = $autheur->getPinnedNotes($autheur);
         $otherNotes = $autheur->getOtherNotes($user);
         $labelsB = [];

         // on est sur la page de recherche
         $pageSearch = 1;
         // Les labels -----------
         foreach($pinnedNotes as $pn) {
             // prends le label correspondant à l'id de la note
             $labelsB[$pn->noteId]  = labels::getLabelsByNote($pn->noteId);
         }
         foreach($otherNotes as $on) {
             $labelsB[$on->noteId]  = labels::getLabelsByNote($on->noteId);
         }

        $searchLabels = $_POST['labels'] ?? []; // Récupérer les libellés cochés depuis le formulaire
        
        $filteredNotes = $this->filterNotesByLabels($notes, $searchLabels); // Filtrer les notes par libellés

        // Pour récupérer les tags
        if(isset($_GET["param1"])) {
            $labelsB = Tools::url_safe_decode($_GET["param1"]);
        }

        (new View("search_notes"))->show([
            "labels" => $labels,
            "labelsB" => $labelsB,
            "notes" => $notes,
            "filteredNotes" => $filteredNotes,
            "userId" => $autheur->id,
            "isAjax" => $this->is_ajax()
        ]);
    }


    private function filterNotesByLabels($notes, $labels) {
        if (empty($labels)) {
            return $notes; // Si aucun libellé n'est coché, retourner toutes les notes
        }
        $filteredNotes = [];
        foreach ($notes as $note) {
            if (Labels::getCheckLabelsByNoteId($labels,$note->noteId)){// si c'est vrai on prend la note
                $filteredNotes[] = $note;
            } 
            
        }
    
        return array_unique($filteredNotes, SORT_REGULAR); // Éviter les doublons
    }

    private function is_ajax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // modifier une cheklistNote en supprimant ou ajouter un nv item
    public function view_edit_checklist_note() : void {
        $user = $this->get_user_or_redirect();
        $errors = [];
        $user->hasAccessToNoteOrAbort(isset($_GET["param1"]) ? $_GET['param1'] : "");

        if(isset($_GET["param1"]) && $_GET["param1"] != "") { // si il y a un paramètre dans l'url
            $noteId = $_GET["param1"];
            $mesCheckList = CheckListNotes::getChecListkNoteId($noteId);
            //var_dump($mesCheckList);
            if(isset($_POST["newItem"]) ) {
                $newItem = Tools::sanitize($_POST["newItem"]);
                $newCheckListItem = new CheckListNoteItem(null, $noteId, $newItem, false); 
                $errors = $newCheckListItem->validateItem($newItem);
                if(empty($errors)) {
                    $newCheckListItem->saveNote();
                    self::redirect("Notes", "view_edit_checklist_note", $noteId);
                }
            }
            if(isset($_GET["param2"])) {
                $id = (int)$_GET["param2"]; 
                $this->redirect("Notes", "deleteItem", $noteId, $id);
            }
            else if(isset($_POST["title"])) {
                $title = Tools::sanitize($_POST["title"]);
                $currentDateTime = date("Y-m-d H:i:s");
                $updateMaNote = new CheckListNotes($title,
                    $user,
                    $mesCheckList->createdAt,
                    $currentDateTime,
                    $mesCheckList->pinned,
                    $mesCheckList->archived,
                    $mesCheckList->weight,
                    $mesCheckList->noteId // important de passer l'id pour faire un update et pas un insert
                );
                $errors = $updateMaNote->validateTitle($title);
                if(empty($errors)) {
                    $updateMaNote->saveNote();
                    self::redirect("Notes", "index");
                }

            }

        }
        (new View("edit_checklist_note"))->show(["checkList"=> $mesCheckList, "noteId" => $noteId, "errors" =>$errors]);   
    }
    public function deleteItem() {
        if(!empty($_GET["param2"])) {
            $id1 = $_GET["param1"];
            //var_dump($id1);
            $id = $_GET["param2"];
            CheckListNoteItem::deleteChecklistItem($id);
            self::redirect("Notes", "view_edit_checklist_note", $id1);
        }    
    }



    public function pinned_note() : void {
        // $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"])) {
            $noteId = $_GET["param1"];
            $maNote = TextNotes::getContentNoteById($noteId);
            $mesCheckList = CheckListNotes::getChecListkNoteId($noteId);

            if($maNote) {
                $newPinnedStatus = !$maNote->pinned;
                TextNotes::updatePinnedStatus($noteId, $newPinnedStatus);
                (new View("open_text_note"))->show(["maNote" => $maNote,"newPinnedStatus"=> $newPinnedStatus] );

            } 
            if($mesCheckList) {
                $newPinnedStatus = !$mesCheckList->pinned;
                CheckListNotes::updatePinnedStatus($noteId, $newPinnedStatus);
                (new View("open_checkList_note"))->show(["checkList" => $mesCheckList, "newPinnedStatus"=> $newPinnedStatus] );

            }
 
        }
    }

    public function move_note_left() : void {
        if(isset($_GET["param1"])) {
            $noteId = $_GET["param1"];
            Notes::newWeightSub($noteId);
        }
        self::redirect("Notes");
    }
    public function move_note() : void {
        if(isset($_GET["param1"])) { 
            $noteId = $_GET["param1"];
            //$noteIdB = $_GET["param2"];
            echo "Note ID received: " . $noteId;
            //echo "Note ID received: " . $noteIdB;
            //Notes::newWeightSub($noteId); faire autre methode sur notes.php
        }
        //self::redirect("Notes");
    }

    public function move_note_right():void{

        if(isset($_GET["param1"])) { 
            $noteId = $_GET["param1"];
             Notes::newWeightAdd($noteId);
        }
        self::redirect("Notes");
    }
    public function move_right_service() : bool {
        if(isset($_POST["param1"])) { 
            $noteId = $_POST["param1"];
            if($noteId == false) {
                echo json_encode(["success" => false]);
                return false;
            } else {
                Notes::newWeightAdd($noteId);
                echo json_encode(["success" => true]);
                return true;
            }
        }
        // Retourner false si aucun paramètre n'est passé
        return false;
    }
    public function move_left_service() : bool {
        if(isset($_POST["param1"])) { 
            $noteId = $_POST["param1"];
            if($noteId == false) {
                echo json_encode(["success" => false]);
                return false;
            } else {
                Notes::newWeightSub($noteId);
                echo json_encode(["success" => true]);
                return true;
            }
        }
        // Retourner false si aucun paramètre n'est passé
        return false;
    }


    // Pour vérifier que le titre est unique par utilisateur
    public function title_exist_owner() : void {
        $user = $this->get_user_or_redirect();
        $result = "false";
        //echo($user->id);
        if(isset($_POST["title"]) && $_POST["title"] !== "" ) {

            $title = $_POST["title"];
           // echo($title);

            if(Notes::titleExist($title , $user)) {
                $result = "true";
            }
        }
        echo $result;
    }

    // Pour vérifier que le titre est unique par utilisateur
    public function label_exist() : void {
        $result = "false";
        //echo($user->id);
        if(isset($_POST["label"]) && $_POST["label"] !== "" ) {
            $label = $_POST["label"];
            $noteId = $_POST["noteId"];

            if(Labels::labelExists($label , $noteId)) {
                $result = "true";
            }
        }
        echo $result;
    }



    public function delete_item_service() : bool {
        $user = $this->get_user_or_redirect();
        if(isset($_POST["monItem"]) && $_POST["monItem"] !== "") {
            $itemId = $_POST["monItem"];
            // Va servir apres pour savoir l'id
           // $newItem = CheckListNoteItem::get_by_id($itemId);
            if($itemId == false) {
                echo json_encode(["success" => false]);
                exit;
            } else {
                CheckListNoteItem::deleteChecklistItem($itemId);
            }
        }
        echo json_encode(["success" => false]);
        exit;
    }

    // Vérifier si l'item est unique
    public function item_unique_owner() : void {
        $user = $this->get_user_or_redirect();
        $result = "false";
        if(isset($_POST["newItem"]) && $_POST["newItem" !== ""]) {
            $item = $_POST["newItem"];
            if(CheckListNoteItem::validateInputCheck($item, $user)) {
                $result = "true";
            }
        }
        echo $result;
    }

    public function label_service() : bool {
        $user = $this->get_user_or_redirect();
        $result = false;
        // l'id
        if(isset($_POST["newLabel"]) && $_POST["newLabel"] !== ""){
            $labelId = $_POST["newLabel"];
            $label = $_POST["label"];
            $labelNew = new Labels($labelId,$label);
            $labelNew->saveLabel($label, $labelId);

            echo json_encode(["success" => true]);
            exit;
        } else {
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
            exit;
        }

    }

    public function item_service() : bool {
        $user = $this->get_user_or_redirect();
        $result = false;
        if(isset($_POST["monItem"]) && $_POST["monItem"] !== "") {
            $itemId = $_POST["monItem"];
            $content = $_POST["content"];
            $checked = $_POST["checked"];
            $checkListItem = new CheckListNoteItem(null, $itemId, $content, $checked);
            $checkListItem->saveNote();

            echo json_encode(["success" => true]);
            exit;
        } else {
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
            exit;
        }

    }

    // javascript-----
    public function remove_label() : bool {
        $user = $this->get_user_or_redirect();
        if(isset($_POST["deleteLabel"]) && $_POST["deleteLabel"] !== "") {
            $labelLabel = $_POST["deleteLabel"];
            $idNote = $_POST["idNotePost"];
            // Va servir apres pour savoir l'id
           // $newItem = CheckListNoteItem::get_by_id($itemId);
            if($labelLabel == false) {
                echo json_encode(["success" => false]);
                exit;
            } else {
                labels::deleteLabel($idNote, $labelLabel);
            }
        }
        echo json_encode(["success" => false]);
        exit;
    }
}




?>