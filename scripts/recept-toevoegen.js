let editMode = false;
let editId = null;

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

    // Check if editing
    const urlParams = new URLSearchParams(window.location.search);
    const receptId = urlParams.get('id');

    if (receptId) {
        editMode = true;
        editId = receptId;
        document.getElementById('formTitle').textContent = 'Recept Bewerken';
        document.getElementById('submitBtn').textContent = 'Bijwerken';
        await loadReceptForEdit(receptId);
    }

    // Form submit handler
    document.getElementById('receptForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        await saveRecept();
    });

    // Image file input handler
    document.getElementById('afbeelding_file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check if file is an image
            if (!file.type.startsWith('image/')) {
                alert('Selecteer een geldige afbeelding');
                e.target.value = '';
                return;
            }

            // Check file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Afbeelding is te groot. Maximum grootte is 5MB');
                e.target.value = '';
                return;
            }

            // Read file as base64
            const reader = new FileReader();
            reader.onload = function(event) {
                const base64String = event.target.result;
                document.getElementById('afbeelding_url').value = base64String;
                
                // Show preview
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImage');
                previewImg.src = base64String;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            // Hide preview if no file selected
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('afbeelding_url').value = '';
        }
    });

    document.getElementById('searchInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
});

async function loadReceptForEdit(id) {
    const recept = await getRecept(id);

    if (recept.error) {
        showMessage(recept.error, 'error');
        return;
    }

    const user = getUserSession();
    if (user.id !== recept.gebruiker_id && !isAdmin()) {
        showMessage('Je hebt geen toestemming om dit recept te bewerken', 'error');
        setTimeout(() => {
            window.location.href = 'homepage.html';
        }, 2000);
        return;
    }

    document.getElementById('titel').value = recept.titel;
    document.getElementById('beschrijving').value = recept.beschrijving || '';
    document.getElementById('categorie').value = recept.categorie;
    document.getElementById('ingrediënten').value = recept.ingrediënten;
    document.getElementById('instructies').value = recept.instructies;
    
    // Load image if it exists (could be URL or base64)
    if (recept.afbeelding_url) {
        document.getElementById('afbeelding_url').value = recept.afbeelding_url;
        // Show preview if it's a base64 string
        if (recept.afbeelding_url.startsWith('data:image')) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImage');
            previewImg.src = recept.afbeelding_url;
            preview.style.display = 'block';
        }
    }
}

async function saveRecept() {
    const messageDiv = document.getElementById('message');
    messageDiv.textContent = '';
    messageDiv.className = '';

    const formData = {
        titel: document.getElementById('titel').value,
        beschrijving: document.getElementById('beschrijving').value,
        categorie: document.getElementById('categorie').value,
        type_gerecht: '',
        ingrediënten: document.getElementById('ingrediënten').value,
        instructies: document.getElementById('instructies').value,
        afbeelding_url: document.getElementById('afbeelding_url').value
    };

    let result;
    if (editMode) {
        result = await updateRecept(editId, formData);
    } else {
        result = await createRecept(formData);
    }

    if (result.success) {
        showMessage(editMode ? 'Recept succesvol bijgewerkt!' : 'Recept succesvol toegevoegd!', 'success');
        setTimeout(() => {
            window.location.href = editMode ? `recept-detail.html?id=${editId}` : 'homepage.html';
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

