<?php
// article.php - Halaman untuk menampilkan satu artikel berita lengkap
require_once 'config.php';
require_once 'functions.php';

$newsArticle = null;
// Periksa apakah parameter 'slug' ada di URL
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    $newsArticle = getNewsBySlug($conn, $slug);
}

// Tutup koneksi database setelah data diambil
$conn->close();

// Jika artikel tidak ditemukan, tampilkan halaman 404
if (!$newsArticle) {
    header("HTTP/1.0 404 Not Found");
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>404 Not Found</title><link rel="stylesheet" href="css/style.css"></head><body>';
    include 'includes/header.php';
    echo '<main class="container" style="text-align: center; padding: 50px;"><h1>404 Not Found</h1><p>Artikel berita yang Anda cari tidak ditemukan.</p><p><a href="index.php" style="color: #0066cc;">Kembali ke Halaman Utama</a></p></main>';
    include 'includes/footer.php';
    echo '</body></html>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($newsArticle['title']); ?> - Situs Berita</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <article class="article-container">
            <header class="article-header">
                <p class="category"><?php echo htmlspecialchars($newsArticle['category_name']); ?></p>
                <h1><?php echo htmlspecialchars($newsArticle['title']); ?></h1>
                <p class="news-meta">
                    Diterbitkan pada <time datetime="<?php echo $newsArticle['created_at']; ?>"><?php echo date('d F Y', strtotime($newsArticle['created_at'])); ?></time>
                </p>
            </header>
            
            <?php if ($newsArticle['image_url']): ?>
                <img src="<?php echo htmlspecialchars($newsArticle['image_url']); ?>" alt="<?php echo htmlspecialchars($newsArticle['title']); ?>" class="article-image">
            <?php endif; ?>

            <div class="article-content">
                <?php 
                $content = htmlspecialchars($newsArticle['content']);
                // Ganti baris baru ganda dengan tag paragraf
                $paragraphs = preg_split('/(\r\n|\n|\r){2,}/', $content);
                foreach ($paragraphs as $paragraph) {
                    // Terapkan nl2br untuk baris baru tunggal di dalam paragraf dan cetak
                    echo '<p>' . nl2br(trim($paragraph), false) . '</p>';
                }
                ?>
            </div>
        </article>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>