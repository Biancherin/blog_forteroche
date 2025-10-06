<h2>Commentaires de l'article <?= $idArticle ?></h2>

<form method="get" action="index.php">
    <input type="hidden" name="action" value="showCommentsAdmin">
    <input type="hidden" name="idArticle" value="<?= $idArticle ?>">
    <input type="text" name="pseudo" placeholder="Filtrer par pseudo" value="<?= htmlspecialchars($filterPseudo) ?>">
    <input type="text" name="content" placeholder="Filtrer par contenu" value="<?= htmlspecialchars($filterContent) ?>">
    <input type="date" name="date_creation" value="<?= htmlspecialchars($filterDate) ?>">
    <button type="submit" class="submit">Filtrer</button>
</form>

<table class="monitoringTable">
    <thead>
        <tr>
            <th>Pseudo</th>
            <th>Contenu</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($comments as $comment) { ?>
            <tr>
                <td><?= htmlspecialchars($comment->getPseudo()) ?></td>
                <td><?= htmlspecialchars($comment->getContent()) ?></td>
                <td><?= $comment->getDateCreation()->format('Y-m-d H:i') ?></td>
                <td>
                    <a class="submit" href="index.php?action=deleteComment&id=<?= $comment->getId() ?>"
                       <?= Utils::askConfirmation("Supprimer ce commentaire ?") ?>>Supprimer</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a href="index.php?action=showCommentsAdmin&idArticle=<?= $idArticle ?>&page=<?= $p ?>&pseudo=<?= urlencode($filterPseudo) ?>&content=<?= urlencode($filterContent) ?>&date_creation=<?= $filterDate ?>"><?= $p ?></a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
