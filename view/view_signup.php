<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <base href="<?= $web_root ?>">
    <!-- Pour bootsrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="d-flex align-items-center signUp">
        <div class="container border rounded my-auto">
            <h2 class="text-center mt-4">Sign up</h2>
            <hr>

            <form action="main/signup" method="post">
                <!-- Email -->
                <div class="input-group mb-2"> <!--mb-2 car c'est 2 colonnes-->
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="#D9E2E6" class="bi bi-envelope" viewBox="0 0 16 16">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
                            </svg>
                        </div>
                    </div>
                    <input type="text" class="form-control texteBlanc" id="userMail" placeholder="Email" value="<?= isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : '' ?>" name="mail">
                </div>
                <!-- Afficher les erreurs en rouge -->
                <div>
                    <?= (isset($errors["userExist"])) ? "<p class='red'>".  $errors["userExist"] : ""?>
                    <?= (isset($errors["mailInvalidFormat"])) ? "<p class='red'>".  $errors["mailInvalidFormat"] : ""?>
                    <?= (isset($errors["emptyMail"])) ? "<p class='red'>".  $errors["emptyMail"] : ""?>
                </div>
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
                    <input type="text" class="form-control texteBlanc"  placeholder="Full name" value="<?= $fullName ?>" name="fullName">
                </div>
                <div>
                    <?= (isset($errors["pseudoRequired"])) ? "<p style='color: red'>".  $errors["pseudoRequired"] : ""?>
                    <?= (isset($errors["pseudoLength"])) ? "<p style='color: red'>".  $errors["pseudoLength"] : ""?>
                </div>
                <!-- Password -->
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                    <!-- icone avatar-->
                    <div class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="#D9E2E6" class="bi bi-key" viewBox="0 0 16 16">
                            <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5"/>
                            <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                        </svg>
                    </div>
                    </div>
                    <input type="password" class="form-control texteBlanc"  placeholder="Password" value="" name="password">
                </div>
                <div>
                    <?= (isset($errors["passwordLength"])) ? "<p style='color: red'>".  $errors["passwordLength"] : ""?>
                    <?= (isset($errors["passwordValid"])) ? "<p style='color: red'>".  $errors["passwordValid"] : ""?>
                </div>
                <!-- Confirm password -->
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                    <!-- icone avatar-->
                    <div class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="#D9E2E6" class="bi bi-key" viewBox="0 0 16 16">
                            <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5"/>
                            <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                        </svg>
                    </div>
                    </div>
                    <input type="password" class="form-control texteBlanc"  placeholder="Confirm your password" value="" name="passwordConfirm">
                </div>
                <div>
                <?= (isset($errors["passwordSame"])) ? "<p style='color: red'>".  $errors["passwordSame"] : ""?>

                </div>
                <button type="submit" value="Sign Up" class="btn btn-primary col-12 mt-2">Sign Up</button>
                <a href="main/index" role="button" class="btn btn-outline-danger col-12 mt-2 mb-4">Cancel</a>
            </form>
        </div>
    </div>


</body>
</html>