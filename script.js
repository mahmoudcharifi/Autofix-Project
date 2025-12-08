function Jsondata() {
    const fullName = document.getElementById("FullName").value.trim();
    const email = document.getElementById("Email").value.trim();
    const pass = document.getElementById("PassCode").value.trim();
    const type = document.querySelector("input[name='TypeOfAcc']:checked");

    if (!fullName || !email || !pass || !type) {
        alert("Veuillez remplir tous les champs");
        return;
    }

    let formData = new FormData();
    formData.append("fullName", fullName);
    formData.append("email", email);
    formData.append("password", pass);
    formData.append("type", type.value);

    fetch("dataStorage.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if (data === "garage_incomplete") {
            window.location.href = "completeGarage.php";
        } else if (data === "success") {
            window.location.href = "pageLogin.php";
        } else {
            alert(data);
        }
    });
}
