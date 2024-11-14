<?php
// Récupération de la valeur minimale de titre en PHP
$titleMinSize = Configuration::get('title_min_size');
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>open_text_note</title>
    <base href="<?= $web_root ?>">
    <!-- Pour bootsrap -->
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
            <!-- Sauver la note -->
            <div>
                <!-- boutonInvisible  -->
                <button class="boutonInvisible" form="saveTextNote">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-floppy" viewBox="0 0 16 16">
                        <path d="M11 2H9v3h2z"/>
                        <path d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z"/>
                    </svg>
                </button>
            </div>

        </div>
    </header> 

    <div class="main-container container-fluid">
    <p class="italique">Created <?=  $maNote->calculDateCreated()?>. <?=  $maNote->calculDateEdited()?></p>
        <form action="Notes/edit_text_note/<?= $maNote->noteId; ?>" method="post" id="saveTextNote">
        <!-- input caché, besoin pour garder l'id -->
        <input type="hidden" id="idNote" name="idNote" value="<?= $maNote->noteId; ?>">
        <!-- Titre -->
            Title
            <div class="input-group mb-2"> 
                <!-- ControllerNote, méthode open_text_notes  -->
                <input type="text" class="form-control texteBlanc" name="title" id="title" value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : htmlspecialchars($maNote->title) ?>">
            </div>
            <?= (isset($errors["titleEmpty"])) ? "<p class='red'>". $errors["titleEmpty"]."</p>" : ""?>
            <?= (isset($errors["titleSize"])) ? "<p class='red'>". $errors["titleSize"]."</p>" : ""?>
            <?= (isset($errors["titleBigSize"])) ? "<p class='red'>". $errors["titleBigSize"]."</p>" : ""?>
            <!-- pour jquery -->
            <p id="errorTitle"></p>
            <!-- Text -->
            Text
            <div class="input-group mb-2">
                <!-- Je récupère le contenu d'une note -->
                <textarea class="form-control texteBlanc"  name="texte" id="texte"><?= !empty($maNote->content) ? $maNote->content : '' ?></textarea>
            </div>
            <p id="errorDescription"></p>
        </form>   
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

    <script>
        let title, description, btnEnvoi, errorTitle, errorDescription, isValidTitle, isValidDescription, formNote, reponse, estModifie, btnCancelLink;
        // Récupérer mes elements
        title = $("#title");
        description = $("#texte");
        btnEnvoi = $("#btnEnvoi");
        errorTitle = $("#errorTitle");
        errorDescription = $("#errorDescription");
        formNote = $("#formNote");
        btnCancelLink = $("#cancelLink");
        // style 
        errorTitle.addClass("text-danger");
        errorDescription.addClass("text-danger");

        // desactiver le lien 
        confirm_back();
        $("#cancelLink").attr("href","javascript:confirm_back()");


        // Désactiver les erreurs php 
        let phpErreur = document.querySelectorAll('.errors');
        phpErreur.forEach((error) => {
            error.style.display = 'none';
        });

        // empecher l'envoi si erreur
        btnEnvoi.click(function(event) {
            event.preventDefault(); 
            if(isValidTitle && isValidDescription) {
                formNote.submit();
            }
        });

        // Vérification quand j'écris
        title.on("input", async function() {
            let titreValue = $(this).val().trim();
            // la taille du titre
            if( titreValue.length < <?= Configuration::get('title_min_size') ?> ){
                errorTitle.html("Title need to be more than <?= Configuration::get('title_min_size') ?> character.");
                isValidTitle = false;
            } else if (titreValue.length > <?= Configuration::get('title_max_size') ?>) {
                errorTitle.html("Title need to be between <?= Configuration::get('title_min_size') ?> and <?= Configuration::get('title_max_size') ?> max");
                isValidTitle = false;
            } else {
                errorTitle.html("");
                isValidTitle = true;
                estModifie = true;
                titleUniqueByOwner($(this).val(), errorTitle);
               
            }
        });

        description.on("input", async function() {
            let decriptionValue = $(this).val().trim();
             if(decriptionValue != ""){
                if(decriptionValue.length < <?= Configuration::get('content_min_size') ?>) {
                    errorDescription.html("Description need to be minimum <?= Configuration::get('content_min_size') ?> character.");
                    isValidDescription = false;
                } else if(decriptionValue.length > 60) {
                    errorDescription.html("Description need to be between <?= Configuration::get('content_min_size') ?> and <?= Configuration::get('content_max_size') ?> character.");
                    isValidDescription = false;
                } else {
                    errorDescription.html("");
                    isValidDescription = true;
                    estModifie = true;
                }
            }
        });

        // Titre unique 
        async function titleUniqueByOwner(string, erreurUnique) {
            //console.log(string);
            erreurUnique.addClass("text-danger");
            try {
                // const response = await $.post("Notes/title_exist_owner/",{title: string}, null, 'json');
                const response = await $.post("Notes/title_exist_owner/", {title: string}, null, 'json');

                console.log(response);
                if (response) {
                    erreurUnique.html("Title already exists");
                    isValidTitle = false;
                } 
                else {
                    erreurUnique.html("It's ok, the title doesn't already exist.");
                    erreurUnique.removeClass("text-danger").addClass("text-success");
                    isValidTitle = true;
                }
            } catch (error) {
                console.error("Error:", error);
            }
        }
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