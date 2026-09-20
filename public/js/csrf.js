// Lit le jeton CSRF déposé dans le <head> par header.php / header_back.php.
// À utiliser pour toute requête fetch() qui envoie un corps JSON (le champ
// caché des formulaires classiques suffit pour les envois FormData/POST natifs).
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : '';
}
