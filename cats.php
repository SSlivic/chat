<?php
session_start();
date_default_timezone_set('Europe/Ljubljana');

/* DELETE – radi i za multiline poruke */
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $sadrzaj = file_get_contents("komentari.php");

    // REGEX koji briše ceo <div class='post' ... </div>
    $pattern = "/<div class='post' data-id='$id'>.*?<\/div>\s*/s";

    $sadrzaj = preg_replace($pattern, "", $sadrzaj);

    file_put_contents("komentari.php", $sadrzaj);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
/* EDIT – prikaz forme */
if (isset($_POST['edit_id'])) {
    $_SESSION['edit_id'] = $_POST['edit_id'];
}
/* EDIT – čuvanje izmene */
if (isset($_POST['save_edit'])) {
    $id   = $_SESSION['edit_id'];
    $nova = nl2br(htmlspecialchars($_POST['poruka']));

    $sadrzaj = file_get_contents("komentari.php");

    // zameni samo <p>...</p> za taj ID
    $pattern = "/(<div class='post' data-id='$id'>.*?<p>)(.*?)(<\/p>)/s";
    $replacement = "$1$nova$3";

    $sadrzaj = preg_replace($pattern, $replacement, $sadrzaj);

    file_put_contents("komentari.php", $sadrzaj);

    unset($_SESSION['edit_id']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


/* QUOTE */
if (isset($_POST['quote_id'])) {
    $id     = $_POST['quote_id'];
    $linije = file("komentari.php");

    foreach ($linije as $linija) {
        if (strpos($linija, "data-id='$id'") !== false) {
            if (preg_match('/<p>(.*?)<\/p>/', $linija, $m)) {
                $_SESSION['quote_text'] = "> " . $m[1] . "\n";
            }
        }
    }
}

/* LOGIN PROVERE */
/*if (!isset($_SESSION['log']) || !isset($_SESSION['ime'])) {
    header("Location: register.php");
    exit();
}

if ($_SESSION['log'] !== true) {
    header("Location: index.html");
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.html");
    exit();
}
*/
/* SLANJE NOVE PORUKE */
if (isset($_POST['submit'])) {
    setlocale(LC_TIME, 'sl_SI.UTF-8');
    $timestamp = strftime("%d. %B %Y ob %H:%M");

   /* $poruka = trim(str_replace(["\r", "\n"], " ", $_POST['poruka']));*/
  	$poruka = trim($_POST['poruka']);
	$poruka = nl2br(htmlspecialchars($poruka));


    $id     = time();

    $html = "<div class='post' data-id='$id'><hr><h3>{$_SESSION['ime']}</h3><p>$poruka</p><span class='vreme'>$timestamp</span><div class='akcije'><form method='POST' style='display:inline;'><input type='hidden' name='edit_id' value='$id'><button class='btn' type='submit'>Edit</button></form><form method='POST' style='display:inline;'><input type='hidden' name='delete_id' value='$id'><button class='btn' type='submit'>Delete</button></form><form method='POST' style='display:inline;'><input type='hidden' name='quote_id' value='$id'><button class='btn' type='submit'>Quote</button></form></div><hr></div>\n";
    

    file_put_contents("komentari.php", $html, FILE_APPEND);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

/* EMOJI FUNKCIJA */
function zameniEmoji($tekst) {
    $emojiMap = [
        ':sta:'      => '<img src="slike/sta.png" class="emoji" style="width:28px;">',
        ':namig:'    => '<img src="slike/namig.png" class="emoji" style="width:28px;">',
        ':tuga:'     => '<img src="slike/tuga.png" class="emoji" style="width:28px;">',
        ':smajli:'   => '<img src="slike/smajli.png" class="emoji" style="width:28px;">',
        ':ljut:'     => '<img src="slike/ljut.png" class="emoji" style="width:28px;">',
        ':like:'     => '<img src="slike/like.png" class="emoji" style="width:28px;">',
        ':bolestan:' => '<img src="slike/bolestan.png" class="emoji" style="width:28px;">',
        ':lizko:'    => '<img src="slike/lizko.png" class="emoji" style="width:28px;">',
        ':cool:'     => '<img src="slike/cool.png" class="emoji" style="width:28px;">',
        ':mafija:'   => '<img src="slike/mafija.png" class="emoji" style="width:28px;">',
        ':vruce:'    => '<img src="slike/heat.gif" class="emoji" style="width:46px;">',
        ':tuzan:'    => '<img src="slike/ac.gif" class="emoji" data-code=":tuzan:">',
        ':veseo:'    => '<img src="slike/ab.gif" class="emoji" data-code=":veseo:">',
        ':ah:'    => '<img src="slike/ah.gif" class="emoji" data-code=":ah:">',
        ':st:'       => '<img src="slike/st.gif" class="emoji" data-code=":st:" style="width:50px;">',
        ':smoke:'    => '<img src="slike/smoke.gif" class="emoji" data-code=":smoke:" style="width:50px;">',
        ':party:'    => '<img src="slike/party.gif" class="emoji" data-code=":party:" style="width:100px;">',
        ':shame:'    => '<img src="slike/shame.gif" class="emoji" data-code=":shame:" style="width:50px;">'


        
    ];

    foreach ($emojiMap as $kod => $img) {
        $tekst = str_replace($kod, $img, $tekst);
    }

    return $tekst;
}

/* PRIKAZ PORUKA */
ob_start();
if (file_exists("komentari.php")) {
    include("komentari.php");
}
$poruke = ob_get_clean();
$poruke = zameniEmoji($poruke);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>cats</title>
<style>
  .sve{margin-left:50px;}
  .strelica{
    position: fixed;
    right:50px;
    bottom: 50px;
  }
  .strelica img{
    width:40px;
    height: auto;
  }
  .emoji-container {
    margin-bottom: 10px;
  }
  .emoji {
    width: 32px;
    height: auto;
    cursor: pointer;
    margin: 5px;
    vertical-align: middle;
  }

  body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(to right, #f0f4ff, #e0e7ff);
    color: #333;
  }

  textarea {
    width: 100%;
    max-width: 600px;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
    resize: vertical;
  }

  input[type="submit"], button {
    background-color: #4a6cf7;
    color: white;
    border: none;
    padding: 10px 20px;
    margin-top: 10px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  input[type="submit"]:hover, button:hover {
    background-color: #3a5de0;
  }

  .poruka {
    background-color: #ffffff;
    border-radius: 10px;
    padding: 10px;
    margin: 10px 0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    max-width: 600px;
  }
  .poruka strong {
    color: #4a6cf7;
  }
    .post{
         background-color: #ffffff;
    border-radius: 10px;
    padding: 10px;
    margin: 10px 50px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    max-width: 600px;
        
    }
  .vreme {
    font-size: 12px;
    color: #888;
    margin-left: 10px;
  }
    .btn {
    background: #e6e6e6;
    color: #333;
    border: 1px solid #ccc;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 13px;
    cursor: pointer;
}
.btn:hover {
    background: #dcdcdc;
}
    .pano{
    display:none;
    width: 400px;
    height:auto;
    background-color:white;
    margin: 5px solid blue;


}
</style>
</head>
<body>

<div class="sve">

<div id="up"></div>

<h2>Dobrodošli, <?php echo htmlspecialchars($_SESSION['ime']); ?>!</h2>

<form method="post">
    <button type="submit" name="logout">Odjavi se</button>
</form>

<?php if (isset($_SESSION['edit_id'])): ?>
    <h3>Edit poruke</h3>
    <form method="POST">
        <textarea name="nova_poruka" rows="4" cols="50"></textarea><br>
        <button type="submit" name="save_edit">Sačuvaj</button>
    </form>
<?php endif; ?>

<?php
echo $poruke;
?>

<div class="emoji-container">
    <img src="slike/sta.png" class="emoji" data-code=":sta:">
    <img src="slike/tuga.png" class="emoji" data-code=":tuga:">
    <img src="slike/namig.png" class="emoji" data-code=":namig:">
    <img src="slike/smajli.png" class="emoji" data-code=":smajli:">
    <img src="slike/bolestan.png" class="emoji" data-code=":bolestan:">
    <img src="slike/cool.png" class="emoji" data-code=":cool:">
    <img src="slike/lizko.png" class="emoji" data-code=":lizko:">
    <img src="slike/ljut.png" class="emoji" data-code=":ljut:">
    <img src="slike/like.png" class="emoji" data-code=":like:">
    <img src="slike/mafija.png" class="emoji" data-code=":mafija:">
    <img src="slike/heat.gif" class="emoji" data-code=":vruce:" style="width:46px;">
    <img src="slike/ac.gif" class="emoji" data-code=":tuzan:">
    <img src="slike/ab.gif" class="emoji" data-code=":veseo:">
    <button id="otvori" onclick="promeni()">još...</button>
    <div class="pano">
        <img src="slike/heat.gif" class="emoji" data-code=":vruce:" style="width:50px;">
        <img src="slike/ac.gif" class="emoji" data-code=":tuzan:">
        <img src="slike/ab.gif" class="emoji" data-code=":veseo:">
        <img src="slike/st.gif" class="emoji" data-code=":st:">
        <img src="slike/smoke.gif" class="emoji" data-code=":smoke:" style="width:50px;">
        <img src="slike/shame.gif" class="emoji" data-code=":shame:" style="width:40px;">
        <img src="slike/ah.gif" class="emoji" data-code=":ah:" style="width:50px;">
       <img src="slike/party.gif" class="emoji" data-code=":party:"style="width:100px;">
    </div>


</div>

<form method="POST">
    <?php
$prefill = "";

// QUOTE
if (isset($_SESSION['quote_text'])) {
    $prefill = $_SESSION['quote_text'];
    unset($_SESSION['quote_text']);
}
    // EDIT
if (isset($_SESSION['edit_id'])) {
    $id = $_SESSION['edit_id'];
    $linije = file("komentari.php");

    foreach ($linije as $linija) {
        if (strpos($linija, "data-id='$id'") !== false) {
            if (preg_match('/<p>(.*?)<\/p>/', $linija, $m)) {
                // vrati <br> u \n
                $prefill = str_replace("<br>", "\n", $m[1]);
            }
        }
    }
}
?>

    
    <textarea id="chatInput" name="poruka" rows="5" cols="40" placeholder="Napiši poruku..."><?php 
        echo htmlspecialchars($prefill);
    ?></textarea><br>

    <?php if (isset($_SESSION['edit_id'])): ?>
        <input type="submit" name="save_edit" value="Sačuvaj izmene">
    <?php else: ?>
        <input type="submit" name="submit" value="Pošalji">
    <?php endif; ?>
</form>
    
<div class="strelica">

<a href="#up">
  <img src="slike/gore.png" alt="gore" id="gore"></a>
<a href="#down">  <img src="slike/dole.png" alt="dole" id="dole"></a>

</div>
    
<div id="down"></div>

</div>

<script>
const emojis = document.querySelectorAll('.emoji');
const textarea = document.getElementById('chatInput');

emojis.forEach(emoji => {
    emoji.addEventListener('click', () => {
        const code = emoji.getAttribute('data-code');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.slice(0, start) + code + text.slice(end);
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + code.length;
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const posts = document.querySelectorAll('.post');
    if (posts.length) posts[posts.length - 1].scrollIntoView();
});
</script>
<script>
    function promeni() {
    const pano = document.getElementsByClassName("pano")[0];
    pano.style.display = (pano.style.display === "none") ? "inline-block" : "none";
}
</script>
    <div class="counter">
    <?php include "counter.php"; ?>
</div>

</body>
</html>


  