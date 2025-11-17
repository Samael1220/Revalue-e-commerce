<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RE-VALUE.PH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Anton&family=Bebas+Neue&family=Oswald:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800;900&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles.css" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="carousel.js"></script>
  </head>
  <body>
    <header class="header">
      <div class="container">
        <div class="nav-wrapper">
          <!-- Logo -->
          <div class="logo">
            <img src="uploads/logo.webp" alt="logo" />
            <a href="#" class="brand">RE-VALUE.PH</a>
          </div>

          <!-- Desktop Navigation -->
          <nav class="nav-links">
            <a href="#">HOME</a>
            <a href="#new">NEW DROPS</a>
            <a href="#collection">COLLECTIONS</a>
            <a href="#footer">CONTACT</a>
          </nav>

          <!-- Right Actions -->
          <div class="actions">
            <a href="store.php" target="_blank" class="sign-in-btn">SHOP NOW</a>
            <button id="menu-btn" class="icon-btn mobile-only">
              <i data-lucide="menu"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div id="mobile-menu" class="mobile-menu">
        <nav class="nav-links">
          <a href="#home">HOME</a>
          <a href="#new">NEW DROPS</a>
          <a href="#collection">COLLECTIONS</a>
          <a href="#footer">CONTACT</a>
        </nav>
      </div>
    </header>

    <main>
      <section class="hero-section">
        <!-- Background Video -->
        <div class="video-container" id="home">
          <video autoplay muted loop playsinline>
            <source
              src="vid/Conceptual Commercial  thrift Shop  Sony a7siii - Gassel Kandathil (1080p, h264).mp4"
              type="video/mp4"
            />
            Your browser does not support the video tag.
          </video>
          <div class="video-overlay"></div>
        </div>

        <!-- Hero Content -->
        <div class="hero-content">
          <div class="hero-text-container">
            <div class="hero-badge">EST. 2025</div>
            <h1 class="hero-title">
              <span class="title-line dwt">REVALUE.</span>
              <span class="title-line accent-text">REWEAR.</span>
              <span class="title-line wt">REPEAT.</span>
            </h1>
            <p class="hero-subtext wt">
              CURATED PRE-LOVED FASHION. REDEFINED FOR THE STREETS.
            </p>
            <div class="hero-buttons">
              <a href="store.php" class="btn btn-primary">EXPLORE DROPS</a>
              <a href="#collection" class="btn btn-outline brw wt"
                >VIEW COLLECTION</a
              >
            </div>
          </div>
        </div>
      </section>
<!-- $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ -->
      <section class="featured-section" id="new">
    <div class="container">
        <div class="featured-header">
            <div class="section-tag">NEW DROPS</div>
            <h2 class="section-title">FRESH FINDS</h2>
        </div>

        <!-- Carousel Wrapper -->
        <div class="carousel">
            

            <div class="carousel-track-container">
                <div class="carousel-track">
                    <?php
include('db.php');

$stmt = $conn->prepare("SELECT id, name, category, image, price FROM inventory ORDER BY id DESC LIMIT 12");
$stmt->execute();
$products = $stmt->get_result();

if ($products && $products->num_rows > 0):
    while ($product = $products->fetch_assoc()):
?>
    <div class="product-card">
        <div class="product-image">
            <img src="<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" 
                 alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" />
           
        </div>
        <div class="product-info">
            <span class="product-category"><?php echo htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?></span>
            <h3><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <span class="product-price">₱<?php echo number_format((float)$product['price'], 2); ?></span>
        </div>
    </div>
<?php
    endwhile;
else:
?>
    <p>No products found.</p>
<?php
endif;

$stmt->close();
$conn->close();
?>
                </div>
            </div>

           
        </div>
    </div>
