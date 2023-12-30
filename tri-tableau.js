var debounceTimer;

function debounce(func, delay) {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(func, delay);
}

function sortTable(columnIndex) {
    debounce(sortTableLogic(columnIndex), 300);
}

function sortTableLogic(columnIndex) {
    var table, rows, switching, shouldSwitch, switchcount = 0, i;
    var ordreTri = "asc";
    table = document.getElementById("table_affichage");
    switching = true;

    // Tant qu'il reste des lignes à trier
    while (switching) {
        switching = false;
        rows = table.getElementsByTagName("TR");

        // On parcourt toutes les lignes pour voir s'il reste des lignes à trier
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            // On compare les lignes adjacentes
            var ligneUne = rows[i].getElementsByTagName("TD")[columnIndex];
            var ligneDeux = rows[i + 1].getElementsByTagName("TD")[columnIndex];

            // On détermine si les lignes doivent être pérmutées en fonction de l'odre du tri
            if ((ordreTri == "asc" && ligneUne.innerHTML.toLowerCase() > ligneDeux.innerHTML.toLowerCase()) ||
                (ordreTri == "desc" && ligneUne.innerHTML.toLowerCase() < ligneDeux.innerHTML.toLowerCase())) {
                shouldSwitch = true;
                break;
            }
        }

        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
        } else if (switchcount === 0) {
            ordreTri = ordreTri == "asc" ? "desc" : "asc";
            switching = true;
        }
    }

    // Update the icons next to headers
    updateSortIcons(columnIndex, ordreTri);
}

function updateSortIcons(sortedColumnIndex, ordreTri) {
    // Remove classes from all headers
    var headers = document.getElementById("table_affichage").getElementsByTagName("TH");
    for (var i = 0; i < headers.length; i++) {
        headers[i].innerHTML = headers[i].innerHTML.replace(" ▲", "").replace(" ▼", "");
    }

    // Add the correct class to the sorted header
    var header = headers[sortedColumnIndex];
    if (ordreTri == "asc") {
        header.innerHTML += " ▲";
    } else {
        header.innerHTML += " ▼";
    }
}