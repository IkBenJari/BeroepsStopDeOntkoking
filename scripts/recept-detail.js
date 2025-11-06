let currentRecept = null;

document.addEventListener('DOMContentLoaded', async function () {
    const user = getUserSession();
    if (!user) {
        window.location.href = '../index.html';
        return;
    }

    updateUserDisplay();

    if (isAdmin()) {
        document.getElementById('adminLink').style.display = 'block';
    }

    const urlParams = new URLSearchParams(window.location.search);
    const receptId = urlParams.get('id');

    if (receptId) {
        await loadRecept(receptId);
    } else {
        document.getElementById('receptDetail').innerHTML = '<p>Recept niet gevonden.</p>';
    }

    document.getElementById('searchInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
});

async function loadRecept(id) {
    const recept = await getRecept(id);
    const container = document.getElementById('receptDetail');

    if (recept.error) {
        container.innerHTML = `<p>${recept.error}</p>`;
        return;
    }

    currentRecept = recept;
    const user = getUserSession();
    const canEdit = user && (user.id === recept.gebruiker_id || isAdmin());

    const imageUrl = recept.afbeelding_url || '../images/gezond-recept.jpg';
    const ingrediënten = recept.ingrediënten.split('\n').filter(i => i.trim());
    const instructies = recept.instructies.split('\n').filter(i => i.trim());

    // Load reviews
    const recensies = await getRecensiesByRecept(id);
    const recensiesHTML = Array.isArray(recensies) && recensies.length > 0 ? 
        recensies.map(recensie => `
            <div class="recensie-item">
                <div class="recensie-header">
                    <strong>Recensie door: ${recensie.voornaam} ${recensie.achternaam}</strong>
                    <div class="recensie-rating">
                        ${'★'.repeat(recensie.beoordeling)}${'☆'.repeat(5 - recensie.beoordeling)}
                    </div>
                    <span class="recensie-datum">${new Date(recensie.created_at).toLocaleDateString('nl-NL')}</span>
                </div>
                <h4>${recensie.titel}</h4>
                <p>${recensie.tekst}</p>
                ${(user.id === recensie.gebruiker_id || isAdmin()) ? `
                    <button onclick="deleteRecensieById(${recensie.id})" class="btn-delete-small">Verwijderen</button>
                ` : ''}
            </div>
        `).join('') : '<p>Nog geen recensies. Wees de eerste!</p>';

    // Ensure author name is available
    const auteurNaam = (recept.voornaam && recept.achternaam) 
        ? `${recept.voornaam} ${recept.achternaam}` 
        : 'Onbekend';

    container.innerHTML = `
        <div class="recept-header">
            <h1>${recept.titel}</h1>
            <div class="recept-meta">
                ${recept.categorie} | Door: ${auteurNaam}
            </div>
        </div>
        
        <img src="${imageUrl}" alt="${recept.titel}" class="recept-image" onerror="this.src='../images/gezond-recept.jpg'">
        
        ${recept.beschrijving ? `
            <div class="recept-section">
                <h2>Beschrijving</h2>
                <p>${recept.beschrijving}</p>
            </div>
        ` : ''}
        
        <div class="recept-section">
            <h2>Ingrediënten</h2>
            <ul>
                ${ingrediënten.map(ing => `<li>${ing}</li>`).join('')}
            </ul>
        </div>
        
        <div class="recept-section">
            <h2>Instructies</h2>
            <ol>
                ${instructies.map(inst => `<li>${inst}</li>`).join('')}
            </ol>
        </div>
        
        <div class="recept-actions">
            <a href="recensie-schrijven.html?recept_id=${recept.id}" class="btn btn-primary">Recensie Schrijven</a>
            ${canEdit ? `
                <a href="recept-toevoegen.html?id=${recept.id}" class="btn btn-primary">Bewerken</a>
                <button onclick="deleteReceptById(${recept.id})" class="btn btn-danger">Verwijderen</button>
            ` : ''}
            <a href="overzicht.html" class="btn btn-secondary">Terug naar overzicht</a>
        </div>

        <div class="recensies-section">
            <h2>Recensies</h2>
            <div class="recensies-container">
                ${recensiesHTML}
            </div>
        </div>
    `;
}

async function deleteReceptById(receptId) {
    if (!confirm('Weet je zeker dat je dit recept wilt verwijderen?')) {
        return;
    }

    const result = await deleteRecept(receptId);
    if (result.success) {
        alert('Recept verwijderd!');
        window.location.href = 'overzicht.html';
    } else {
        alert(result.error || 'Fout bij verwijderen');
    }
}

function performSearch() {
    const searchTerm = document.getElementById('searchInput').value;
    if (searchTerm.trim()) {
        window.location.href = `overzicht.html?zoekterm=${encodeURIComponent(searchTerm)}`;
    }
}

async function deleteRecensieById(recensieId) {
    if (!confirm('Weet je zeker dat je deze recensie wilt verwijderen?')) {
        return;
    }

    const result = await deleteRecensie(recensieId);
    if (result.success) {
        alert('Recensie verwijderd!');
        // Reload the page to refresh reviews
        const urlParams = new URLSearchParams(window.location.search);
        const receptId = urlParams.get('id');
        await loadRecept(receptId);
    } else {
        alert(result.error || 'Fout bij verwijderen');
    }
}

function logout() {
    if (confirm('Weet je zeker dat je wilt uitloggen?')) {
        clearUserSession();
        window.location.href = '../index.html';
    }
}

