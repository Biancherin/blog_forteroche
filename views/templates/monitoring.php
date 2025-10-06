<?php
/**
 * Affichage de la page Monitoring : tableau des articles avec vues, commentaires et date.
 */
?>

<h2>Monitoring des articles</h2>

<table class="monitoringTable">
    <thead>
        <tr>
            <th>Titre</th>
            <th>Nombre de vues</th>
            <th>Nombre de commentaires</th>
            <th>Date de publication</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $article) { ?>
            <tr>
                <td><?= Utils::format($article->getTitle()) ?></td>
                <td><?= $article->getNbViews() ?></td>
                <td><?= $article->getNbComments() ?></td>
                <td><?= Utils::convertDateToFrenchFormat($article->getDateCreation()) ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
