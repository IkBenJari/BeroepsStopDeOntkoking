// Homepage functionality
document.addEventListener('DOMContentLoaded', async function() {
    // Check if user is logged in
    const user = getUserSession();
    if (!user) {
        window.location.href = '../index.html';
        return;
    }
    
    updateUserDisplay();
    
    // Show admin link if user is admin
    if (isAdmin()) {
        document.getElementById('adminLink').style.display = 'block';
    }
    
    // Load recent recipes
    await loadRecentRecepten();
    
    // Search on Enter key
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
});

async function loadRecentRecepten() {
    const recepten = await getRecepten({});
    const container = document.getElementById('recentRecepten');
    
    if (Array.isArray(recepten) && recepten.length > 0) {
        container.innerHTML = '';
        const recentRecepten = recepten.slice(0, 6); // Show 6 most recent
        
        recentRecepten.forEach(recept => {
            const receptCard = createReceptCard(recept);
            container.appendChild(receptCard);
        });
    } else {
        container.innerHTML = '<p>Nog geen recepten beschikbaar. Voeg als eerste een recept toe!</p>';
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

function performSearch() {
    const searchTerm = document.getElementById('searchInput').value;
    if (searchTerm.trim()) {
        window.location.href = `overzicht.html?zoekterm=${encodeURIComponent(searchTerm)}`;
    }
}

function logout() {
    if (confirm('Weet je zeker dat je wilt uitloggen?')) {
        clearUserSession();
        window.location.href = '../index.html';
    }
}

