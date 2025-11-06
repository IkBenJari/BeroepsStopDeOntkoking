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

    // Get URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const categorie = urlParams.get('categorie');
    const zoekterm = urlParams.get('zoekterm');

    if (categorie) {
        document.getElementById('categorieFilter').value = categorie;
    }

    if (zoekterm) {
        document.getElementById('searchInput').value = zoekterm;
    }

    await loadRecepten();

    document.getElementById('searchInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
});

async function loadRecepten() {
    const categorie = document.getElementById('categorieFilter').value;
    const zoekterm = document.getElementById('searchInput').value;

    const filters = {};
    if (categorie) filters.categorie = categorie;
    if (zoekterm) filters.zoekterm = zoekterm;

    const recepten = await getRecepten(filters);
    const container = document.getElementById('receptenContainer');

    if (Array.isArray(recepten) && recepten.length > 0) {
        container.innerHTML = '';
        recepten.forEach(recept => {
            const card = createReceptCard(recept);
            container.appendChild(card);
        });
    } else {
        container.innerHTML = '<div class="no-results">Geen recepten gevonden. Probeer andere filters of voeg een nieuw recept toe!</div>';
    }
}

function createReceptCard(recept) {
    const card = document.createElement('div');
    card.className = 'recept-card';
    card.onclick = () => window.location.href = `recept-detail.html?id=${recept.id}`;

    const imageUrl = recept.afbeelding_url || '../images/gezond-recept.jpg';

    card.innerHTML = `
        <img src="${imageUrl}" alt="${recept.titel}" onerror="this.src='../images/gezond-recept.jpg'">
        <div class="recept-card-content">
            <h3>${recept.titel}</h3>
            <p class="recept-categorie">${recept.categorie}</p>
            <p class="recept-auteur">Door: ${recept.voornaam} ${recept.achternaam}</p>
        </div>
    `;

    return card;
}

function applyFilters() {
    loadRecepten();
}

function performSearch() {
    loadRecepten();
}

function logout() {
    if (confirm('Weet je zeker dat je wilt uitloggen?')) {
        clearUserSession();
        window.location.href = '../index.html';
    }
}

