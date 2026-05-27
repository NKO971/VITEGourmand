document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('connexion-form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    form.addEventListener('submit', (event) => {
         
        console.log("Tentative de connexion pour :", emailInput.value);
        
        // On peut juste faire une petite vérification visuelle avant l'envoi
        if (emailInput.value === "" || passwordInput.value === "") {
            event.preventDefault(); // On bloque seulement si c'est vide
            alert("Merci de remplir tous les champs.");
        }
    });
});