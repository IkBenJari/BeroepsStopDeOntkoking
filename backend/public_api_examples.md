# Hoe gebruikers content toevoegen / bewerken (voorbeeld endpoints)


1) Recept toevoegen (POST /backend/recipes_create.php)
- POST: title, ingredients, steps, description, image (file)
- Vereist: ingelogde gebruiker


2) Recept bewerken (POST /backend/recipes_update.php?id=...)
3) Recept verwijderen (GET/POST /backend/recipes_delete.php?id=...)
4) Tips en ervaringen: vergelijkbare endpoints