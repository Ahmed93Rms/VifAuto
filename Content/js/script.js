document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les éléments <h1> dans les blocs
    const headers = document.querySelectorAll('.block > h1');

            headers.forEach(header => {
                    header.addEventListener('click', function() {
                    // Trouver le formulaire frère directement après le <h1> cliqué
                    const form = this.nextElementSibling;

                    // Basculer l'affichage du formulaire
                    form.style.display = form.style.display === 'block' ? 'none' : 'block';
                    });
            });
});
