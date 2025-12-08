<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Login / Inscription</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <form id="loginForm" >
        <h2>Cree un compt</h2>
        <label>Nom :</label>
        <input type="text" id="FullName" placeholder="Jean Aloka" />

        <label>Email :</label>
        <input type="email" id="Email" placeholder="...........@gmail.com"/>

        <label>Mot de passe :</label>
        <input type="password" id="PassCode" placeholder="**********"/>

        <label>Type de compte :</label>
        <div>
            <input type="radio" name="TypeOfAcc" class="TypeOfAcc" value="Client"><label for="" class="lableClientClass">Client</label> 
            <input type="radio" name="TypeOfAcc" class="TypeOfAcc" value="Garage"> <label for="">Garage</label>
        </div>
        <div id="garageFields" >
           <input type="text" id="garageName" placeholder="Nom du garage">
           <input type="text" id="garageLocation" placeholder="Localisation">
        </div>

        <button type="button" id="SubmitButton" onclick="Jsondata()">Envoyer</button>
        <a class="connectionLink" href="pageLogin.php">Se connecter</a>
    </form>

    <script src="script.js"></script>
</body>
</html>
