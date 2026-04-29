/**
 * Scanner de code-barres
 */

let scannerActive = false;
let scannedCode = '';
let scanTimeout = null;

// Initialiser le listener global pour le scanner
document.addEventListener('keypress', function (e) {
    if (document.activeElement.id === 'barcode-input' || (scannerActive && e.key !== 'Enter')) {
        scannedCode += e.key;

        // Réinitialiser le timer
        clearTimeout(scanTimeout);
        scanTimeout = setTimeout(() => {
            processBarcodeScan(scannedCode);
            scannedCode = '';
        }, 100);
    }
});

function openScanner() {
    const modal = document.getElementById('scanner-modal');
    if (modal) {
        modal.classList.remove('hidden');
        scannerActive = true;
        document.getElementById('barcode-input').focus();
    }
}

function closeScanner() {
    const modal = document.getElementById('scanner-modal');
    if (modal) {
        modal.classList.add('hidden');
        scannerActive = false;
        scannedCode = '';
    }
}

function processBarcodeScan(barcode) {
    if (!barcode.trim()) return;

    // Envoyer la requête au serveur pour trouver le produit
    fetch('/facturation/modules/produits/lire.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ barcode: barcode })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.product) {
                addProductToCart(data.product);
                closeScanner();
            } else {
                alert('Produit non trouvé: ' + barcode);
            }
        })
        .catch(err => {
            console.error('Erreur:', err);
            alert('Erreur lors de la lecture du code-barres');
        });
}

function addProductToCart(product) {
    // Fonction à implémenter dans la page POS
    if (typeof addToCart === 'function') {
        addToCart(product);
    }
}
