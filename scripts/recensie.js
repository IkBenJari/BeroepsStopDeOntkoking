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

    // Load recipes for dropdown
    await loadRecepten();

    // Check if recept_id is in URL
    const urlParams = new URLSearchParams(window.location.search);
    const receptId = urlParams.get('recept_id');
    if (receptId) {
        document.getElementById('receptSelect').value = receptId;
    }

    // Form submit handler
    document.getElementById('recensieForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        await saveRecensie();
    });

    document.getElementById('searchInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
});

async function loadRecepten() {
    const recepten = await getRecepten({});
    const select = document.getElementById('receptSelect');

    if (Array.isArray(recepten) && recepten.length > 0) {
        recepten.forEach(recept => {
            const option = document.createElement('option');
            option.value = recept.id;
            option.textContent = recept.titel;
            select.appendChild(option);
        });
    } else {
        select.innerHTML = '<option value="">Geen recepten beschikbaar</option>';
    }
}

async function saveRecensie() {
    const messageDiv = document.getElementById('message');
    messageDiv.textContent = '';
    messageDiv.className = '';

    const beoordeling = document.querySelector('input[name="beoordeling"]:checked');
    if (!beoordeling) {
        showMessage('Selecteer een beoordeling', 'error');
        return;
    }

    const formData = {
        recept_id: parseInt(document.getElementById('receptSelect').value),
        beoordeling: parseInt(beoordeling.value),
        titel: document.getElementById('titel').value,
        tekst: document.getElementById('tekst').value
    };

    const result = await createRecensie(formData);

    if (result.success) {
        showMessage('Recensie succesvol geplaatst!', 'success');
        setTimeout(() => {
            window.location.href = `recept-detail.html?id=${formData.recept_id}`;
        }, 1500);
    } else {
        showMessage(result.error || 'Er is een fout opgetreden', 'error');
    }
}

function showMessage(message, type) {
    const messageDiv = document.getElementById('message');
    messageDiv.textContent = message;
    messageDiv.className = type === 'success' ? 'message-success' : 'message-error';
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

