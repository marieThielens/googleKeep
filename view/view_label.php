<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>labels</title>
    <base href="<?= $web_root ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/styles.css">
    <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
</head>
<body>
<header class="container-fluid d-flex justify-content-between">
    <a href="Notes/index/" id="cancelLink">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
        </svg>
    </a>
</header>


<div class="main-container container-fluid">
    <h1 class="small">Labels : </h1>

    <?php if(empty($LabelsByNote)) : ?>
        <p class="font-italic">This note does not yet have label</p>
    <?php endif; ?>
    <div id="containerLabel">

        <?php foreach($LabelsByNote as $ml) :?>
        <div class="input-group mb-3" id="<?= $ml->label ?>">
            <input type="text" class="form-control" value="<?= $ml->label ?>" name="test" readonly>
            <div class="input-group-append">
                <a class="btn btn-danger deleteItemId" href="Notes/labels/<?= $noteId; ?>/<?= $ml->label ?>" id="<?= $ml->label ?>">-</a>
            </div>
        </div>

    <?php endforeach; ?>  
    </div>


    <form action="notes/labels/<?= $noteId; ?>" method="post" id="formCheclist">
        <div>
            <label for="newItem">New Item</label>
            <div class="input-group mb-2">
            <input type="text" class="form-control texteBlanc item-input" name="newItem" value="<?= isset($_POST['newItem']) ? htmlspecialchars($_POST['newItem']) : "" ?>" class="gris" id="newItem">
            <datalist id="newItem">
            <?php foreach($LabelsByNote as $ml) : ?>
                <option value="<?= $ml->label ?>"></option>
            <?php endforeach; ?>
            </datalist>
            <div class="input-group-prepend">
                    <button class="btn btn-primary" type="submit" form="formCheclist" id="btnAddItem">+</button>
                </div>
            </div>
            <p id="errorLabel"></p>
        </div>
    </form>
    <?= (isset($errors["labelSize"])) ? "<p class='red'>". $errors["labelSize"]."</p>" : ""?>
    <?= (isset($errors["labelSpaces"])) ? "<p class='red'>". $errors["labelSpaces"]."</p>" : ""?>
    <?= (isset($errors["sameLabel"])) ? "<p class='red'>". $errors["sameLabel"]."</p>" : ""?>


</div>

<script>

    const idNote = <?= $noteId ?>;
    let newitem, btnAddItem, errorNewLabel, isValidItem, formNewItem, btnDelete;


    newItem = $("#newItem");
    btnAddItem = $("#btnAddItem");
    errorNewLabel = $("#errorLabel");
    formNewItem = $("#formCheclist");
    btnDelete = $("#deleteItemId");

    errorNewLabel.addClass("text-danger");

    // Désactiver les erreurs php 
    let phpErreur = document.querySelectorAll('.errors');
    phpErreur.forEach((error) => {
        error.style.display = 'none';
    });

    // Ajouter un item dans la vue en javascript
    btnAddItem.click(async function(event) {
        event.preventDefault();
        
        let newlabelValue = $(newItem).val().trim();

        if(isValidItem) {
                                                            //$_POST;
            const data = await $.post("Notes/label_service", {newLabel : idNote, label:newlabelValue }, null, 'json');
            // si ca a fonctionné
            if(data) {
                // ajouter dans la vue
                let div = $("<div>").addClass("input-group mb-3");
                let inputItem = $("<input>").attr({ 
                    type: "text", 
                    class: "form-control texteBlanc item-input",
                    name: "test",  
                    value: newlabelValue,
                    readonly: true
                });
                let divInputGroupPrepend = $("<div>").addClass("input-group-prepend");
                let hiddenInputItemId = $("<input>").attr({ type: "text", id: idNote, value: newlabelValue, name: "deleteItemId", hidden: true });
                let deleteItemLink = $("<a>").addClass("btn btn-danger deleteItemId").attr("href", "Notes/view_edit_checklist_note/" + <?= $noteId ?> + "/" + idNote).text("-");
                divInputGroupPrepend.append(hiddenInputItemId, deleteItemLink);
                div.append( inputItem, divInputGroupPrepend);
                $("#containerLabel").append(div);
            }
        }
    });

    // Vérifier s'il n'y a pas d'espaces dans le texte
    function hasNoSpaces(text) {
    return !/\s/.test(text);
    }

    // Erreur avec le nouvel item
    newItem.on("input", function() {
        // trim() : enlever espace avant et apres
        let newItemValue = $(this).val().trim();
        // La taille du texte
        if(newItemValue.length < 2 ||newItemValue.length > 10) {
            errorNewLabel.html("Label length must be between 2 and 10");
            isValidItem = false;

        }
        else if (!hasNoSpaces(newItemValue)) {
            errorNewLabel.html("Label must not contain spaces");
            isValidItem = false;
        }

        else {
            errorNewLabel.html("");
            labelUnique($(this).val(), idNote, errorNewLabel);
            isValidItem = true;
        }
    });

    // delete -----------
    $(document).on("click", ".deleteItemId", async function (event) {
        event.preventDefault();
        // this represente le bouton sur lequel j'ai cliqué
       const idButton = $(this).attr("id");
       const data = await $.post("Notes/remove_label", {idNotePost: idNote ,  deleteLabel : idButton}, null, "json");
       if(data) {
        let divASupprimer = $(this).attr("id");
        // supprimer la bonne div
        $("#" + divASupprimer).remove();
       }
    });

    // titre unique 
    async function labelUnique(label, noteId, erreurUnique) {
        erreurUnique.addClass("text-danger");
        try {
            const response = await $.post("Notes/label_exist/", {label: label, noteId: noteId}, null, 'json');

            if (response) {
                erreurUnique.html("Label already exists");
                isValidItem = false;
            } 
            else {
                erreurUnique.html("It's ok, the label doesn't already exist.");
                erreurUnique.removeClass("text-danger").addClass("text-success");
                isValidItem = true;
            }
        } catch (error) {
            console.error("Error:", error);
        }   
    }
    
</script>
    
</body>
</html>