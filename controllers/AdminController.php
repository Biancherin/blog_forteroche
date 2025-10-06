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
        $articles = $articleManager->getAllArticlesForAdmin(); // nouvelle méthode

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
    /**
    * Affiche les commentaires d'un article avec filtres et pagination.
    */
    public function showCommentsAdmin(): void
    {
        $this->checkIfUserIsConnected();

        $idArticle = Utils::request('idArticle', -1);
        $page = max(1, Utils::request('page', 1));
        $limit = 10; // nombre de commentaires par page
        $offset = ($page - 1) * $limit;

        // filtres
        $filterPseudo = Utils::request('pseudo', '');
        $filterContent = Utils::request('content', '');
        $filterDate = Utils::request('date_creation', ''); // optionnel

        $commentManager = new CommentManager();
        $totalComments = $commentManager->countCommentsByArticlePaginated($idArticle, $filterPseudo, $filterContent, $filterDate);
        $comments = $commentManager->getCommentsByArticlePaginated($idArticle, $limit, $offset, $filterPseudo, $filterContent, $filterDate);

        $totalPages = ceil($totalComments / $limit);

        $view = new View("Commentaires de l'article");
        $view->render("adminComment", [
            'comments' => $comments,
            'idArticle' => $idArticle,
            'page' => $page,
            'totalPages' => $totalPages,
            'filterPseudo' => $filterPseudo,
            'filterContent' => $filterContent,
            'filterDate' => $filterDate
        ]);
    }

    /**
    * Supprime un commentaire depuis l'interface admin.
    */
    public function deleteComment(): void
    {
        $this->checkIfUserIsConnected();
        $idComment = Utils::request("id", -1);

        $commentManager = new CommentManager();
        $comment = $commentManager->getCommentById($idComment);
        if ($comment) {
            $commentManager->deleteComment($comment);
            Utils::redirect("showCommentsAdmin&idArticle=" . $comment->getIdArticle());
        } else {
            throw new Exception("Le commentaire n'existe pas.");
        }
    }

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
