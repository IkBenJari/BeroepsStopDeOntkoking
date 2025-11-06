// Client-side storage using localStorage
// This allows the app to work without a server (FileZilla compatible)

class LocalStorage {
    constructor() {
        this.init();
    }

    init() {
        // Initialize with admin user if not exists
        // Remove old admin if exists
        let users = this.getUsers();
        const oldAdmin = users.find(u => u.email === '101939@glr.nl');
        if (oldAdmin) {
            users = users.filter(u => u.id !== oldAdmin.id);
            this.saveUsers(users);
        }
        
        // Create new admin if not exists
        if (!users.find(u => u.email === 'admin@glr.nl')) {
            const adminUser = {
                id: 1,
                voornaam: 'Admin',
                achternaam: 'User',
                telefoon: '0000000000',
                email: 'admin@glr.nl',
                wachtwoord: '1234',
                is_admin: 1,
                created_at: new Date().toISOString()
            };
            this.addUser(adminUser);
        }
    }

    // Users
    getUsers() {
        const users = localStorage.getItem('users');
        return users ? JSON.parse(users) : [];
    }

    saveUsers(users) {
        localStorage.setItem('users', JSON.stringify(users));
    }

    addUser(user) {
        const users = this.getUsers();
        user.id = users.length > 0 ? Math.max(...users.map(u => u.id)) + 1 : 1;
        user.created_at = new Date().toISOString();
        users.push(user);
        this.saveUsers(users);
        return user;
    }

    getUserByEmail(email) {
        return this.getUsers().find(u => u.email === email);
    }

    getUserById(id) {
        return this.getUsers().find(u => u.id === parseInt(id));
    }

    updateUser(id, updates) {
        const users = this.getUsers();
        const index = users.findIndex(u => u.id === parseInt(id));
        if (index !== -1) {
            users[index] = { ...users[index], ...updates };
            this.saveUsers(users);
            return users[index];
        }
        return null;
    }

    deleteUser(id) {
        const users = this.getUsers();
        const filtered = users.filter(u => u.id !== parseInt(id));
        this.saveUsers(filtered);
    }

    // Recipes
    getRecepten() {
        const recepten = localStorage.getItem('recepten');
        return recepten ? JSON.parse(recepten) : [];
    }

    saveRecepten(recepten) {
        localStorage.setItem('recepten', JSON.stringify(recepten));
    }

    addRecept(recept) {
        const recepten = this.getRecepten();
        recept.id = recepten.length > 0 ? Math.max(...recepten.map(r => r.id)) + 1 : 1;
        recept.created_at = new Date().toISOString();
        recept.updated_at = new Date().toISOString();
        recepten.push(recept);
        this.saveRecepten(recepten);
        return recept;
    }

    getReceptById(id) {
        const recept = this.getRecepten().find(r => r.id === parseInt(id));
        if (recept) {
            const user = this.getUserById(recept.gebruiker_id);
            if (user) {
                return {
                    ...recept,
                    voornaam: user.voornaam || '',
                    achternaam: user.achternaam || ''
                };
            } else {
                // If user not found, return recept without user info
                return {
                    ...recept,
                    voornaam: '',
                    achternaam: ''
                };
            }
        }
        return null;
    }

    updateRecept(id, updates) {
        const recepten = this.getRecepten();
        const index = recepten.findIndex(r => r.id === parseInt(id));
        if (index !== -1) {
            recepten[index] = { ...recepten[index], ...updates, updated_at: new Date().toISOString() };
            this.saveRecepten(recepten);
            return recepten[index];
        }
        return null;
    }

    deleteRecept(id) {
        const recepten = this.getRecepten();
        const filtered = recepten.filter(r => r.id !== parseInt(id));
        this.saveRecepten(filtered);
    }

    searchRecepten(filters = {}) {
        let recepten = this.getRecepten();
        
        // Add user info
        recepten = recepten.map(recept => {
            const user = this.getUserById(recept.gebruiker_id);
            return {
                ...recept,
                voornaam: user?.voornaam || '',
                achternaam: user?.achternaam || ''
            };
        });

        // Filter by category
        if (filters.categorie) {
            recepten = recepten.filter(r => r.categorie === filters.categorie);
        }

        // Search term
        if (filters.zoekterm) {
            const term = filters.zoekterm.toLowerCase();
            recepten = recepten.filter(r => 
                r.titel.toLowerCase().includes(term) ||
                r.beschrijving?.toLowerCase().includes(term) ||
                r.ingrediënten.toLowerCase().includes(term)
            );
        }

        // Sort by date
        recepten.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

        return recepten;
    }

    // Reviews
    getRecensies() {
        const recensies = localStorage.getItem('recensies');
        return recensies ? JSON.parse(recensies) : [];
    }

    saveRecensies(recensies) {
        localStorage.setItem('recensies', JSON.stringify(recensies));
    }

    addRecensie(recensie) {
        const recensies = this.getRecensies();
        recensie.id = recensies.length > 0 ? Math.max(...recensies.map(r => r.id)) + 1 : 1;
        recensie.created_at = new Date().toISOString();
        recensies.push(recensie);
        this.saveRecensies(recensies);
        return recensie;
    }

    getRecensiesByReceptId(receptId) {
        const recensies = this.getRecensies();
        return recensies
            .filter(r => r.recept_id === parseInt(receptId))
            .map(recensie => {
                const user = this.getUserById(recensie.gebruiker_id);
                return {
                    ...recensie,
                    voornaam: user?.voornaam || '',
                    achternaam: user?.achternaam || ''
                };
            })
            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    }

    deleteRecensie(id) {
        const recensies = this.getRecensies();
        const filtered = recensies.filter(r => r.id !== parseInt(id));
        this.saveRecensies(filtered);
    }
}

// Global storage instance
const storage = new LocalStorage();

