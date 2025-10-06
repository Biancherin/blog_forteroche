<?php

/**
 * Cette classe sert à gérer les commentaires.
 */
class CommentManager extends AbstractEntityManager
{
    /**
     * Récupère tous les commentaires d'un article.
     */
    public function getAllCommentsByArticleId(int $idArticle): array
    {
        $sql = "SELECT * FROM comment WHERE id_article = $idArticle ORDER BY date_creation DESC";
        $result = $this->db->query($sql);

        $comments = [];
        while ($comment = $result->fetch()) {
            $comments[] = new Comment($comment);
        }
        return $comments;
    }

    /**
     * Récupère un commentaire par son id.
     */
    public function getCommentById(int $id): ?Comment
    {
        $sql = "SELECT * FROM comment WHERE id = $id";
        $result = $this->db->query($sql);
        $comment = $result->fetch();
        return $comment ? new Comment($comment) : null;
    }

    /**
     * Ajoute un commentaire.
     */
    public function addComment(Comment $comment): bool
    {
        $pseudo = addslashes($comment->getPseudo());
        $content = addslashes($comment->getContent());
        $idArticle = (int)$comment->getIdArticle();

        $sql = "INSERT INTO comment (pseudo, content, id_article, date_creation) 
                VALUES ('$pseudo', '$content', $idArticle, NOW())";
        $result = $this->db->query($sql);
        return $result->rowCount() > 0;
    }

    /**
     * Supprime un commentaire.
     */
    public function deleteComment(Comment $comment): bool
    {
        $id = (int)$comment->getId();
        $sql = "DELETE FROM comment WHERE id = $id";
        $result = $this->db->query($sql);
        return $result->rowCount() > 0;
    }

    /**
     * Compte le nombre de commentaires pour un article.
     */
    public function countCommentsByArticleId(int $idArticle): int
    {
        $sql = "SELECT COUNT(*) AS nb FROM comment WHERE id_article = $idArticle";
        $result = $this->db->query($sql);
        $row = $result->fetch();
        return $row ? (int)$row['nb'] : 0;
    }

    /**
     * Récupère les commentaires avec pagination et filtres.
     */
    public function getCommentsByArticlePaginated(
        int $idArticle,
        int $limit,
        int $offset,
        string $pseudoFilter = "",
        string $contentFilter = "",
        string $dateFilter = ""
    ): array {
        $sql = "SELECT * FROM comment WHERE id_article = $idArticle";

        if ($pseudoFilter !== "") {
            $sql .= " AND pseudo LIKE '%" . addslashes($pseudoFilter) . "%'";
        }
        if ($contentFilter !== "") {
            $sql .= " AND content LIKE '%" . addslashes($contentFilter) . "%'";
        }
        if ($dateFilter !== "") {
            $sql .= " AND DATE(date_creation) = '" . addslashes($dateFilter) . "'";
        }

        $sql .= " ORDER BY date_creation DESC LIMIT $limit OFFSET $offset";

        $result = $this->db->query($sql);
        $comments = [];
        while ($row = $result->fetch()) {
            $comments[] = new Comment($row);
        }
        return $comments;
    }

    /**
     * Compte les commentaires avec filtres (pour pagination).
     */
    public function countCommentsByArticlePaginated(
        int $idArticle,
        string $pseudoFilter = "",
        string $contentFilter = "",
        string $dateFilter = ""
    ): int {
        $sql = "SELECT COUNT(*) AS total FROM comment WHERE id_article = $idArticle";

        if ($pseudoFilter !== "") {
            $sql .= " AND pseudo LIKE '%" . addslashes($pseudoFilter) . "%'";
        }
        if ($contentFilter !== "") {
            $sql .= " AND content LIKE '%" . addslashes($contentFilter) . "%'";
        }
        if ($dateFilter !== "") {
            $sql .= " AND DATE(date_creation) = '" . addslashes($dateFilter) . "'";
        }

        $result = $this->db->query($sql);
        $row = $result->fetch();
        return (int)($row['total'] ?? 0);
    }
}

