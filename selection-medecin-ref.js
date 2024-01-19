const comboboxUsagers = document.getElementById('combobox_usagers');
const comboboxMedecins = document.getElementById('combobox_medecins');

comboboxUsagers.addEventListener('change', function() {
  const idMedecinRef = comboboxUsagers.options[comboboxUsagers.selectedIndex].getAttribute('data-idMedecinRef');

  // On cherche l'option correspondant à l'id du médecin référent
  if (idMedecinRef.length > 0){
    for (let i = 0; i < comboboxMedecins.options.length; i++) {
        if (comboboxMedecins.options[i].value === idMedecinRef) {
            comboboxMedecins.selectedIndex = i;
          break;
        }
      }
  } 
});