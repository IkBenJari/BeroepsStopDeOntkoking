function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.src = '../images/hidden-eye.png'; 
    } else {
        passwordInput.type = 'password';
        eyeIcon.src = '../images/eye.png';
    }
}

function checkStrength() {
    const password = document.getElementById('password').value;
    const strengthDiv = document.getElementById('password-strength');
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