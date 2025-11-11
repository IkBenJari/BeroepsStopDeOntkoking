// Detail pagina functionaliteit
let currentRecipe = null;
let currentServings = 2;
let originalServings = 2;
let userRating = 0;

// DOM geladen
document.addEventListener('DOMContentLoaded', function() {
    loadRecipeDetail();
    loadUserData();
});

// Laad recept details
function loadRecipeDetail() {
    const recipeId = localStorage.getItem('selectedRecipe');
    if (!recipeId) {
        window.location.href = 'homepage.html';
        return;
    }

    // Haal recept data op uit recepten.js
    currentRecipe = getRecipeData(recipeId);
    if (!currentRecipe) {
        alert('Recept niet gevonden!');
        window.location.href = 'homepage.html';
        return;
    }

    displayRecipe();
}

// Toon recept details
function displayRecipe() {
    document.getElementById('recipeTitle').textContent = currentRecipe.title;
    document.getElementById('recipeImage').src = currentRecipe.image;
    document.getElementById('recipeTime').textContent = `⏱️ ${currentRecipe.time}`;
    document.getElementById('recipeServings').textContent = `👥 ${currentRecipe.servings}`;
    document.getElementById('recipeRating').textContent = `⭐ ${currentRecipe.rating}`;
    document.getElementById('recipeDifficulty').textContent = `📊 ${currentRecipe.difficulty}`;

    // Parse servings voor calculator
    originalServings = parseInt(currentRecipe.servings.match(/\d+/)[0]);
    currentServings = originalServings;
    document.getElementById('currentServings').textContent = currentServings;

    displayIngredients();
    displayInstructions();
    displayTips();
    loadReviews();
}

// Toon ingrediënten
function displayIngredients() {
    const ingredientsList = document.getElementById('ingredientsList');
    ingredientsList.innerHTML = '';
    
    currentRecipe.ingredients.forEach(ingredient => {
        const li = document.createElement('li');
        li.textContent = adjustIngredientAmount(ingredient);
        ingredientsList.appendChild(li);
    });
}

// Pas ingrediënt hoeveelheden aan
function adjustIngredientAmount(ingredient) {
    const ratio = currentServings / originalServings;
    return ingredient.replace(/\d+/g, function(match) {
        const newAmount = Math.round(parseInt(match) * ratio * 10) / 10;
        return newAmount % 1 === 0 ? newAmount.toString() : newAmount.toFixed(1);
    });
}

// Toon instructies
function displayInstructions() {
    const instructionsList = document.getElementById('instructionsList');
    instructionsList.innerHTML = '';
    
    currentRecipe.instructions.forEach(instruction => {
        const li = document.createElement('li');
        li.textContent = instruction;
        instructionsList.appendChild(li);
    });
}

// Toon tips
function displayTips() {
    const tipsList = document.getElementById('tipsList');
    tipsList.innerHTML = '';
    
    currentRecipe.tips.forEach(tip => {
        const li = document.createElement('li');
        li.textContent = tip;
        tipsList.appendChild(li);
    });
}

// Pas aantal porties aan
function adjustServings(change) {
    const newServings = currentServings + change;
    if (newServings > 0 && newServings <= 20) {
        currentServings = newServings;
        document.getElementById('currentServings').textContent = currentServings;
        displayIngredients();
    }
}

// Toggle favoriet
function toggleFavorite() {
    const btn = document.querySelector('.favorite-btn');
    let favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    const recipeId = localStorage.getItem('selectedRecipe');
    
    if (favorites.includes(recipeId)) {
        favorites = favorites.filter(id => id !== recipeId);
        btn.textContent = '🤍 Favoriet';
        btn.style.background = 'rgba(255, 255, 255, 0.2)';
    } else {
        favorites.push(recipeId);
        btn.textContent = '❤️ Favoriet';
        btn.style.background = '#e74c3c';
    }
    
    localStorage.setItem('favorites', JSON.stringify(favorites));
}

// Deel recept
function shareRecipe() {
    if (navigator.share) {
        navigator.share({
            title: currentRecipe.title,
            text: `Bekijk dit heerlijke recept: ${currentRecipe.title}`,
            url: window.location.href
        });
    } else {
        // Fallback: kopieer link naar clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link gekopieerd naar clipboard!');
        });
    }
}

// Print recept
function printRecipe() {
    window.print();
}

// Zet rating
function setRating(rating) {
    userRating = rating;
    const stars = document.querySelectorAll('.star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

// Voeg review toe
function submitReview() {
    const reviewText = document.getElementById('reviewText').value.trim();
    const user = JSON.parse(localStorage.getItem('currentUser') || '{}');
    
    if (!reviewText) {
        alert('Voer een review tekst in!');
        return;
    }
    
    if (userRating === 0) {
        alert('Geef een rating!');
        return;
    }
    
    const review = {
        id: Date.now(),
        user: user.voornaam || 'Anoniem',
        rating: userRating,
        text: reviewText,
        date: new Date().toLocaleDateString('nl-NL')
    };
    
    // Sla review op
    const recipeId = localStorage.getItem('selectedRecipe');
    let reviews = JSON.parse(localStorage.getItem(`reviews_${recipeId}`) || '[]');
    reviews.unshift(review);
    localStorage.setItem(`reviews_${recipeId}`, JSON.stringify(reviews));
    
    // Reset form
    document.getElementById('reviewText').value = '';
    setRating(0);
    
    // Herlaad reviews
    loadReviews();
    
    alert('Review toegevoegd!');
}

// Laad reviews
function loadReviews() {
    const recipeId = localStorage.getItem('selectedRecipe');
    const reviews = JSON.parse(localStorage.getItem(`reviews_${recipeId}`) || '[]');
    const reviewsList = document.getElementById('reviewsList');
    
    reviewsList.innerHTML = '';
    
    if (reviews.length === 0) {
        reviewsList.innerHTML = '<p style="text-align: center; color: #666; margin-top: 2rem;">Nog geen reviews. Wees de eerste!</p>';
        return;
    }
    
    reviews.forEach(review => {
        const reviewDiv = document.createElement('div');
        reviewDiv.className = 'review-item';
        reviewDiv.innerHTML = `
            <div class="review-header">
                <strong>${review.user}</strong>
                <span class="review-rating">${'⭐'.repeat(review.rating)}</span>
                <span class="review-date">${review.date}</span>
            </div>
            <p class="review-text">${review.text}</p>
        `;
        reviewsList.appendChild(reviewDiv);
    });
}

// Laad gebruiker data
function loadUserData() {
    const savedUser = localStorage.getItem('currentUser');
    if (savedUser) {
        const user = JSON.parse(savedUser);
        document.getElementById('username').textContent = user.voornaam || 'Gast';
    }
    
    // Check favorites
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    const recipeId = localStorage.getItem('selectedRecipe');
    const btn = document.querySelector('.favorite-btn');
    
    if (favorites.includes(recipeId)) {
        btn.textContent = '❤️ Favoriet';
        btn.style.background = '#e74c3c';
    }
}

// CSS voor reviews (dynamisch toegevoegd)
const reviewStyles = `
    .review-item {
        background: #f8f9ff;
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 1rem;
        border-left: 4px solid #667eea;
    }
    
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        flex-wrap: wrap;
    }
    
    .review-rating {
        font-size: 1.2rem;
    }
    
    .review-date {
        color: #666;
        font-size: 0.9rem;
    }
    
    .review-text {
        line-height: 1.5;
        color: #333;
    }
`;

// Voeg styles toe
const styleSheet = document.createElement('style');
styleSheet.textContent = reviewStyles;
document.head.appendChild(styleSheet);