// Recepten pagina functionaliteit
let allRecipes = [];
let currentFilter = 'all';

// DOM geladen
document.addEventListener('DOMContentLoaded', function() {
    loadRecipes();
    loadUserData();
    
    // Check voor opgeslagen zoekterm
    const savedSearchTerm = localStorage.getItem('searchTerm');
    if (savedSearchTerm) {
        document.getElementById('searchInput').value = savedSearchTerm;
        searchRecipes();
        localStorage.removeItem('searchTerm'); // Verwijder na gebruik
    }
});

// Laad recepten
function loadRecipes() {
    const recipesGrid = document.getElementById('recipesGrid');
    allRecipes = Array.from(recipesGrid.querySelectorAll('.recipe-card'));
}

// Laad gebruiker data
function loadUserData() {
    const savedUser = localStorage.getItem('currentUser');
    if (savedUser) {
        const user = JSON.parse(savedUser);
        document.getElementById('username').textContent = user.voornaam || 'Gast';
    }
}

// Filter recepten
function filterRecipes(category) {
    currentFilter = category;
    
    // Update active filter button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Filter recipe cards
    allRecipes.forEach(card => {
        const categories = card.dataset.category;
        
        if (category === 'all' || categories.includes(category)) {
            card.style.display = 'block';
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
            setTimeout(() => {
                if (card.classList.contains('hidden')) {
                    card.style.display = 'none';
                }
            }, 300);
        }
    });
}

// Zoek recepten
function searchRecipes() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    allRecipes.forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        const description = card.querySelector('p').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || description.includes(searchTerm)) {
            card.style.display = 'block';
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
            setTimeout(() => {
                if (card.classList.contains('hidden')) {
                    card.style.display = 'none';
                }
            }, 300);
        }
    });
}

// Bekijk recept detail
function viewRecipe(recipeId) {
    // Sla recept ID op voor detail pagina
    localStorage.setItem('selectedRecipe', recipeId);
    // Navigeer naar detail pagina
    window.location.href = 'recept-detail.html';
}

// Enter toets voor zoeken
document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && document.activeElement.id === 'searchInput') {
        searchRecipes();
    }
});

// Recipe data (simulatie van database)
const recipeDatabase = {
    'pancakes': {
        title: 'Fluffy Pancakes',
        image: '../images/Pancake.jpg',
        time: '15 min',
        servings: '2 personen',
        rating: '4.5/5',
        difficulty: 'Makkelijk',
        ingredients: [
            '200g bloem',
            '2 eieren',
            '300ml melk',
            '2 el suiker',
            '1 tl bakpoeder',
            'Snufje zout',
            'Boter voor bakken'
        ],
        instructions: [
            'Meng bloem, suiker, bakpoeder en zout in een kom',
            'Klop eieren en melk erdoor tot een gladde massa',
            'Verhit een pan met een beetje boter',
            'Schep beslag in de pan en bak goudbruin',
            'Keer om en bak de andere kant',
            'Serveer warm met stroop of fruit'
        ],
        tips: [
            'Laat het beslag 5 minuten rusten voor betere textuur',
            'Gebruik niet te veel beslag per pancake',
            'Houd de eerste pancakes warm in de oven'
        ]
    },
    'acai-bowl': {
        title: 'Acai Bowl',
        image: '../images/gezond-recept.jpg',
        time: '10 min',
        servings: '1 persoon',
        rating: '4.8/5',
        difficulty: 'Makkelijk',
        ingredients: [
            '100g bevroren acai pulp',
            '1 banaan',
            '50ml kokosmelk',
            '1 el honing',
            'Granola',
            'Verse bessen',
            'Kokossnippers'
        ],
        instructions: [
            'Laat acai pulp iets ontdooien',
            'Mix acai, halve banaan, kokosmelk en honing',
            'Blend tot een dikke smoothie',
            'Schep in een kom',
            'Garneer met granola, bessen en kokos',
            'Serveer direct'
        ],
        tips: [
            'Gebruik bevroren fruit voor dikke consistentie',
            'Varieer met verschillende toppings',
            'Eet direct op voor beste textuur'
        ]
    },
    'citroen-munt': {
        title: 'Citroen Munt Water',
        image: '../images/citroen-munt-drankje.webp',
        time: '5 min',
        servings: '4 glazen',
        rating: '4.7/5',
        difficulty: 'Zeer makkelijk',
        ingredients: [
            '1 liter water',
            '2 citroenen',
            '10 verse muntblaadjes',
            'IJsblokjes',
            'Honing naar smaak'
        ],
        instructions: [
            'Was citroenen en snijd in schijfjes',
            'Was muntblaadjes voorzichtig',
            'Voeg citroen en munt toe aan water',
            'Laat 30 minuten trekken in koelkast',
            'Voeg ijsblokjes toe',
            'Zoet naar smaak met honing'
        ],
        tips: [
            'Gebruik biologische citroenen voor de schil',
            'Kneus muntblaadjes licht voor meer smaak',
            'Houd 24 uur in koelkast'
        ]
    }
    // Meer recepten kunnen hier toegevoegd worden
};

// Functie om recept data op te halen
function getRecipeData(recipeId) {
    return recipeDatabase[recipeId] || null;
}