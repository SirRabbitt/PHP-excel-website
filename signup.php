<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
    <?php
    if (isset($_SESSION["userid"])) {
        // Menu for logged-in users
    ?>
        <header>
            <nav>
                <div>
                <ul class="menu-main">
                        <li><a href="index.php">zmiana hasła użytkownika</a></li>
                        <li><a href="raport.php">raport sprzedaży wg grup produktów w wybranym zakresie dat</a></li>
                        <li><a href="sellyears.php">zestawienie sprzedaży wg grup produktów, rok do roku</a></li>
                    </ul>
                </div>
                <ul class="menu-member">
                    <li><a href="#"> <?php echo $_SESSION["useruid"]; ?></a></li>
                    <li><a href="includes/logout.inc.php" class="header-login-a">Wyloguj</a></li>
                </ul>
            </nav>
        </header>
    <?php
    } else {
        // Menu dla niezalogowanych użytkowników
    ?>
        <header>
            <nav>
                <ul class="menu-member">

                    <li><a href="signup.php">zarejestruj się</a></li>
                    <li><a href="index.php" class="header-login-a">zaloguj się</a></li>
                </ul>
            </nav>
        </header>
        <section class="index-login">
            <div class="wrapper">
                <div class="index-login-login">
                    <h4>Zarejestruj się</h4>
                    <form action="includes/signup.inc.php" method="post">
                        <input type="text" name="uid" placeholder="Login" required>
                        <input type="password" name="pwd" placeholder="Hasło" minlength="5" pattern="(?=.*\d)(?=.*[A-Z]).{5,}" title="Hasło musi zawierać co najmniej 5 znaków, w tym jedną dużą literę i jedną cyfrę" required>
                        <input type="password" name="pwdrepeat" placeholder="Powtórz Hasło" minlength="5" required>
                        <input type="text" name="email" placeholder="E-mail" required>
                        <br>
                        <button type="submit" name="submit">Sign Up</button>
                    </form>

                </div>
            </div>
        </section>
    <?php
    }
    ?>
</body>

</html>