<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Note</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/styles.css">
    <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
</head>
<body>

<header class="container d-flex justify-content-between">
    <a href="Notes/index/">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
        </svg>
    </a>
    <button class="boutonInvisible" form="formNote" id="btnEnvoi">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy-fill" viewBox="0 0 16 16">
                <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0H3v5.5A1.5 1.5 0 0 0 4.5 7h7A1.5 1.5 0 0 0 13 5.5V0h.086a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5H14v-5.5A1.5 1.5 0 0 0 12.5 9h-9A1.5 1.5 0 0 0 2 10.5V16h-.5A1.5 1.5 0 0 1 0 14.5z"/>
                <path d="M3 16h10v-5.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5zm9-16H4v5.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5zM9 1h2v4H9z"/>
            </svg>
    </button>  
</header>

<div class="main-container container-fluid">
    
    
    <form action="Notes/saveNote" method="post" id="formNote">
        <label for="title">Title</label>
        <div class="input-group mb-2"> 
            <input type="text" class="form-control texteBlanc" name="title" id="title" value=<?= isset($_POST["title"]) ? htmlspecialchars($_POST["title"]) : "" ?>>
        </div>
        <?= (isset($errors["titleEmpty"])) ? "<p class='red'>". $errors["titleEmpty"]."</p>" : ""?>
        <?= (isset($errors["titleSize"])) ? "<p class='red'>". $errors["titleSize"]."</p>" : ""?>
        <?= (isset($errors["titleBigSize"])) ? "<p class='red'>". $errors["titleBigSize"]."</p>" : ""?>
        <!-- pour jquery -->
        <p id="errorTitle"></p>
        <!-- Text -->
        <label for="text">Text</label>
        <div class="input-group mb-2">  
            <textarea class="form-control texteBlanc" name="text" id="text" rows="20"><?= isset($_POST["text"]) ? htmlspecialchars($_POST["text"]) : "" ?></textarea>
        </div>
        <p id="errorDescription"></p>
    </form>
    
</div>

<script>
        let title, description, btnEnvoi, errorTitle, errorDescription, isValidTitle, isValidDescription, formNote, reponse;
        // Récupérer mes elements
        title = $("#title");
        description = $("#text");
        btnEnvoi = $("#btnEnvoi");
        errorTitle = $("#errorTitle");
        errorDescription = $("#errorDescription");
        formNote = $("#formNote");

        // style 
        errorTitle.addClass("text-danger");
        errorDescription.addClass("text-danger");


        // Désactiver les erreurs php 
        let phpErreur = document.querySelectorAll('.errors');
        phpErreur.forEach((error) => {
            error.style.display = 'none';
        });

        // empecher l'envoi si erreur
        btnEnvoi.click(function(event) {
            let titreValue = $(this).val().trim();
            event.preventDefault(); 
            if(isValidTitle && isValidDescription) {
                formNote.submit();
            } else {
                // Mettre une erreur si le titre est vide et qu'on a pas écrit dans l'input
                if(titreValue === "") {
                    errorTitle.html("Title cannot be empty");
                }
            }
        });

        // Vérification quand j'écris
        title.on("input", async function() {
            let titreValue = $(this).val().trim();
            // la taille du titre
            if( titreValue.length < <?= Configuration::get('title_min_size') ?>){
                errorTitle.html("Title need to be more than <?= Configuration::get('title_min_size') ?> character.").addClass('text-danger').removeClass('text-success');
                isValidTitle = false;
            } else if (titreValue.length > <?= Configuration::get('title_max_size') ?>) {
                errorTitle.html("Title need to be between <?= Configuration::get('title_min_size') ?> and <?= Configuration::get('title_max_size') ?> max").addClass('text-danger').removeClass('text-success');
                isValidTitle = false;
            } else {
                errorTitle.html("").removeClass('text-danger').addClass('text-success');
                titleUniqueByOwner($(this).val(), errorTitle);
            }
        });

        description.on("input", async function() {
            let decriptionValue = $(this).val().trim();

             if(decriptionValue != ""){
                if(decriptionValue.length < <?= Configuration::get('content_min_size') ?> ) {
                    errorDescription.html("Description need to be minimum <?= Configuration::get('content_min_size') ?> character.");
                    isValidDescription = false;
                } else if(decriptionValue.length > <?= Configuration::get('content_max_size') ?>) {
                    errorDescription.html("Description need to be between <?= Configuration::get('content_min_size') ?> and <?= Configuration::get('content_max_size') ?> character.");
                    isValidDescription = false;
                } else {
                    errorDescription.html("").removeClass('text-danger').addClass('text-success');
                    isValidDescription = true;
                }
            } else {
                    // Si la description est vide, aucun message d'erreur n'est affiché et isValidDescription est mis à true
                    errorDescription.html("").removeClass('text-danger').addClass('text-success');
                    isValidDescription = true;
            }
        });

        // Titre unique 
        async function titleUniqueByOwner(string, erreurUnique) {
            //console.log(string);
            erreurUnique.addClass("text-danger");
            try {
                // const response = await $.post("Notes/title_exist_owner/",{title: string}, null, 'json');
                const response = await $.post("Notes/title_exist_owner/", {title: string}, null, 'json');

                if (response) {
                    erreurUnique.html("Title already exists").addClass('text-danger').removeClass('text-success');
                    isValidTitle = false;
                } else {
                    erreurUnique.html("It's ok, the title doesn't already exist.").removeClass('text-danger').addClass('text-success');
                    isValidTitle = true;
                }
            } catch (error) {
                console.error("Error:", error);
            }
        }


    </script>

</body>
</html>
