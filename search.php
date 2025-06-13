<?php
require_once 'config.php';
require_once 'functions.php';

$searchQuery = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $searchQuery = trim($_GET['q']);
} else {
    // Redirect ke halaman utama jika query kosong
    header("Location: index.php");
    exit();
}

// Pengaturan Paginasi untuk hasil pencarian
$newsPerPage = 5;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;

$totalNews = getTotalSearchCount($conn, $searchQuery);
$totalPages = ceil($totalNews / $newsPerPage);

if ($totalPages > 0 && $currentPage > $totalPages) {
    $currentPage = $totalPages;
}

$offset = ($currentPage - 1) * $newsPerPage;
$newsArticles = searchNewsArticles($conn, $searchQuery, $newsPerPage, $offset);

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian untuk "<?php echo htmlspecialchars($searchQuery); ?>"</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="container">
        <h1 class="page-title">Hasil Pencarian</h1>
        <p class="search-results-info">Menampilkan hasil untuk: "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"</p>

        <?php if (!empty($newsArticles)): ?>
            <section class="news-list">
                <?php foreach ($newsArticles as $news): ?>
                    <a href="article.php?slug=<?php echo htmlspecialchars($news['slug']); ?>" class="news-item">
                        <?php if ($news['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($news['image_url']); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" class="news-thumbnail">
                        <?php endif; ?>
                        <div class="news-content">
                            <p class="news-meta">
                                <span class="category"><?php echo htmlspecialchars($news['category_name']); ?></span>
                            </p>
                            <h2><?php echo htmlspecialchars($news['title']); ?></h2>
                            <p><?php echo htmlspecialchars($news['snippet']); ?>...</p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </section>

            <?php if ($totalPages > 1): ?>
            <nav class="pagination">
                 <?php
                    // Logika Paginasi dengan mempertahankan query pencarian
                    $queryString = '&q=' . urlencode($searchQuery);
                ?>
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?php echo $currentPage - 1; ?><?php echo $queryString; ?>" class="page-link">Sebelumnya</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo $queryString; ?>" class="page-link <?php echo ($i == $currentPage) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?php echo $currentPage + 1; ?><?php echo $queryString; ?>" class="page-link">Berikutnya</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>
        <?php else: ?>
            <p style="text-align: center; padding: 40px 0;">Tidak ada artikel berita yang cocok dengan pencarian Anda.</p>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>