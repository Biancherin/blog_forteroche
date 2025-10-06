<?php 
/**
 * Contrôleur de la partie admin.
 */
class AdminController {

    /**
     * Affiche la page d'administration.
     * @return void
     */
    public function showAdmin(): void {
        $this->checkIfUserIsConnected();

        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        $view = new View("Administration");
        $view->render("admin", [
            'articles' => $articles
        ]);
    }

    /**
     * Affiche la page Monitoring : liste des articles avec vues, commentaires et date.
     * Gère aussi le tri en PHP.
     * @return void
     */
    public function showMonitoring(): void {
        $this->checkIfUserIsConnected();

        // Récupération des paramètres de tri envoyés par l’URL
        $sort = Utils::request('sort', 'title');   // colonne de tri par défaut
        $order = Utils::request('order', 'asc');   // ordre de tri par défaut

        // Récupération des articles
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticlesForMonitoring();

        // Tri des articles en PHP
        usort($articles, function($a, $b) use ($sort, $order) {
            switch ($sort) {
                case 'nb_views':
                    $valA = $a->getNbViews();
                    $valB = $b->getNbViews();
                    break;
                case 'nb_comments':
                    $valA = $a->getNbComments();
                    $valB = $b->getNbComments();
                    break;
                case 'date_creation':
                    $valA = $a->getDateCreation()->getTimestamp();
                    $valB = $b->getDateCreation()->getTimestamp();
                    break;
                case 'title':
                default:
                    $valA = strtolower($a->getTitle());
                    $valB = strtolower($b->getTitle());
                    break;
            }

            if ($valA == $valB) return 0;
            if ($order === 'asc') {
                return ($valA < $valB) ? -1 : 1;
            } else {
                return ($valA > $valB) ? -1 : 1;
            }
        });

        // Rendu de la vue
        $view = new View("Monitoring");
        $view->render("monitoring", [
            'articles' => $articles,
            'sort' => $sort,
            'order' => $order
        ]);
    }

    /**
     * Vérifie que l'utilisateur est connecté.
     * @return void
     */
    private function checkIfUserIsConnected(): void {
        if (!isset($_SESSION['user'])) {
            Utils::redirect("connectionForm");
        }
    }

    /**
     * Affichage du formulaire de connexion.
     */
    public function displayConnectionForm(): void {
        $view = new View("Connexion");
        $view->render("connectionForm");
    }

    /**
     * Connexion de l'utilisateur.
     */
    public function connectUser(): void {
        $login = Utils::request("login");
        $password = Utils::request("password");

        if (empty($login) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires.");
        }

        $userManager = new UserManager();
        $user = $userManager->getUserByLogin($login);

        if (!$user) {
            throw new Exception("L'utilisateur demandé n'existe pas.");
        }

        if (!password_verify($password, $user->getPassword())) {
            throw new Exception("Le mot de passe est incorrect.");
        }

        $_SESSION['user'] = $user;
        $_SESSION['idUser'] = $user->getId();

        Utils::redirect("admin");
    }

    /**
     * Déconnexion de l'utilisateur.
     */
    public function disconnectUser(): void {
        unset($_SESSION['user']);
        Utils::redirect("home");
    }

    /**
     * Affichage du formulaire d'ajout ou modification d'un article.
     */
    public function showUpdateArticleForm(): void {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);

        if (!$article) {
            $article = new Article();
        }

        $view = new View("Edition d'un article");
        $view->render("updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Ajout ou mise à jour d'un article.
     */
    public function updateArticle(): void {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);
        $title = Utils::request("title");
        $content = Utils::request("content");

        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires.");
        }

        $article = new Article([
            'id' => $id,
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        Utils::redirect("admin");
    }

    /**
     * Suppression d'un article.
     */
    public function deleteArticle(): void {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);

        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);

        Utils::redirect("admin");
    }
}
