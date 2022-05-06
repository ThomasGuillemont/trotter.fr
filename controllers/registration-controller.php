<?php

//! require once
require_once(dirname(__FILE__) . '/../utils/init.php');
require_once(dirname(__FILE__) . '/../config/regex.php');
require_once(dirname(__FILE__) . '/../models/User.php');
require_once(dirname(__FILE__) . '/../helpers/sessionFlash.php');

$checked = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = [];

    //! ip
    $ip = trim(filter_input(INPUT_POST, 'ip', FILTER_SANITIZE_SPECIAL_CHARS));

    //! registered_at
    $date = new DateTime('', new DateTimeZone('Europe/Paris'));
    $registered_at = $date->format('Y-m-d H:i');

    //! role
    $role = 3;

    //! avatar
    $avatar = intval(filter_input(INPUT_POST, 'flexRadio', FILTER_SANITIZE_NUMBER_INT));

    $minAvatar = 1;
    $maxAvatar = 8;
    if (empty($avatar)) {
        $error['avatar'] = 'Vous devez choisir un avatar';
    } else {
        $avatarValid = filter_var($avatar, FILTER_VALIDATE_INT, array("options" => array("min_range" => $minAvatar, "max_range" => $maxAvatar)));
        if ($avatarValid === false) {
            $error['avatar'] = 'L\'avatar n\'est pas correct';
        } else {
            $checked = $avatar;
        }
    }

    //! pseudo
    $pseudo = trim(filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_SPECIAL_CHARS));
    if (empty($pseudo)) {
        $error['pseudo'] = 'Vous devez saisir un pseudo';
    } else {
        $pseudoValid = filter_var($pseudo, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^" . PSEUDO . "$/")));
        if ($pseudoValid === false) {
            $error['pseudo'] = 'Le pseudo doit faire entre 2 et 30 caractères';
        } else {
            if (User::isPseudoExist($pseudo)) {
                $error['pseudo'] = "Ce pseudo est déjà utilisé";
            }
        }
    }

    //! email
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    if (empty($email)) {
        $error['email'] = 'Vous devez saisir une adresse email';
    } else {
        $emailValid = filter_var($email, FILTER_VALIDATE_EMAIL);
        if ($emailValid === false) {
            $error['email'] = 'Le format de l\'adresse email est incorrect';
        } else {
            if (User::isEmailExist($email)) {
                $error['email'] = "Cette adresse email est déjà utilisée";
            }
        }
    }

    //! password
    $password = $_POST['password'];
    $passwordConfirm = $_POST['passwordConfirm'];

    if (empty($password)) {
        $error['password'] = 'Veuillez saisir un mot de passe';
    }
    if (empty($passwordConfirm)) {
        $error['passwordConfirm'] = 'Veuillez confirmez votre mot de passe';
    } else {
        if ($password != $passwordConfirm) {
            $error['password'] = 'Le mots de passe et la confirmation ne sont pas identiques';
            $error['passwordConfirm'] = 'Le mots de passe et la confirmation ne sont pas identiques';
        } else {
            $passwordValid = filter_var($password, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^" . PASSWORD . "$/")));
            if ($passwordValid === false) {
                $error['password'] = 'Le mot de passe doit faire entre 2 et 30 caractères';
                $error['passwordConfirm'] = 'La confirmation doit faire entre 2 et 30 caractères';
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            }
        }
    }

    //! checkbox
    $checkbox = intval(filter_input(INPUT_POST, 'checkbox', FILTER_SANITIZE_NUMBER_INT));
    if ($checkbox) {
        $checkboxCheked = 'checked';
    }
    if ($checkbox !== 1) {
        $error['checkbox'] = 'Vous devez lire et accepter les <a href="/conditions.html">conditions</a>';
    }

    //! $user->add()
    if (empty($error)) {
        $user = new User($ip, $registered_at, $email, $pseudo, $passwordHash, $avatar, $role);
        $user = $user->add();

        //! message success or error
        if (!$user) {
            $message = 'Une erreur est survenue';
        } else {
            SessionFlash::set('Un email de confirmation vous à était adressé, merci cliquer sur le lien envoyé  !');
            sleep(1.5);
            header('Location: /connexion');
            die;
        }
    }
}

//! include
include(dirname(__FILE__) . '/../views/templates/header.php');
include(dirname(__FILE__) . '/../views/registration.php');
include(dirname(__FILE__) . '/../views/templates/footer.php');
