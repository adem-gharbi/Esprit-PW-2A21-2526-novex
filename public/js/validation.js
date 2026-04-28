window.onload = function () {

    let valid = {
        ville: false,
        pays: false,
        description: false,
        image: false,
        categorie: false
    };

    function setMsg(id, message, isValid) {
        let el = document.getElementById(id);
        el.innerHTML = message;

        if (isValid) {
            el.className = "msg msg-success";
        } else {
            el.className = "msg msg-error";
        }
    }

    // VILLE
    document.getElementById("ville").addEventListener("input", function () {
        if (this.value.trim().length >= 3) {
            setMsg("errVille", "✔ Ville valide", true);
            valid.ville = true;
        } else {
            setMsg("errVille", "❌ Obligatoire (min 3 caractères)", false);
            valid.ville = false;
        }
    });

    // PAYS
    document.getElementById("pays").addEventListener("input", function () {
        if (this.value.trim().length >= 3) {
            setMsg("errPays", "✔ Pays valide", true);
            valid.pays = true;
        } else {
            setMsg("errPays", "❌ Obligatoire (min 3 caractères)", false);
            valid.pays = false;
        }
    });

    // DESCRIPTION
    document.getElementById("description").addEventListener("input", function () {
        if (this.value.trim().length >= 10) {
            setMsg("errDescription", "✔ Description valide", true);
            valid.description = true;
        } else {
            setMsg("errDescription", "❌ Obligatoire (min 10 caractères)", false);
            valid.description = false;
        }
    });

    // IMAGE
    document.getElementById("image").addEventListener("input", function () {
        if (this.value.trim() !== "") {
            setMsg("errImage", "✔ Image OK", true);
            valid.image = true;
        } else {
            setMsg("errImage", "❌ Obligatoire", false);
            valid.image = false;
        }
    });

    // CATEGORIE ( 3 caractères minimum)
    document.getElementById("categorie").addEventListener("input", function () {
        if (this.value.trim().length >= 3) {
            setMsg("errCategorie", "✔ Catégorie valide", true);
            valid.categorie = true;
        } else {
            setMsg("errCategorie", "❌ Min 3 caractères", false);
            valid.categorie = false;
        }
    });

    // SUBMIT
    window.validateDestination = function () {

        let hasError = false;

        // FORCER validation live pour afficher messages
        document.getElementById("ville").dispatchEvent(new Event('input'));
        document.getElementById("pays").dispatchEvent(new Event('input'));
        document.getElementById("description").dispatchEvent(new Event('input'));
        document.getElementById("image").dispatchEvent(new Event('input'));
        document.getElementById("categorie").dispatchEvent(new Event('input'));

        // check vide → alert
        if (

            !valid.ville ||
            !valid.pays ||
            !valid.description ||
            !valid.image ||
            !valid.categorie
        ) {
            alert("⚠️ Veuillez remplir tous les champs correctement !");
            return false;
        }

        return true;
    };
};