<?php
/**
 * Affichage de la page Monitoring : tableau des articles avec vues, commentaires et date.
 * Les colonnes sont triables en cliquant sur les entêtes.
 */

// Fonction utilitaire pour créer les liens de tri
function sortLink(string $label, string $field, string $currentSort, string $currentOrder) : string {
    // Détermine l'ordre à appliquer si on clique à nouveau
    $nextOrder = ($currentSort === $field && $currentOrder === 'asc') ? 'desc' : 'asc';

    // Ajoute une flèche visuelle ↑ ou ↓ si la colonne est active
    $arrow = '';
    if ($currentSort === $field) {
        $arrow = $currentOrder === 'asc' ? ' ↑' : ' ↓';
    }

    return "<a href='index.php?action=showMonitoring&sort=$field&order=$nextOrder'>$label$arrow</a>";
}
?>

<h2>Monitoring des articles</h2>

<table class="monitoringTable">
    <thead>
        <tr>
            <th><?= sortLink("Titre", "title", $sort, $order) ?></th>
            <th><?= sortLink("Nombre de vues", "nb_views", $sort, $order) ?></th>
            <th><?= sortLink("Nombre de commentaires", "nb_comments", $sort, $order) ?></th>
            <th><?= sortLink("Date de publication", "date_creation", $sort, $order) ?></th>
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
