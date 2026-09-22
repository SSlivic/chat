<?php


$userIP = $_SERVER['REMOTE_ADDR'];

$counterFile = "counter.txt";

// Ako fajl ne postoji, napravi ga
if (!file_exists($counterFile)) {
    file_put_contents($counterFile, 0);
}

// Ako je tvoja poseta — samo prikaži broj, ne povećavaj
if ($userIP === $myIP) {
    echo "Posete: " . file_get_contents($counterFile);
    exit;
}

// Učitaj trenutni broj
$visits = (int)file_get_contents($counterFile);

// Povećaj broj poseta
$visits++;

// Sačuvaj novi broj
file_put_contents($counterFile, $visits);

// Prikaži broj poseta
echo "Posete: " . $visits;
?>
