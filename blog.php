<?php 
$pageTitle = "AU // CENTENARY LOG";
include 'includes/header.php'; 
?>
<style>
    .blog-header {
        text-align: center;
        padding: 4rem 20px;
        background: var(--au-blue);
        color: var(--paper-white);
        border-bottom: var(--border-thick);
    }
    .article-card {
        border: var(--border-thick);
        background: #fff;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s;
    }
    .article-card:hover {
        transform: translateY(-5px);
        box-shadow: 8px 8px 0px rgba(0,0,0,0.1);
    }
    .article-img {
        height: 250px; 
        background: #ddd; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-family: var(--font-varsity); 
        font-size: 2rem; 
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .article-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.8;
    }
    .article-img::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(transparent, rgba(0,33,71,0.8));
    }
    .article-content {
        padding: 2rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .meta-tag {
        font-family: var(--font-tech); 
        font-size: 0.8rem; 
        color: var(--au-blue); 
        font-weight: bold;
        margin-bottom: 10px;
        background: var(--off-white);
        padding: 4px 8px;
        display: inline-block;
        border: 1px solid #ccc;
    }
</style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="blog-header">
    <p style="font-family: var(--font-tech); margin-bottom: 1rem; color: var(--au-gold);">// OFFICIAL NEWS & UPDATES</p>
    <h1 class="display-text" style="font-size: 4rem; color: #fff;">AU@100 LOG</h1>
    <p style="font-family: var(--font-street); max-width: 600px; margin: 1rem auto 0; font-size: 1.1rem; opacity: 0.9;">
        Documenting the historic Shatabdi Mahotsav, campus culture, and the archives of Andhra University.
    </p>
</div>

<div class="container" style="padding: 4rem 20px;">
    <div class="grid-2" style="gap: 3rem;">
        
        <!-- Article 1: Actual 2026 Event -->
        <article class="article-card">
            <div class="article-img" style="background: #0a0a0a;">
                <img src="uploads/products/adminBlock.jpg" alt="AU Campus">
                <span style="position: absolute; z-index: 2;">[IMG: AERIAL VIEW]</span>
            </div>
            <div class="article-content">
                <div>
                    <span class="meta-tag">APR 17, 2026 // EVENTS</span>
                    <h2 style="margin: 10px 0; font-size: 2rem; line-height: 1.1;">MASSIVE "AU@100" HUMAN CHAIN AT GOLDEN JUBILEE GROUNDS</h2>
                    <p style="margin-bottom: 1.5rem; color: #555; line-height: 1.6;">
                        In a stunning display of coordination, hundreds of students, faculty, and staff formed a massive human "AU@100" sign at the Engineering College grounds to mark the centenary. The formation, coded in the colors of the national flag, was captured via drone...
                    </p>
                </div>
                <button class="btn" style="font-size: 0.8rem; align-self: flex-start; margin-top: auto;">READ_REPORT</button>
            </div>
        </article>

        <!-- Article 2: Actual 2026 Event -->
        <article class="article-card">
            <div class="article-img" style="background: var(--au-blue);">
                <span style="position: absolute; z-index: 2; color: var(--au-gold);">[IMG: EXHIBITION]</span>
            </div>
            <div class="article-content">
                <div>
                    <span class="meta-tag">APR 15, 2026 // HISTORY</span>
                    <h2 style="margin: 10px 0; font-size: 2rem; line-height: 1.1;">100 YEARS OF LEGACY: RARE PHOTO EXHIBITION</h2>
                    <p style="margin-bottom: 1.5rem; color: #555; line-height: 1.6;">
                        Inaugurated by Vice-Chancellor Prof. G.P. Raja Sekhar at the TLN Sabha Hall, the Journalism Department curated a historic visual tribute. The archives showcase AU's evolution from 1926 and its deep connection with global leaders, scientists, and alumni...
                    </p>
                </div>
                <button class="btn" style="font-size: 0.8rem; align-self: flex-start; margin-top: auto;">VIEW_GALLERY</button>
            </div>
        </article>

        <!-- Article 3: Vision Document -->
        <article class="article-card" style="grid-column: 1 / -1; flex-direction: row; align-items: stretch;">
            <div class="article-img" style="width: 40%; height: auto; background: #222;">
                <span style="position: absolute; z-index: 2;">[IMG: BLUEPRINTS]</span>
            </div>
            <div class="article-content" style="width: 60%; justify-content: center;">
                <span class="meta-tag" style="align-self: flex-start;">MAR 20, 2026 // FUTURE</span>
                <h2 style="margin: 10px 0 20px; font-size: 2.5rem; line-height: 1;">THE CENTENARY VISION: AI, SUSTAINABILITY & INNOVATION</h2>
                <p style="margin-bottom: 2rem; font-size: 1.1rem; color: #444;">
                    Ahead of the grand finale on April 27, AU has released its comprehensive vision document. The university is set to introduce futuristic courses in Artificial Intelligence, establish a Centenary Start-up Challenge, and partner with the Ratan Tata Innovation Hub. 
                </p>
                <button class="btn" style="font-size: 0.9rem; align-self: flex-start; background: var(--au-blue); color: var(--au-gold);">DOWNLOAD VISION PDF</button>
            </div>
        </article>

    </div>
</div>

<?php include 'includes/footer.php'; ?>