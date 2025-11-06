document.addEventListener('DOMContentLoaded', async function () {
    const user = getUserSession();
    if (!user) {
        window.location.href = '../index.html';
        return;
    }

    if (!isAdmin()) {
        alert('Je hebt geen toegang tot deze pagina');
        window.location.href = 'homepage.html';
        return;
    }

    updateUserDisplay();
    document.getElementById('adminLink').style.display = 'block';

    await loadRecepten();
    await loadGebruikers();
    await loadRecensies();

    document.getElementById('searchInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
});

function switchTab(tabName) {
    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

    if (tabName === 'recepten') {
        document.querySelector('.tab:nth-child(1)').classList.add('active');
        document.getElementById('receptenTab').classList.add('active');
    } else if (tabName === 'gebruikers') {
        document.querySelector('.tab:nth-child(2)').classList.add('active');
        document.getElementById('gebruikersTab').classList.add('active');
    } else if (tabName === 'recensies') {
        document.querySelector('.tab:nth-child(3)').classList.add('active');
        document.getElementById('recensiesTab').classList.add('active');
    }
}

async function loadRecepten() {
    const recepten = await getRecepten({});
    const container = document.getElementById('receptenTable');

    if (Array.isArray(recepten) && recepten.length > 0) {
        let html = `
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titel</th>
                        <th>Categorie</th>
                        <th>Auteur</th>
                        <th>Datum</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
        `;

        recepten.forEach(recept => {
            html += `
                <tr>
                    <td>${recept.id}</td>
                    <td>${recept.titel}</td>
                    <td>${recept.categorie}</td>
                    <td>${recept.voornaam} ${recept.achternaam}</td>
                    <td>${new Date(recept.created_at).toLocaleDateString('nl-NL')}</td>
                    <td>
                        <a href="recept-toevoegen.html?id=${recept.id}" class="btn btn-edit">Bewerken</a>
                        <button onclick="deleteReceptAdmin(${recept.id})" class="btn btn-delete">Verwijderen</button>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        container.innerHTML = html;
    } else {
        container.innerHTML = '<p>Geen recepten gevonden.</p>';
    }
}

async function loadGebruikers() {
    const gebruikers = await getAllUsers();
    const container = document.getElementById('gebruikersTable');
    const user = getUserSession();

    if (Array.isArray(gebruikers) && gebruikers.length > 0) {
        let html = `
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Naam</th>
                        <th>E-mail</th>
                        <th>Telefoon</th>
                        <th>Rol</th>
                        <th>Datum</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
        `;

        gebruikers.forEach(gebruiker => {
            const isAdminUser = gebruiker.is_admin === 1;
            const isCurrentUser = gebruiker.id === user.id;
            html += `
                <tr>
                    <td>${gebruiker.id}</td>
                    <td>${gebruiker.voornaam} ${gebruiker.achternaam}</td>
                    <td>${gebruiker.email}</td>
                    <td>${gebruiker.telefoon}</td>
                    <td>
                        <span class="admin-badge ${isAdminUser ? 'badge-admin' : 'badge-user'}">
                            ${isAdminUser ? 'Admin' : 'Gebruiker'}
                        </span>
                    </td>
                    <td>${new Date(gebruiker.created_at).toLocaleDateString('nl-NL')}</td>
                    <td>
                        <button onclick="editUser(${gebruiker.id})" class="btn btn-edit">Bewerken</button>
                        ${isCurrentUser ? 
                            '<button disabled class="btn btn-delete" style="opacity: 0.5; cursor: not-allowed;" title="Je kunt je eigen account niet verwijderen">Verwijderen (Jezelf)</button>' : 
                            `<button onclick="deleteUserAdmin(${gebruiker.id})" class="btn btn-delete">Verwijderen</button>`
                        }
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        container.innerHTML = html;
    } else {
        container.innerHTML = '<p>Geen gebruikers gevonden.</p>';
    }
}

async function deleteReceptAdmin(id) {
    if (!confirm('Weet je zeker dat je dit recept wilt verwijderen?')) {
        return;
    }

    const result = await deleteRecept(id);
    if (result.success) {
        alert('Recept verwijderd!');
        await loadRecepten();
    } else {
        alert(result.error || 'Fout bij verwijderen');
    }
}

async function deleteUserAdmin(id) {
    const user = getUserSession();
    if (id === user.id) {
        alert('Je kunt je eigen account niet verwijderen');
        return;
    }

    if (!confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?')) {
        return;
    }

    const result = await deleteUser(id);
    if (result.success) {
        alert('Gebruiker verwijderd!');
        await loadGebruikers();
    } else {
        alert(result.error || 'Fout bij verwijderen');
    }
}

async function loadRecensies() {
    const recensies = await getAllRecensies();
    const container = document.getElementById('recensiesTable');

    if (Array.isArray(recensies) && recensies.length > 0) {
        // Get all recipes and users for display
        const recepten = await getRecepten({});
        const gebruikers = await getAllUsers();

        let html = `
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Recept | Categorie door: Auteur</th>
                        <th>Recensie door</th>
                        <th>Beoordeling</th>
                        <th>Titel</th>
                        <th>Tekst</th>
                        <th>Datum</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
        `;

        recensies.forEach(recensie => {
            const recept = recepten.find(r => r.id === recensie.recept_id);
            const gebruiker = gebruikers.find(u => u.id === recensie.gebruiker_id);
            const receptAuteur = recept ? gebruikers.find(u => u.id === recept.gebruiker_id) : null;
            
            const receptTitel = recept ? recept.titel : 'Onbekend recept';
            const receptCategorie = recept ? recept.categorie : '';
            const receptAuteurNaam = receptAuteur ? `${receptAuteur.voornaam} ${receptAuteur.achternaam}` : 'Onbekend';
            const gebruikerNaam = gebruiker ? `${gebruiker.voornaam} ${gebruiker.achternaam}` : 'Onbekende gebruiker';
            const tekstPreview = recensie.tekst.length > 50 ? recensie.tekst.substring(0, 50) + '...' : recensie.tekst;
            
            const receptInfo = recept ? `${receptTitel} | ${receptCategorie} door: ${receptAuteurNaam}` : 'Onbekend recept';

            html += `
                <tr>
                    <td>${recensie.id}</td>
                    <td>${receptInfo}</td>
                    <td>${gebruikerNaam}</td>
                    <td>
                        <span class="recensie-rating-admin">
                            ${'★'.repeat(recensie.beoordeling)}${'☆'.repeat(5 - recensie.beoordeling)}
                        </span>
                    </td>
                    <td>${recensie.titel}</td>
                    <td title="${recensie.tekst}">${tekstPreview}</td>
                    <td>${new Date(recensie.created_at).toLocaleDateString('nl-NL')}</td>
                    <td>
                        <button onclick="deleteRecensieAdmin(${recensie.id})" class="btn btn-delete">Verwijderen</button>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        container.innerHTML = html;
    } else {
        container.innerHTML = '<p>Geen recensies gevonden.</p>';
    }
}

async function deleteRecensieAdmin(id) {
    if (!confirm('Weet je zeker dat je deze recensie wilt verwijderen?')) {
        return;
    }

    const result = await deleteRecensie(id);
    if (result.success) {
        alert('Recensie verwijderd!');
        await loadRecensies();
    } else {
        alert(result.error || 'Fout bij verwijderen');
    }
}

function editUser(id) {
    const newRole = prompt('Wijzig rol (admin/user):');
    if (newRole === null) return;

    const isAdminUser = newRole.toLowerCase() === 'admin';
    alert('Gebruiker bewerken functionaliteit kan worden uitgebreid');
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

