<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Realm | Discover World of Flavors</title>
    <meta name="description" content="Recipe Realm is your digital cookbook for discovering and sharing amazing recipes from around the globe.">
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-utensils"></i>
            <span><!--<img src="Logo.png" width = "50" height="55">-->Recipe Realm</span>
        </div>
        <ul class="nav-links">
            <li><a href="#" class="nav-link active" data-page="home">Home</a></li>
            <li><a href="#" class="nav-link" data-page="discovery">Recipes</a></li>
            <li><a href="#" class="nav-link" data-page="add-recipe">Add Recipe</a></li>
            <li><a href="#" class="nav-link" data-page="profile">Profile</a></li>
        </ul>
        <div class="nav-actions">
            <div class="search-icon" id="search-trigger">
                <i class="fas fa-search"></i>
            </div>
            <button class="btn login-btn" id="login-button" data-page="login">Login</button>
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main id="main-content">
        
        <!-- HOME PAGE VIEW -->
        <section id="home" class="page-view active">
            <!-- Hero Slider -->
            <div class="hero">
                <div class="slider" id="hero-slider">
                    <!-- Slides will be injected by JS or hardcoded for efficiency -->
                    <div class="slide" id="hero-slide-1">
                        <div class="hero-content">
                            <h1>Welcome to Recipe Realm</h1>
                            <p>Step in a World of Flavors and discover your next favorite meal.</p>
                            <button class="btn login-btn" onclick="app.navigateTo('discovery')">Explore Recipes</button>
                        </div>
                    </div>
                </div>
                <div class="slider-controls">
                    <span class="dot active"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
            </div>

            <!-- Featured Recipes -->
            <div class="featured-section">
                <div class="section-title">
                    <h2>Featured Recipes</h2>
                    <p>Hand-picked dishes just for you</p>
                </div>
                <div class="recipe-grid" id="featured-grid">
                    <!-- Cards injected by JS -->
                </div>
            </div>

            <!-- Trending Recipes -->
            <div class="trending-section" style="background: var(--bg-alt);">
                <div class="section-title">
                    <h2>Trending Now</h2>
                    <p>What everyone is cooking this week</p>
                </div>
                <div class="recipe-grid" id="trending-grid">
                    <!-- Cards injected by JS -->
                </div>
            </div>
        </section>

        <!-- DISCOVERY PAGE VIEW -->
        <section id="discovery" class="page-view">
            <div class="discovery-header">
                <h1>Discovery Recipes</h1>
                <p>Browse through our collection of amazing dishes</p>
                <div class="search-container">
                    <input type="text" class="search-bar" id="recipe-search" placeholder="Search for recipes, ingredients...">
                </div>
                <div class="category-filters" id="category-filters">
                    <button class="filter-btn active" data-category="all">All</button>
                    <button class="filter-btn" data-category="Vegetarian">Vegetarian</button>
                    <button class="filter-btn" data-category="Vegan">Vegan</button>
                    <button class="filter-btn" data-category="Gluten-Free">Gluten-Free</button>
                    <button class="filter-btn" data-category="Dessert">Desserts</button>
                </div>
            </div>
            <div class="recipe-grid" id="discovery-grid">
                <!-- Cards injected by JS -->
            </div>
        </section>

        <!-- ADD RECIPE PAGE VIEW -->
        <section id="add-recipe" class="page-view">
            <div class="section-title">
                <h2>Share Your Recipe</h2>
                <p>Contribute to our community and inspire others</p>
            </div>
            <div class="form-container">
                <form id="recipe-form">
                    <div class="form-group">
                        <label for="recipe-title">Recipe Title</label>
                        <input type="text" id="recipe-title" placeholder="e.g. Grandma's Secret Pasta" required>
                    </div>
                    <div class="form-group">
                        <label for="recipe-category">Category</label>
                        <select id="recipe-category" required>
                            <option value="">Select Category</option>
                            <option value="Appetizer">Appetizer</option>
                            <option value="Main Course">Main Course</option>
                            <option value="Dessert">Dessert</option>
                            <option value="Snack">Snack</option>
                            <option value="Vegetarian">Vegetarian</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="recipe-ingredients">Ingredients</label>
                        <textarea id="recipe-ingredients" placeholder="Separate each ingredient with a comma (e.g. 2 cups flour, 1 tsp salt)" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="recipe-instructions">Instructions</label>
                        <textarea id="recipe-instructions" placeholder="Step-by-step guide..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="recipe-email">Your Email</label>
                        <input type="email" id="recipe-email" placeholder="jane@example.com" required>
                    </div>
                    <button type="submit" class="btn submit-btn">Submit Recipe</button>
                </form>
            </div>
        </section>

        <!-- RECIPE DETAILS PAGE VIEW -->
        <section id="recipe-details" class="page-view">
            <button class="btn view-btn" onclick="app.navigateTo('discovery')" style="margin-bottom: 20px;">
                <i class="fas fa-arrow-left"></i> Back to Gallery
            </button>
            <div id="details-container" class="recipe-details-view">
                <!-- Data injected by JS -->
            </div>
        </section>

        <!-- USER PROFILE PAGE VIEW -->
        <section id="profile" class="page-view">
            <div class="profile-header">
                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=200&h=200&auto=format&fit=crop" alt="Jane Doe" class="profile-img">
                <div class="profile-info">
                    <h2>Jane Doe</h2>
                    <p><i class="fas fa-utensils"></i> Home Cook | London, UK</p>
                    <p style="margin-top: 10px;">Exploring the world, one recipe at a time. Passionate about plant-based cooking.</p>
                </div>
            </div>
            
            <div class="section-title" style="text-align: left;">
                <h3>My Created Recipes</h3>
            </div>
            <div class="recipe-grid" id="my-recipes-grid">
                <!-- User's recipes -->
            </div>

            <div class="section-title" style="text-align: left; margin-top: 40px;">
                <h3>Recently Viewed</h3>
            </div>
            <ul id="recent-viewed-list" style="display: flex; gap: 20px; overflow-x: auto; padding-bottom: 15px;">
                <!-- Recent items -->
            </ul>
        </section>

        <!-- LOGIN PAGE VIEW -->
        <section id="login" class="page-view">
            <div class="login-container">
                <h2>Welcome Back</h2>
                <p id="auth-status" style="margin-bottom: 16px; color: var(--text-muted);"></p>
                <form id="login-form">
                    <div class="form-group">
                        <label for="login-user">Username or Email</label>
                        <input type="text" id="login-user" placeholder="Enter your credentials" required>
                    </div>
                    <div class="form-group">
                        <label for="login-pass">Password</label>
                        <input type="password" id="login-pass" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn submit-btn">Login</button>
                    <a href="#" class="forgot-pw">Forgot Password?</a>
                    <a href="#" class="forgot-pw" id="show-register">Create New Account</a>
                </form>

                <form id="register-form" style="display: none; margin-top: 24px;">
                    <div class="form-group">
                        <label for="register-username">Username</label>
                        <input type="text" id="register-username" placeholder="Choose a username" required>
                    </div>
                    <div class="form-group">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" placeholder="you@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="register-pass">Password</label>
                        <input type="password" id="register-pass" placeholder="Minimum 6 characters" required>
                    </div>
                    <button type="submit" class="btn submit-btn">Create Account</button>
                    <a href="#" class="forgot-pw" id="show-login">Back to Login</a>
                </form>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer>
        <span class="footer-logo"><i class="fas fa-utensils"></i> Recipe Realm</span>
        <div class="social-links">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-pinterest"></i></a>
        </div>
        <p class="copyright">&copy; 2026 Recipe Realm. All rights reserved. Built for University Project.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
