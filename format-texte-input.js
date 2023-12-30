function premiereLettreMajuscule(input) {
    input.addEventListener('input', function () {
        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();
    });
}

function supprimerEspaces(input) {
    input.addEventListener('input', function () {
        this.value = this.value.replace(/\s/g, '');
    });
}

function empecherPlusieursEspaces(input) {
    input.addEventListener('input', function () {
        this.value = this.value.replace(/\s+/g, ' ');
    });
}

function chiffresUniquement(event) {
    const input = event.target;
    const texteInput = input.value;
    input.value = texteInput.replace(/\D/g, '');
}


var inputsTextuels = document.querySelectorAll('input[type="text"]:not(.espaces_permis)');

// On attache un listener à tous les inputs de type 'text' 
// pour mettre la premiere lettre en majuscule et empecher les
// espaces, sauf aux inputs le permettant (classe espaces_permis)
inputsTextuels.forEach(function (input) {
    premiereLettreMajuscule(input);
    supprimerEspaces(input);
});

var inputsTextuelsAvecEspaces = document.querySelectorAll('input[type="text"].espaces_permis');

// On attache un listener à tous les inputs de type 'text' 
// et de classe 'espaces_permis' pour empecher plus d'un espace à la fois
inputsTextuelsAvecEspaces.forEach(function (input){
    empecherPlusieursEspaces(input);
});