</section>

      <section class="collage-section" id="collection">
        <div class="container">
          <div class="featured-header">
            <div class="section-tag">COLLECTIONS</div>
            <h2 class="section-title">THE ARCHIVE</h2>
          </div>

          <div class="collage-balanced">
            <!-- Left Column -->
            <div class="collage-column">
              <div class="collage-item tall">
                <img
                  src="models/Gemini_Generated_Image_iyj008iyj008iyj0.png"
                  alt="Running Model"
                />
                <div class="collage-text">
                  <span class="collage-tag">OUTERWEAR</span>
                  <h3>BRANDED JACKETS</h3>
                </div>
              </div>
              <div class="collage-item tall">
                <img
                  src="models/Gemini_Generated_Image_vni94svni94svni9.png"
                  alt="Urban Style"
                />
                <div class="collage-text">
                  <span class="collage-tag">LIFESTYLE</span>
                  <h3>URBAN VIBES</h3>
                </div>
              </div>
              <div class="collage-item medium">
                <img
                  src="models/Gemini_Generated_Image_brtx12brtx12brtx.png"
                  alt=""
                />
                <div class="collage-text">
                  <span class="collage-tag">VINTAGE</span>
                  <h3>VINTAGE ADIDAS</h3>
                </div>
              </div>
            </div>

            <!-- Right Column -->
            <div class="collage-column">
              <div class="collage-item medium">
                <img
                  src="models/552217052_719329417788092_2995318837290783952_n.png"
                  alt="Women's Collection"
                />
                <div class="collage-text">
                  <span class="collage-tag">ESSENTIALS</span>
                  <h3>MEN'S COLLECTION</h3>
                </div>
              </div>
              <div class="collage-item tall">
                <img
                  src="models/Gemini_Generated_Image_l3kc72l3kc72l3kc.png"
                  alt="Training Gear"
                />
                <div class="collage-text">
                  <span class="collage-tag">SPORTSWEAR</span>
                  <h3>NIKE GEAR</h3>
                </div>
              </div>
              <div class="collage-item tall">
                <img
                  src="models/Gemini_Generated_Image_mjuvi5mjuvi5mjuv.png"
                  alt="Street Wear"
                />
                <div class="collage-text">
                  <span class="collage-tag">PREMIUM</span>
                  <h3>STREET WEAR</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="about-section" id="about">
        <div class="container about-container">
          <div class="about-content">
            <div class="section-tag">OUR STORY</div>
            <h2 class="about-title">
              REDEFINING <span class="accent-text">STREET CULTURE</span>
            </h2>
            <p class="about-description">
              At <strong>REVALUE</strong>, we believe in redefining fashion
              through sustainability and creativity. Every thrifted piece we
              curate tells a story — reimagined, revalued, and ready to be part
              of a new journey.
            </p>

            <div class="our-story">
              <h3>THE MOVEMENT</h3>
              <p>
                What started as a small love for thrifting grew into a
                purpose-driven shop — one that promotes mindful fashion and
                timeless style. Each collection is handpicked, cleaned, and
                styled with care, ensuring every piece continues its story in
                someone else's wardrobe.
              </p>
              <button class="bt" onclick="window.open('story.html', '_blank')">
                READ MORE
              </button>
            </div>
          </div>

          <!-- Image side containers -->
          <div class="about-images">
            <div class="about-img-box">
              <img src="uploads/2.jpg" alt="Revalue Thrift Shop" />
              <div class="img-overlay">
                <span class="img-tag">CURATED</span>
              </div>
            </div>
            <div class="about-img-box">
              <img src="uploads/1.jpg" alt="Thrift Collection" />
              <div class="img-overlay">
                <span class="img-tag">AUTHENTIC</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <footer class="footer" id="footer">
        <div class="container footer-container">
          <div class="footer-brand">
            <h3>RE-VALUE.PH</h3>
            <p>
              REDEFINING STYLE WITH MODERN AND VINTAGE COLLECTIONS FOR EVERY
              GENERATION.
            </p>
            <div class="footer-badge">EST. 2025</div>
          </div>

          <div class="footer-links">
            <h4>SHOP</h4>
            <ul>
              <li><a href="#new">NEW ARRIVALS</a></li>
              <li><a href="#men">MEN</a></li>
              <li><a href="#women">WOMEN</a></li>
              <li><a href="#sale">SALE</a></li>
            </ul>
          </div>

          <div class="footer-links">
            <h4>SUPPORT</h4>
            <ul>
              <li><a href="#">CONTACT US</a></li>
              <li><a href="#">FAQs</a></li>
              <li><a href="#">SHIPPING & RETURNS</a></li>
              <li><a href="#">PRIVACY POLICY</a></li>
            </ul>
          </div>

          <div class="footer-socials">
            <h4>FOLLOW US</h4>
            <div class="social-icons">
              <a
                href="https://www.facebook.com/AngelsThrift11"
                aria-label="Facebook"
              >
                <i data-lucide="facebook"></i>
              </a>
              <a
                href="https://www.instagram.com/re_value.ph/"
                aria-label="Instagram"
              >
                <i data-lucide="instagram"></i>
              </a>
              <a
                href="https://www.facebook.com/AngelsThrift11"
                aria-label="Twitter"
              >
                <i data-lucide="twitter"></i>
              </a>
            </div>
          </div>
        </div>

        <div class="footer-bottom">
          <p>© 2025 RE-VALUE.PH. ALL RIGHTS RESERVED.</p>
        </div>
      </footer>
    </main>

    <script>
      lucide.createIcons();

      const menuBtn = document.getElementById("menu-btn");
      const mobileMenu = document.getElementById("mobile-menu");

      menuBtn.addEventListener("click", () => {
        mobileMenu.classList.toggle("active");
      });
    </script>
  </body>
</html>
