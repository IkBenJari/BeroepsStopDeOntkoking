// API functions that work with localStorage (no server needed - FileZilla compatible)

// Session management
function setUserSession(user) {
    localStorage.setItem('currentUser', JSON.stringify(user));
}

function getUserSession() {
    const userStr = localStorage.getItem('currentUser');
    return userStr ? JSON.parse(userStr) : null;
}

function clearUserSession() {
    localStorage.removeItem('currentUser');
}

function isAdmin() {
    const user = getUserSession();
    return user && user.is_admin === 1;
}

// User functions
async function registerUser(userData) {
    try {
        const existingUser = storage.getUserByEmail(userData.email);
        if (existingUser) {
            return { error: 'Dit e-mailadres is al geregistreerd' };
        }

        const newUser = storage.addUser({
            ...userData,
            wachtwoord: userData.wachtwoord,
            is_admin: 0
        });

        const { wachtwoord, ...userWithoutPassword } = newUser;
        return {
            success: true,
            message: 'Account succesvol aangemaakt',
            userId: newUser.id
        };
    } catch (error) {
        return { error: 'Fout bij registratie' };
    }
}

async function loginUser(email, wachtwoord) {
    try {
        const user = storage.getUserByEmail(email);
        
        if (!user) {
            return { error: 'Account bestaat niet' };
        }

        if (user.wachtwoord !== wachtwoord) {
            return { error: 'Wachtwoord is onjuist' };
        }

        const { wachtwoord: _, ...userWithoutPassword } = user;
        return {
            success: true,
            user: userWithoutPassword
        };
    } catch (error) {
        return { error: 'Fout bij inloggen' };
    }
}

// Recipe functions
async function getRecepten(filters = {}) {
    try {
        return storage.searchRecepten(filters);
    } catch (error) {
        return { error: 'Fout bij ophalen recepten' };
    }
}

async function getRecept(id) {
    try {
        const recept = storage.getReceptById(id);
        if (!recept) {
            return { error: 'Recept niet gevonden' };
        }
        return recept;
    } catch (error) {
        return { error: 'Fout bij ophalen recept' };
    }
}

async function createRecept(receptData) {
    try {
        const user = getUserSession();
        if (!user) {
            return { error: 'Je moet ingelogd zijn om recepten toe te voegen' };
        }

        const newRecept = storage.addRecept({
            ...receptData,
            gebruiker_id: user.id
        });

        return {
            success: true,
            message: 'Recept succesvol aangemaakt',
            recipeId: newRecept.id
        };
    } catch (error) {
        return { error: 'Fout bij aanmaken recept' };
    }
}

async function updateRecept(id, receptData) {
    try {
        const user = getUserSession();
        if (!user) {
            return { error: 'Je moet ingelogd zijn' };
        }

        const recept = storage.getReceptById(id);
        if (!recept) {
            return { error: 'Recept niet gevonden' };
        }

        if (recept.gebruiker_id !== user.id && !isAdmin()) {
            return { error: 'Je hebt geen toestemming om dit recept te bewerken' };
        }

        storage.updateRecept(id, receptData);
        return {
            success: true,
            message: 'Recept bijgewerkt'
        };
    } catch (error) {
        return { error: 'Fout bij bijwerken recept' };
    }
}

async function deleteRecept(id) {
    try {
        const user = getUserSession();
        if (!user) {
            return { error: 'Je moet ingelogd zijn' };
        }

        const recept = storage.getReceptById(id);
        if (!recept) {
            return { error: 'Recept niet gevonden' };
        }

        if (recept.gebruiker_id !== user.id && !isAdmin()) {
            return { error: 'Je hebt geen toestemming om dit recept te verwijderen' };
        }

        storage.deleteRecept(id);
        return {
            success: true,
            message: 'Recept verwijderd'
        };
    } catch (error) {
        return { error: 'Fout bij verwijderen recept' };
    }
}

// Admin functions
async function getAllUsers() {
    try {
        const users = storage.getUsers();
        return users.map(({ wachtwoord, ...user }) => user);
    } catch (error) {
        return { error: 'Fout bij ophalen gebruikers' };
    }
}

async function updateUser(id, userData) {
    try {
        storage.updateUser(id, userData);
        return {
            success: true,
            message: 'Gebruiker bijgewerkt'
        };
    } catch (error) {
        return { error: 'Fout bij bijwerken gebruiker' };
    }
}

async function deleteUser(id) {
    try {
        storage.deleteUser(id);
        return {
            success: true,
            message: 'Gebruiker verwijderd'
        };
    } catch (error) {
        return { error: 'Fout bij verwijderen gebruiker' };
    }
}

// Review functions
async function createRecensie(recensieData) {
    try {
        const user = getUserSession();
        if (!user) {
            return { error: 'Je moet ingelogd zijn om een recensie te schrijven' };
        }

        const newRecensie = storage.addRecensie({
            ...recensieData,
            gebruiker_id: user.id
        });

        return {
            success: true,
            message: 'Recensie succesvol geplaatst',
            recensieId: newRecensie.id
        };
    } catch (error) {
        return { error: 'Fout bij plaatsen recensie' };
    }
}

async function getRecensiesByRecept(receptId) {
    try {
        return storage.getRecensiesByReceptId(receptId);
    } catch (error) {
        return { error: 'Fout bij ophalen recensies' };
    }
}

async function deleteRecensie(id) {
    try {
        const user = getUserSession();
        if (!user) {
            return { error: 'Je moet ingelogd zijn' };
        }

        storage.deleteRecensie(id);
        return {
            success: true,
            message: 'Recensie verwijderd'
        };
    } catch (error) {
        return { error: 'Fout bij verwijderen recensie' };
    }
}

async function getAllRecensies() {
    try {
        const recensies = storage.getRecensies();
        return recensies;
    } catch (error) {
        return { error: 'Fout bij ophalen recensies' };
    }
}

