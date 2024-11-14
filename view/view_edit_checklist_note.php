<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit checklist note</title>
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
        <button form="formUpdate" class="boutonInvisible" id="btnSave">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy-fill" viewBox="0 0 16 16">
                <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0H3v5.5A1.5 1.5 0 0 0 4.5 7h7A1.5 1.5 0 0 0 13 5.5V0h.086a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5H14v-5.5A1.5 1.5 0 0 0 12.5 9h-9A1.5 1.5 0 0 0 2 10.5V16h-.5A1.5 1.5 0 0 1 0 14.5z"/>
                <path d="M3 16h10v-5.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5zm9-16H4v5.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5zM9 1h2v4H9z"/>
            </svg>
        </button>

    </header>
 
    <div class="main-container container-fluid">
    <p class="italique">created <?= $checkList->calculDateCreated()?>. Edited  <?=  $checkList->calculDateEdited()?></p>

        <form action="notes/view_edit_checklist_note/<?= $noteId ?>" method="post" id="formUpdate">
            <label for="title">Title</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control gris" id="title" name="title" value="<?= $checkList->title ?>">
            </div>
        </form>
            <?= (isset($errors["titleEmpty"])) ? "<p class='red'>". $errors["titleEmpty"]."</p>" : ""?>
            <?= (isset($errors["titleSize"])) ? "<p class='red'>". $errors["titleSize"]."</p>" : ""?>
            <?= (isset($errors["titleBigSize"])) ? "<p class='red'>". $errors["titleBigSize"]."</p>" : ""?>
            <p id="errorTitle"></p>
        <div>
        <!-- Ils ont tous le même id global donc je prensd le 1ere -->
    <form action="notes/view_edit_checklist_note/<?= $noteId ?>" method="post" id="formCheclist">
        <div id="containerInputs">
                <!-- Pour chaque case à cocher -->
        <?php foreach($checkList->checkListItems as $item): ?>
            
                <div class="input-group mb-3">
                    <div class="input-group-text">
                        <input type="checkbox" name="input" disabled>
                    </div>
                    <input type="text" class="form-control texteBlanc item-input" name="sujet[<?= $item->id ?>]" value="<?= $item->content ?>">
                    <div class="input-group-prepend">
                        <input type="text" id="<?= $item->id ?>" value="<?= $item->id ?>" name="deleteItemId"  hidden>
                        <a class="btn btn-danger deleteItemId" href="Notes/view_edit_checklist_note/<?= $noteId ?>/<?= $item->id ?>">-</a>
                    </div>
                </div>
                <p class="itemSame errorItem"></p>
        <?php endforeach; ?>
        </div>
                <div>
                    <label for="newItem">New Item</label>
                    <div class="input-group mb-2">

                    <input type="text" class="form-control texteBlanc item-input" name="newItem" value="<?= isset($_POST['newItem']) ? htmlspecialchars($_POST['newItem']) : "" ?>" class="gris" id="newItem">
                    <div class="input-group-prepend">
                            <button class="btn btn-success" type="submit" form="formCheclist" id="btnAddItem">+</button>
                        </div>
                    </div>
                    <p id="errorItem itemSame"></p>
                </div>
                </form>
                <?= (isset($errors["itemEmpty"])) ? "<p class='red'>". $errors["itemEmpty"]."</p>" : ""?>
                <?= (isset($errors["itemBigSize"])) ? "<p class='red'>". $errors["itemBigSize"]."</p>" : ""?>
            </div> 

            
   <!-- ...Modale ... -->
   <div class="modal fade bg-dark" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Unsaved changes ! </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <p>Are you sure you want to leave this from ? </p>
               <p>Changes you made will be note saved</p>
            </div>
            <div class="modal-footer">
                <button id="btnCancelBack" type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button id="btnDeleteModal"  type="button" class="btn btn-danger">Leave Page </button>
            </div>
            </div>
        </div>
    </div>  
    </div>

    <script>
    let title, items, errorTitle, btnSave, isValidTitle, formEnvoi, isValidNewItem, newItem, isDuplicateItem, btnDelete, itemSame, isValidInput, estModifie, contenu;
    const idNote = <?= $noteId ?>;

    title = $("#title");
    errorTitle = $("#errorTitle");
    errorTitle.addClass("text-danger");
    btnSave = $("#btnSave");
    formEnvoi = $("#formCheclist");
    newItem = $("#newItem");
   // errorItem = $(".errorItem");
    itemSame = $(".itemSame");
    contenu = $("#contenu");
    // style
    errorTitle.addClass("text-danger");
    
    // desactiver le lien 
    confirm_back();
    $("#cancelLink").attr("href","javascript:confirm_back()");

    // Désactiver les erreurs php 
    let phpErreur = document.querySelectorAll('.errors');
    phpErreur.forEach((error) => {
        error.style.display = 'none';
    });

    // Empecher l'envoi si erreur
    btnSave.click(function(event) {
        event.preventDefault();
        if(isValidTitle && isValidInput) {
            formEnvoi.submit();
        }
    });



    // Erreur par rapport au titre
    title.on("input", async function() {
        let titreValue = $(this).val().trim();
        if( titreValue.length < <?= Configuration::get('title_min_size') ?>){
                errorTitle.html("Title need to be more than <?= Configuration::get('title_min_size') ?> character.");
                isValidTitle = false;
                $(this).addClass("is-invalid"); // Bootstrap
                estModifie = true;
        } else if (titreValue.length > <?= Configuration::get('title_max_size') ?>) {
            errorTitle.html("Title need to be between <?= Configuration::get('title_min_size') ?> and <?= Configuration::get('title_max_size') ?> max");
            isValidTitle = false;
            $(this).addClass("is-invalid"); // Bootstrap
            estModifie = true;
        }else {
            errorTitle.html("");
            $(this).removeClass("is-invalid").addClass("is-valid");
            isValidTitle = true;
            estModifie = true;
           // titleUniqueByOwner($(this).val(), errorTitle);  
        }
    });
   

    // titre unique 
    async function titleUniqueByOwner(string, erreurUnique) {
            //console.log(string);
            erreurUnique.addClass("text-danger");
            try {
                // const response = await $.post("Notes/title_exist_owner/",{title: string}, null, 'json');
                const response = await $.post("Notes/title_exist_owner/", {title: string}, null, 'json');

               // console.log(response);
                if (response) {
                    erreurUnique.html("Title already exists");
                    isValidTitle = false;
                    estModifie = true;
                } 
                else {
                    erreurUnique.html("It's ok, the title doesn't already exist.");
                    erreurUnique.removeClass("text-danger").addClass("text-success");
                    isValidTitle = true;
                    estModifie = true;
                }
            } catch (error) {
                console.error("Error:", error);
            }   
        }
        
        // delete ----------- 
        $(document).on('click', '.deleteItemId', async function (event) {
            event.preventDefault();
            // récupérer l'id de l'input. closest pour remonter au parent commen et ensuite trouver l'input à l'intérieur 
            let idInput = $(this).closest(".input-group").find("input[name='deleteItemId']").val();
            const data = await $.post("Notes/delete_item_service", { monItem : idInput }, null, "json");
            if(data) {
                let divASupprimer = $(this).closest('.mb-3').remove();
                estModifie = true;
            }
        });



        // Ajouter item ------------
        $(document).on('click', '#btnAddItem', async function (event) {
            event.preventDefault();

            let valueInput = $("#newItem").val().trim();

           //const data = await $.post("Notes/item_service", {monItem : idNote, content: valueInput, checked:0 }, null, 'json');
           // if(data) {
                
                estModifie = true;
                isValidInput = true;
                // ajouter dans la vue
                let div = $("<div>").addClass("input-group mb-3");
                let divEnfant = $("<div>").addClass("input-group-text");;
                let checkbox = $("<input>").attr({ type: "checkbox", name: "input", disabled: true });
                divEnfant.append(checkbox);
                let inputItem = $("<input>").attr({ 
                    type: "text", 
                    class: "form-control texteBlanc item-input", 
                    name: "sujet[<?= $item->id ?>]", 
                    value: valueInput 
                });
                inputItem.addClass("is-valid");
                let divInputGroupPrepend = $("<div>").addClass("input-group-prepend");
                let hiddenInputItemId = $("<input>").attr({ type: "text", id: valueInput, value: valueInput, name: "deleteItemId", hidden: true });
                let deleteItemLink = $("<a>").addClass("btn btn-danger deleteItemId").attr("href", "Notes/view_edit_checklist_note/" + <?= $noteId ?> + "/" + valueInput).text("-");
                divInputGroupPrepend.append(hiddenInputItemId, deleteItemLink);
                div.append(divEnfant, inputItem, divInputGroupPrepend);
                $("#containerInputs").append(div);
                $("#newItem").val("");
                // } else {
                //     console.log("erreur");
                // }
        });


        // ecouterInput
        $(".item-input").on('input', async function() {
        let contentInput = $(this).val().trim();
        let errorItem = $(this).closest('.input-group').next('.errorItem');
        errorItem.addClass("text-danger");

        // Vérifier si le contenu n'est pas vide
        if(contentInput !== "") {
            // Vérifier la longueur du contenu
            if(contentInput.length < 2) {
                errorItem.html("The minimum size is 1 character.");
                $(this).addClass("is-invalid");
                isValidInput = false;
                estModifie = true;
            } else if (contentInput.length > 60) {
                errorItem.html("The maximum size is 60 characters.");
                $(this).addClass("is-invalid");
                isValidInput = false;
                estModifie = true;
            } else {
                // Filtrer les doublons
                let duplicateInputs = $(".item-input").filter(function() {
                    return $(this).val().trim() === contentInput;
                });

                // Vérifier s'il y a des doublons
                if(duplicateInputs.length > 1) {
                    duplicateInputs.each(function() {
                        errorItem.html("Items must be unique");
                        $(this).addClass("is-invalid");
                    });
                    isValidInput = false;
                    estModifie = true;
                } else {
                    // Pas de doublons, valider le champ
                    errorItem.html("");
                    $(".item-input").removeClass("is-invalid").addClass("is-valid");
                    isValidInput = true;
                    estModifie = true;
                }
            }
        } else {
            // Si le champ est vide, réinitialiser l'affichage de l'erreur et la validation
            errorItem.html("");
            $(this).removeClass("is-invalid").removeClass("is-valid");
            isValidInput = false;
            estModifie = true;
        }
    });



    function confirm_back() {
    if(!estModifie) {
            $("#cancelLink").on("click", function() {
            window.location.href = "Notes/index/"; 
            });
        }
    if(estModifie) {
        $('#exampleModal').modal('show');
        $("#btnCancelBack").on("click", function() {
            $('#exampleModal').modal('hide');
        }); 
        $("#btnDeleteModal").on("click", function() {
            window.location.href = "Notes/index/";           // <a href="Notes/index/"></a>
        });
        }
    }
    </script>
</body>
</html>