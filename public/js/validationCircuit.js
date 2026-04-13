window.onload = function () {

    let valid = {
        titre: false,
        duree: false,
        prix: false,
        places: false,
        date: false,
        dest: false,
        hotel: false
    };

    function setMsg(id, msg, ok) {
        let el = document.getElementById(id);
        el.innerHTML = msg;
        el.className = ok ? "msg msg-success" : "msg msg-error";
    }

    // =========================
    // TITRE (min 4 chars)
    // =========================
    document.getElementById("titre").addEventListener("input", function () {
        if (this.value.trim().length >= 4) {
            setMsg("errTitre", "✔ Titre valide", true);
            valid.titre = true;
        } else {
            setMsg("errTitre", "❌ Min 4 caractères", false);
            valid.titre = false;
        }
    });

    // =========================
    // DUREE (chiffres uniquement)
    // =========================
    document.getElementById("duree").addEventListener("input", function () {

        if (/^\d+$/.test(this.value.trim())) {
            setMsg("errDuree", "✔ Durée valide", true);
            valid.duree = true;
        } else {
            setMsg("errDuree", "❌ Chiffres uniquement", false);
            valid.duree = false;
        }
    });

    // =========================
    // PRIX (float)
    // =========================
    document.getElementById("prix").addEventListener("input", function () {

        if (/^\d+(\.\d+)?$/.test(this.value.trim())) {
            setMsg("errPrix", "✔ Prix valide", true);
            valid.prix = true;
        } else {
            setMsg("errPrix", "❌ Format: 100 ou 100.50", false);
            valid.prix = false;
        }
    });

    // =========================
    // PLACES (>=1)
    // =========================
    document.getElementById("nb_places").addEventListener("input", function () {

        if (parseInt(this.value) >= 1) {
            setMsg("errPlaces", "✔ OK", true);
            valid.places = true;
        } else {
            setMsg("errPlaces", "❌ minimum 1", false);
            valid.places = false;
        }
    });

    // =========================
    // DATE (>= today)
    // =========================
    document.getElementById("date_depart").addEventListener("input", function () {

        let today = new Date().toISOString().split('T')[0];

        if (this.value >= today) {
            setMsg("errDate", "✔ Date valide", true);
            valid.date = true;
        } else {
            setMsg("errDate", "❌ date passée interdite", false);
            valid.date = false;
        }
    });

    // =========================
    // DESTINATION
    // =========================
    document.getElementById("id_destination").addEventListener("change", function () {

        if (this.value !== "") {
            setMsg("errDest", "✔ OK", true);
            valid.dest = true;
        } else {
            setMsg("errDest", "❌ obligatoire", false);
            valid.dest = false;
        }
    });

    // =========================
    // HOTEL
    // =========================
    document.getElementById("id_hotel").addEventListener("input", function () {

        if (this.value.trim() !== "") {
            setMsg("errHotel", "✔ OK", true);
            valid.hotel = true;
        } else {
            setMsg("errHotel", "❌ obligatoire", false);
            valid.hotel = false;
        }
    });

    // =========================
    // SUBMIT
    // =========================
    window.validateCircuit = function () {

        if (
            !valid.titre ||
            !valid.duree ||
            !valid.prix ||
            !valid.places ||
            !valid.date ||
            !valid.dest ||
            !valid.hotel
        ) {
            alert("⚠️ Veuillez vérifier les champs !");
            return false;
        }

        return true;
    };
};