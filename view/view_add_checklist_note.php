<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add checklist note</title>
    <base href="<?= $web_root ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/styles.css">
    <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>


</head>
<body>
    <header class="container-fluid d-flex justify-content-between mt-3">
        <a href="Notes/index/">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
            </svg>
        </a>
       <button class="boutonInvisible" form="formCheclist" id="btnEnvoi">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-floppy-fill" viewBox="0 0 16 16">
                <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0H3v5.5A1.5 1.5 0 0 0 4.5 7h7A1.5 1.5 0 0 0 13 5.5V0h.086a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5H14v-5.5A1.5 1.5 0 0 0 12.5 9h-9A1.5 1.5 0 0 0 2 10.5V16h-.5A1.5 1.5 0 0 1 0 14.5z"/>
                <path d="M3 16h10v-5.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5zm9-16H4v5.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5zM9 1h2v4H9z"/>
            </svg>
       </button>

      
    </header>

    <div class="container">
        
    <form action="notes/add_checkList_note" method="post" id="formCheclist" novalidate>
            <div class="d-flex flex-column">
            
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= isset($memoriseInput["title"]) ? htmlspecialchars($memoriseInput["title"]) : ""; ?>"  >
                
            </div>
            <?= (isset($errors["emptyTitle"])) ? "<p class='red'>". $errors["emptyTitle"]."</p>" : ""?>
            <?= (isset($errors["titleLenght"])) ? "<p class='red'>". $errors["titleLenght"]."</p>" : ""?>
            <p id="errorTitle"></p>
            <div class="d-flex flex-column">
                <p>Items</p>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <li>
                        <ul>
                            <input type="text" class="form-control item-input" id="items<?= $i ?>" name="items<?= $i ?>" placeholder="Élément <?= $i ?>" value="<?= isset($memoriseInput["items".$i]) ? htmlspecialchars($memoriseInput["items".$i]) : ""; ?>">
                        </ul>
                        <p class="itemSame"></p>
                    </li>
                    <?= (isset($errors["sameInput"])) ? "<p class='red'>". $errors["sameInput"]."</p>" : ""?>
                <?php endfor; ?>
            </div>
        </form>
    </div>

    <script>
    let title, items, errorTitle, itemSame, btnEnvoi, isValidTitle, formCheclist, isValidInput;

    title = $("#title");
    errorTitle = $("#errorTitle");
    errorTitle.addClass("text-danger");
    itemSame = $(".itemSame");
    itemSame.addClass("text-danger");
    btnEnvoi = $("#btnEnvoi");
    formCheclist = $("#formCheclist");

    // Empecher l'envoi du formulaire si erreurs
    btnEnvoi.click(function() {
        let titreValue = $(this).val().trim();
        event.preventDefault();
        if(isValidTitle && isValidInput) {
            formCheclist.submit();
        }else {
                // Mettre une erreur si le titre est vide et qu'on a pas écrit dans l'input
                if(titreValue === "") {
                    errorTitle.html("Title cannot be empty");
                }
            }
    });
    // Désactiver les erreurs php 
    let phpErreur = document.querySelectorAll('.errors');
    phpErreur.forEach((error) => {
        error.style.display = 'none';
    });
    // Erreur par rapport au titre
    title.on("input", async function() {
        let titreValue = $(this).val().trim();

        if( titreValue.length < <?= Configuration::get('title_min_size') ?>){
                errorTitle.html("Title need to be more than <?= Configuration::get('title_min_size') ?> character.");
                isValidTitle = false;
                $(this).addClass("is-invalid"); // Bootstrap

            } else if (titreValue.length > <?= Configuration::get('title_max_size') ?>) {
                errorTitle.html("Title need to be between <?= Configuration::get('title_min_size') ?> and <?= Configuration::get('title_max_size') ?> max");
                isValidTitle = false;
                $(this).addClass("is-invalid"); // Bootstrap

            }
        else {
            errorTitle.html("");
            $(this).removeClass("is-invalid").addClass("is-valid");
            isValidTitle = true;
        }
    });

    // vérifier si l'item est unique
    function isUnique(item) {
        return !uniqueItem.includes(item);
    }

$(".item-input").on("input", function() {
    let currentItem = $(this).val().trim();
    
    if (currentItem !== "") {
        // Filter pour sélectionner tous les champs d'entrée avec la même valeur que le champ actuel
        let duplicateInputs = $(".item-input").filter(function() {
            return $(this).val().trim() === currentItem;
        });
        
        if (duplicateInputs.length > 1) {
            duplicateInputs.each(function() {
                let errorParagraph = $(this).closest("li").find(".itemSame");
                errorParagraph.html("Items must be unique");
                $(this).addClass("is-invalid"); // Bootstrap
            });
            isValidInput = false;
        } else {
            // Si la valeur est unique, enlever les classes d'erreur
            $(this).removeClass("is-invalid").addClass("is-valid");
            let errorParagraph = $(this).closest("li").find(".itemSame");
            errorParagraph.html("");
            isValidInput = true;
        }
    } else {
        // Si le champ est vide, enlever les classes d'erreur
        $(this).removeClass("is-invalid is-valid");
        let errorParagraph = $(this).closest("li").find(".itemSame");
        errorParagraph.html("Item must be between <?= Configuration::get('item_min_length') ?> and <?= Configuration::get('item_max_length') ?>");
        isValidInput = false;
    }

    // Réinitialiser validInput à true si tous les champs sont valides
    if ($(".item-input.is-invalid").length === 0) {
        validInput = true;
    }
});


    </script>
</body>
</html>