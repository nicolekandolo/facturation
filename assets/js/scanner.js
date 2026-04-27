let lecteurCodeBarre = null;
let scanDetecte = false;

function demarrerScanner() {
    const zone = document.getElementById('zone-scanner');
    const champCodeBarre = document.getElementById('code_barre');

    if (!zone || !champCodeBarre) {
        return;
    }

    if (typeof ZXing === 'undefined') {
        zone.innerHTML = '<p class="erreur">La bibliotheque ZXing n est pas chargee.</p>';
        return;
    }

    if (lecteurCodeBarre !== null) {
        lecteurCodeBarre.reset();
    }

    lecteurCodeBarre = new ZXing.BrowserMultiFormatReader();
    scanDetecte = false;

    zone.innerHTML = ''
        + '<p>Scanner en cours... Placez le code-barres bien en face de la camera.</p>'
        + '<video id="video-scan" width="420" height="280" autoplay muted playsinline></video>'
        + '<p><button type="button" onclick="arreterScanner()">Arreter le scanner</button></p>';

    const contraintesCamera = { facingMode: 'environment' };

    lecteurCodeBarre.decodeFromConstraints(
        { video: contraintesCamera },
        'video-scan',
        function (result, err) {
            if (result) {
                scanDetecte = true;
                lecteurCodeBarre.reset();
                zone.innerHTML = '<p class="succes">Code detecte : ' + result.text + '</p>';
                window.alert('Scan reussi. Code detecte : ' + result.text);
            }
        }
    ).catch(function () {
        zone.innerHTML = '<p class="erreur">Impossible d ouvrir la camera. Verifiez les permissions du navigateur.</p>';
        window.alert('Echec du scan : impossible d ouvrir la camera.');
    });
}

function arreterScanner() {
    const zone = document.getElementById('zone-scanner');
    if (lecteurCodeBarre !== null) {
        lecteurCodeBarre.reset();
    }
    if (zone) {
        zone.innerHTML = '<p>Scanner arrete.</p>';
    }
    if (!scanDetecte) {
        window.alert('Scan non detecte. Aucun code-barres n a ete lu.');
    }
}
