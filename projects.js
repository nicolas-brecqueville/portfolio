// Récupérer l'ID du projet à partir de l'URL
const params = new URLSearchParams(window.location.search);
const projectId = params.get("id"); // Exemple : ?id=1

if (!projectId) {
    console.error("Aucun ID de projet trouvé dans l'URL.");
    document.querySelector('.project-container').innerHTML = "<p>Aucun projet spécifié.</p>";
} else {
    // Charger les données des projets
    // Charger les données JSON et chercher le projet correspondant
    fetch('projects.json') // Remplacez par le chemin réel de votre fichier JSON
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur de chargement des données JSON');
            }
            return response.json();
        })
        .then(data => {
            const params = new URLSearchParams(window.location.search);
            const projectId = params.get("id");

            if (projectId) {
                // Chercher le projet correspondant dans les données
                const project = data.projects.find(p => String(p.id) === String(projectId));

                if (project) {
                    // Injecter les données dans la page
                    document.title = project.title; // Met à jour le titre de la page
                    document.querySelector('.project-title').textContent = project.title;
                    document.querySelector('.project-category').textContent = project.category;

                    // Vérifier si le client existe, sinon masquer l'élément
                    if (project.client) {
                        document.querySelector('.project-client').textContent = project.client;
                    } else {
                        document.querySelector('.project-client').parentElement.style.display = 'none';
                    }

                    document.querySelector('.project-date').textContent = new Date(project.date).toLocaleDateString();

                    // Vérifier si l'URL existe, sinon masquer l'élément
                    if (project.url) {
                        const projectUrl = document.querySelector('.project-url');
                        projectUrl.href = project.url;
                        projectUrl.textContent = project.url;
                    } else {
                        document.querySelector('.project-url').parentElement.style.display = 'none';
                    }

                    document.querySelector('.project-description').textContent = project.description;

                    // Afficher l'image
                    const projectImage = document.querySelector('.project-image');
                    if (project.image) {
                        projectImage.src = project.image;
                        projectImage.alt = `Image of ${project.title}`;
                    } else {
                        projectImage.parentElement.style.display = 'none'; // Cache l'élément si aucune image n'est définie
                    }

                    // Si aucune des informations n'est présente, cacher le div contenant les infos
                    if (!project.client && !project.url && !project.category) {
                        document.querySelector('.portfolio-info').style.display = 'none';
                    }
                } else {
                    console.error('Projet non trouvé');
                    document.querySelector('.project-container').innerHTML = "<p>Projet non trouvé.</p>";
                }
            } else {
                console.error('Aucun ID de projet fourni dans l\'URL');
                document.querySelector('.project-container').innerHTML = "<p>Aucun ID de projet fourni.</p>";
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des projets :', error);
            document.querySelector('.project-container').innerHTML = "<p>Erreur lors du chargement des projets.</p>";
        });
}