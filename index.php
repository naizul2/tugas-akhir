<?php
require_once 'config.php';
require_once 'functions.php';

// --- PENGATURAN PAGINASI DAN KATEGORI ---
$newsPerPage = 5;
$currentPage =isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;

// Cek apakah ada filter kategori dari URL
$activeCategoryId = null;
$activeCategorySlug = null;
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $activeCategorySlug = $_GET['category'];
    $allCategories = getAllCategories($conn);
    // Cari ID kategori berdasarkan slug dari namanya
    foreach ($allCategories as $cat) {
        if (generateSlug($cat['name']) === $activeCategorySlug) {
            $activeCategoryId = $cat['id'];
            break;
        }
    }
}

// Hitung total berita (dengan atau tanpa filter kategori)
$totalNews = getTotalNewsCount($conn, $activeCategoryId);
$totalPages = ceil($totalNews / $newsPerPage);

if ($totalPages > 0 && $currentPage > $totalPages) {
    $currentPage = $totalPages;
}

$offset = ($currentPage - 1) * $newsPerPage;

// Ambil artikel berita (dengan atau tanpa filter kategori)
$newsArticles = getNewsArticles($conn, $newsPerPage, $offset, $activeCategoryId);
$categories = getAllCategories($conn);

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Berita Terkini</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="container">
        <h1 class="page-title">Berita Terbaru</h1>

        <?php if (!empty($categories)): ?>
        <nav class="category-filters">
            <a href="index.php" class="<?php echo ($activeCategoryId === null) ? 'active' : ''; ?>">Semua</a>
            <?php foreach ($categories as $category): ?>
                <?php $categorySlug = generateSlug($category['name']); ?>
                <a href="index.php?category=<?php echo $categorySlug; ?>" class="<?php echo ($categorySlug === $activeCategorySlug) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>

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
                    // Logika Paginasi dengan mempertahankan parameter kategori
                    $queryString = '';
                    if ($activeCategorySlug) {
                        $queryString = '&category=' . urlencode($activeCategorySlug);
                    }
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
            <p style="text-align: center; padding: 40px 0;">Tidak ada artikel berita ditemukan untuk kategori ini.</p>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>