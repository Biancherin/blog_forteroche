<h2>Edition des articles</h2>

<div class="adminArticle">
    <?php foreach ($articles as $article) { ?>
        <div class="articleLine">
            <div class="title"><?= htmlspecialchars($article->getTitle()) ?></div>
            <div class="content"><?= htmlspecialchars($article->getContent(200)) ?></div>
            <div>
                <a class="submit" href="index.php?action=showUpdateArticleForm&id=<?= $article->getId() ?>">
                    Modifier
                </a>
            </div>
            <div>
                <a class="submit" href="index.php?action=deleteArticle&id=<?= $article->getId() ?>"
                   <?= Utils::askConfirmation("Êtes-vous sûr de vouloir supprimer cet article ?") ?>>
                    Supprimer
                </a>
            </div>
            <div>
                <!-- Colonne commentaires : nombre de commentaires et lien vers la page adminComments -->
                <a class="submit" href="index.php?action=showCommentsAdmin&idArticle=<?= $article->getId() ?>">
                    Commentaires (<?= $article->getNbComments() ?>)
                </a>
            </div>
        </div>
    <?php } ?>
</div>

<a class="submit" href="index.php?action=showUpdateArticleForm">Ajouter un article</a>
