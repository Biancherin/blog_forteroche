<?php

/**
 * Classe qui gère les articles.
 */
class ArticleManager extends AbstractEntityManager 
{
    public function getAllArticles() : array
    {
        $sql = "SELECT * FROM article";
        $result = $this->db->query($sql);
        $articles = [];

        while ($article = $result->fetch()) {
            $articles[] = new Article($article);
        }
        return $articles;
    }
    
    public function getArticleById(int $id) : ?Article
    {
        $sql = "SELECT * FROM article WHERE id = :id";
        $result = $this->db->query($sql, ['id' => $id]);
        $article = $result->fetch();
        if ($article) {
            return new Article($article);
        }
        return null;
    }

    public function incrementViews(int $id) : void
    {
        $sql = "UPDATE article SET nb_views = nb_views + 1 WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }

    public function addOrUpdateArticle(Article $article) : void 
    {
        if ($article->getId() == -1) {
            $this->addArticle($article);
        } else {
            $this->updateArticle($article);
        }
    }

    public function addArticle(Article $article) : void
    {
        $sql = "INSERT INTO article (id_user, title, content, date_creation, nb_views) 
                VALUES (:id_user, :title, :content, NOW(), 0)";
        $this->db->query($sql, [
            'id_user' => $article->getIdUser(),
            'title' => $article->getTitle(),
            'content' => $article->getContent()
        ]);
    }

    public function updateArticle(Article $article) : void
    {
        $sql = "UPDATE article 
                SET title = :title, content = :content, date_update = NOW() 
                WHERE id = :id";
        $this->db->query($sql, [
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'id' => $article->getId()
        ]);
    }

    public function deleteArticle(int $id) : void
    {
        $sql = "DELETE FROM article WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }

    /**
     * Méthode spéciale pour le Monitoring :
     * Récupère tous les articles avec leur nombre de commentaires.
     * Pas de tri SQL ici : c’est le contrôleur qui trie.
     */
    public function getAllArticlesForMonitoring(): array
    {
        $sql = "SELECT a.*, 
                   (SELECT COUNT(*) FROM comment c WHERE c.id_article = a.id) AS nb_comments
                FROM article a";

        $result = $this->db->query($sql);
        $articles = [];

        while ($article = $result->fetch()) {
            $articleObj = new Article($article);
            $articleObj->setNbComments($article['nb_comments']);
            $articles[] = $articleObj;
        }

        return $articles;
    }
    public function getAllArticlesForAdmin(): array
{
    $sql = "SELECT a.*, 
               (SELECT COUNT(*) FROM comment c WHERE c.id_article = a.id) AS nb_comments
            FROM article a";

    $result = $this->db->query($sql);
    $articles = [];

    while ($article = $result->fetch()) {
        $articleObj = new Article($article);
        $articleObj->setNbComments($article['nb_comments']);
        $articles[] = $articleObj;
    }

    return $articles;
}
}
