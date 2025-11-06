function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    if (!passwordInput || !eyeIcon) return;
    
    // Check current path to determine correct image path
    const currentPath = window.location.pathname;
    const isInPages = currentPath.includes('/pages/') || currentPath.includes('pages\\');
    const imagePath = isInPages ? '../images/' : 'images/';
    
    // Toggle password visibility
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.src = imagePath + 'hidden-eye.png';
        eyeIcon.alt = 'Verberg wachtwoord';
    } else {
        passwordInput.type = 'password';
        eyeIcon.src = imagePath + 'eye.png';
        eyeIcon.alt = 'Toon wachtwoord';
    }
}

function checkStrength() {
    const passwordInput = document.getElementById('password');
    if (!passwordInput) return;
    
    const password = passwordInput.value;
    const strengthDiv = document.getElementById('password-strength');
    if (!strengthDiv) return;
    
    let strength = '';
    let color = '';

    if (password.length < 5) {
        strength = 'Zwak wachtwoord';
        color = 'red';
    } else if (password.length < 10) {
        strength = 'Matig wachtwoord';
        color = 'orange';
    } else {
        strength = 'Sterk wachtwoord';
        color = 'green';
    }

    strengthDiv.textContent = strength;
    strengthDiv.style.color = color;
}

// Registration form handler
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = '';
            messageDiv.style.color = '';
            
            const formData = {
                voornaam: document.getElementById('voornaam').value,
                achternaam: document.getElementById('achternaam').value,
                telefoon: document.getElementById('telefoon').value,
                email: document.getElementById('email').value,
                wachtwoord: document.getElementById('password').value
            };
            
            const result = await registerUser(formData);
            
            if (result.success) {
                messageDiv.textContent = 'Account succesvol aangemaakt! Je wordt doorgestuurd...';
                messageDiv.style.color = 'green';
                setTimeout(() => {
                    // Check if we're in pages folder or root
                    const currentPath = window.location.pathname;
                    if (currentPath.includes('/pages/')) {
                        window.location.href = 'inloggen.html';
                    } else {
                        window.location.href = 'pages/inloggen.html';
                    }
                }, 2000);
            } else {
                messageDiv.textContent = result.error || 'Er is een fout opgetreden';
                messageDiv.style.color = 'red';
            }
        });
    }
    
    // Login form handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = '';
            messageDiv.style.color = '';
            
            const email = document.getElementById('email').value;
            const wachtwoord = document.getElementById('password').value;
            
            const result = await loginUser(email, wachtwoord);
            
            if (result.success) {
                setUserSession(result.user);
                messageDiv.textContent = 'Succesvol ingelogd! Je wordt doorgestuurd...';
                messageDiv.style.color = 'green';
                setTimeout(() => {
                    // Check if we're in pages folder or root
                    const currentPath = window.location.pathname;
                    if (currentPath.includes('/pages/')) {
                        window.location.href = 'homepage.html';
                    } else {
                        window.location.href = 'pages/homepage.html';
                    }
                }, 1500);
            } else {
                messageDiv.textContent = result.error || 'Ongeldige inloggegevens';
                messageDiv.style.color = 'red';
            }
        });
    }
    
    // Update user display on pages
    updateUserDisplay();
});

function updateUserDisplay() {
    const user = getUserSession();
    const userDisplay = document.querySelector('.user p');
    if (userDisplay && user) {
        userDisplay.textContent = `${user.voornaam} ${user.achternaam}`;
    }
}