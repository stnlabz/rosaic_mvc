<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Ars Rosaic' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    
    <link rel="stylesheet" href="/assets/css/rosaic.css">
    <link rel="icon" href="/assets/icons/favicon.ico" type="image/x-icon">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml" />
    <link rel="alternate" type="application/rss+xml" title="ROR" href="/ror.xml" />
    <link rel="help" type="text/plain" href="/llms.txt">
    
     <meta name="keywords" content="tradition,sovereignty,discipline,witchcraft,belief,coven,person,spirit,seeker,neophyte,adeptus-minor,priestess,adeptus,prioress,quiet,bell" />

    <meta name="author" content="Indicia Institute" />
    <meta name="copyright" content="Ars Rosaic" />
    <meta name="application-name" content="arsrosaic.org" />

    <!-- For Facebook -->
    <meta property="og:title" content="Ars Rosaic" />
    <meta property="og:image" content="https://www.arsrosaic.org/assets/icons/icon.png" />
    <meta property="og:url" content="https://www.arsrosaic.org" />
    <meta property="og:description" content="tradition * sovereignty * discipline * Witchcraft" />

    <!-- For Twitter -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="Ars Rosaic" />
    <meta name="twitter:description" content="tradition * sovereignty * discipline * Witchcraft" />
    <meta name="twitter:url" content="https://www.arsrosaic.org" />
    <meta name="twitter:image" content="https://www.arsrosaic.org/assets/icons/icon.png" />
    
</head>
<body>
    <header style="display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; border-bottom: 1px solid #ccc;">
        <div class="branding">
            <a href="/">
                <img src="/assets/icons/icon.png" alt="ars rosaic" style="height: 50px;"></a> <strong>Rosaic</strong> | ARS ROSAIC
        </div>
        
        <nav style="display: flex; gap: 20px; align-items: center;">
            <a href="/">Home</a>
            <a href="/org/about">About</a>
            <a href="/signup">Signup</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isset($_SESSION['user_level']) && $_SESSION['user_level'] >= 9): ?>
                    <a href="/admin" class="btn btn-sm btn-outline-primary">Admin</a>
                <?php endif; ?>
                
                <a href="/logout" style="color: #666; text-decoration: none;">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="container">
    <main style="padding: 20px;">
