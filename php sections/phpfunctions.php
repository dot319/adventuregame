<?php

function cleanInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Functie van Erwin, nog niet geimplementeerd
function checkInput($input) {
    $text_regex = "/^(?=[a-zA-Z]{3})[0-9a-zA-ZàáâäãåąæčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,\.'-]+$/u";
    if (!preg_match($text_regex, $input)){
        return false;
    }
}

?>