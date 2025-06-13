<?php
// functions.php - Kumpulan fungsi pembantu

function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

// --- FUNGSI BERITA BARU (DENGAN FILTER KATEGORI) ---
function getNewsArticles($conn, $limit, $offset, $categoryId = null) {
    $sql = "SELECT n.id, n.title, n.slug, LEFT(n.content, 150) AS snippet, n.image_url, n.created_at, c.name as category_name, c.id as category_id
            FROM news n
            JOIN categories c ON n.category_id = c.id";
    
    if ($categoryId !== null) {
        $sql .= " WHERE n.category_id = ?";
    }
    
    $sql .= " ORDER BY n.created_at DESC LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);

    if ($categoryId !== null) {
        $stmt->bind_param("iii", $categoryId, $limit, $offset);
    } else {
        $stmt->bind_param("ii", $limit, $offset);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $news = [];
    while ($row = $result->fetch_assoc()) {
        $news[] = $row;
    }
    $stmt->close();
    return $news;
}

function getTotalNewsCount($conn, $categoryId = null) {
    $sql = "SELECT COUNT(*) AS total FROM news";
    if ($categoryId !== null) {
        $sql .= " WHERE category_id = ?";
    }
    
    $stmt = $conn->prepare($sql);

    if ($categoryId !== null) {
        $stmt->bind_param("i", $categoryId);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['total'];
}


// --- FUNGSI PENCARIAN BERITA ---
function searchNewsArticles($conn, $query, $limit, $offset) {
    $searchQuery = "%" . $query . "%";
    $sql = "SELECT n.id, n.title, n.slug, LEFT(n.content, 150) AS snippet, n.image_url, n.created_at, c.name as category_name
            FROM news n
            JOIN categories c ON n.category_id = c.id
            WHERE n.title LIKE ? OR n.content LIKE ?
            ORDER BY n.created_at DESC LIMIT ? OFFSET ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $searchQuery, $searchQuery, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $news = [];
    while ($row = $result->fetch_assoc()) {
        $news[] = $row;
    }
    $stmt->close();
    return $news;
}

function getTotalSearchCount($conn, $query) {
    $searchQuery = "%" . $query . "%";
    $sql = "SELECT COUNT(*) AS total FROM news WHERE title LIKE ? OR content LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $searchQuery, $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['total'];
}


// --- FUNGSI LAINNYA ---
function getNewsBySlug($conn, $slug) {
    $stmt = $conn->prepare("SELECT n.id, n.title, n.slug, n.content, n.image_url, n.created_at, c.name as category_name FROM news n JOIN categories c ON n.category_id = c.id WHERE n.slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $article = $result->fetch_assoc();
    $stmt->close();
    return $article;
}

function getAllCategories($conn) {
    $result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    return $categories;
}
?>