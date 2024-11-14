<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/styles.css">

</head>
<body>

<header class="container d-flex justify-content-between">
    <a href="user/profile">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
        </svg>
    </a>
    <p>Edit Profile</p>
</header>

<div class="main-container container-fluid">
    
    <!-- Formulaire pour l'édition du profil -->
    <form action="user/editProfile" method="post">
        <!-- Email -->
        <div class="input-group mb-2"> <!--mb-2 car c'est 2 colonnes-->
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="#D9E2E6" class="bi bi-envelope" viewBox="0 0 16 16">
                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
                    </svg>
                </div>
            </div>
            <input type="text" class="form-control texteBlanc" placeholder="Email" value="<?= $user->mail ?>" name="mail">
        </div>
        <?= (isset($errors["emptyMail"])) ? "<p style='color: red'>". $errors["emptyMail"]."</p>" : ""?>
        <?= (isset($errors["mailInvalidFormat"])) ? "<p style='color: red'>". $errors["mailInvalidFormat"]."</p>" : ""?>
        <?= (isset($errors["userExist"])) ? "<p style='color: red'>". $errors["userExist"]."</p>" : ""?>

        <!-- Full name -->
        <div class="input-group mb-2">
            <div class="input-group-prepend">
            <!-- icone avatar-->
                <div class="input-group-text">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="#D9E2E6" class="bi bi-person-fill" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z"/>
                    </svg>
                </div>
            </div>
            <input type="text" class="form-control texteBlanc"  placeholder="Full name" value="<?= $user->fullName ?>" name="fullName">
        </div>
        <?= (isset($errors["pseudoRequired"])) ? "<p style='color: red'>". $errors["pseudoRequired"]."</p>" : ""?>
        <?= (isset($errors["pseudoLength"])) ? "<p style='color: red'>". $errors["pseudoLength"]."</p>" : ""?>



        <!-- Bouton de soumission -->
        <button type="submit" class="btn btn-primary col-12 mt-2">Save Changes</button>
    </form>
    <!--   ----------- Afficher le message de succes ---------- -->

    <?php if (isset($success) != 0): ?>
    <p><span class='success' style='color: green'><?= $success ?></span></p>
    <?php endif; ?>
</div>

<!-- Inclure vos scripts JavaScript ici si nécessaire -->

</body>
</html>
