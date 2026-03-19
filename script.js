// Fallback recipes used when API has no data or is unavailable.
const fallbackRecipes = [
  {
    id: 1,
    title: "Pasta Primavera",
    category: "Main Course",
    description:
      "A fresh and vibrant medley of seasonal vegetables tossed with al dente pasta in a light, zesty lemon-parmesan sauce.",
    time: "25 mins",
    servings: "4",
    difficulty: "Easy",
    image: "pasta_primavera_recipe_1772531900140.png",
    ingredients: [
      "12 oz Spaghetti or Linguine",
      "2 cups Broccoli florets",
      "1 Carrot, julienned",
      "1 Bell pepper, sliced",
      "2 cloves Garlic, minced",
      "1/4 cup Olive oil",
      "1/2 cup Grated Parmesan",
      "Juice of 1 Lemon",
      "Fresh Basil for garnish",
    ],
    instructions: [
      "Bring a large pot of salted water to a boil. Cook pasta according to package directions.",
      "In a large skillet, heat olive oil over medium heat. Add garlic and vegetables.",
      "Sauté vegetables for 5-7 minutes until tender-crisp.",
      "Toss the cooked pasta with the vegetables, lemon juice, and parmesan cheese.",
      "Garnish with fresh basil and serve immediately.",
    ],
    featured: true,
    trending: true,
  },
  {
    id: 2,
    title: "Spicy Mexican Tacos",
    category: "Main Course",
    description:
      "Authentic street-style tacos featuring perfectly seasoned meat, fresh cilantro, radishes, and a kick of spicy salsa.",
    time: "30 mins",
    servings: "3",
    difficulty: "Intermediate",
    image: "spicy_tacos_1772531723048.png",
    ingredients: [
      "1 lb Ground Beef or Steak strips",
      "12 Small corn tortillas",
      "1/2 White onion, finely diced",
      "1/2 cup Fresh cilantro, chopped",
      "4 Radishes, thinly sliced",
      "2 Limes, cut into wedges",
      "1 tbsp Taco seasoning",
      "Salsa roja for serving",
    ],
    instructions: [
      "Cook the meat in a skillet with taco seasoning until browned and fully cooked.",
      "Warm the tortillas in a separate dry skillet or over an open flame.",
      "Place a generous spoonful of meat onto each tortilla.",
      "Top with onions, cilantro, and radish slices.",
      "Serve with lime wedges and salsa on the side.",
    ],
    featured: true,
    trending: false,
  },
  {
    id: 3,
    title: "Golden Cauliflower Curry",
    category: "Vegetarian",
    description:
      "A warming and aromatic coconut-based curry filled with tender cauliflower florets and exotic spices.",
    time: "40 mins",
    servings: "4",
    difficulty: "Intermediate",
    image: "cauliflower_curry_1772531819823.png",
    ingredients: [
      "1 Head of Cauliflower, cut into florets",
      "1 can (14oz) Coconut milk",
      "1 Onion, diced",
      "2 tbsp Curry powder",
      "1 tsp Turmeric",
      "1 tbsp Ginger-garlic paste",
      "1 cup Vegetable broth",
      "Fresh cilantro for garnish",
    ],
    instructions: [
      "Sauté onions in a large pot until translucent. Add ginger-garlic paste and spices.",
      "Add cauliflower florets and stir until coated in spices.",
      "Pour in coconut milk and vegetable broth. Bring to a simmer.",
      "Cook for 20-25 minutes until cauliflower is tender.",
      "Serve hot over basmati rice, garnished with cilantro.",
    ],
    featured: true,
    trending: true,
  },
  {
    id: 4,
    title: "Classic Chocolate Fondue",
    category: "Dessert",
    description:
      "Indulgent melted chocolate served with a variety of fresh fruits and marshmallows for a perfect celebration.",
    time: "15 mins",
    servings: "6",
    difficulty: "Easy",
    image:
      "https://images.unsplash.com/photo-1511381939415-efaba117ea23?q=80&w=800&auto=format&fit=crop",
    ingredients: [
      "10 oz Dark semi-sweet chocolate",
      "1 cup Heavy cream",
      "1 tsp Vanilla extract",
      "Assorted fruits (strawberries, bananas, apples)",
      "Marshmallows",
    ],
    instructions: [
      "Chop the chocolate into small pieces and place in a glass bowl.",
      "Heat the cream in a small saucepan until just beginning to simmer.",
      "Pour hot cream over the chocolate and let sit for 5 minutes.",
      "Stir until smooth and glossy. Mix in vanilla extract.",
      "Transfer to a fondue pot or serve immediately with dipping items.",
    ],
    featured: false,
    trending: true,
  },
];

// App State and Controller
const app = {
  currentPage: "home",
  recipes: [...fallbackRecipes],

  async init() {
    this.cacheDOM();
    this.bindEvents();
    await this.loadRecipesFromServer();
    this.renderAll();
    this.initSlider();
  },

  async loadRecipesFromServer() {
    try {
      const response = await fetch("api.php?action=recipes", {
        method: "GET",
      });

      if (!response.ok) {
        return;
      }

      const payload = await response.json();
      if (
        payload &&
        payload.ok &&
        Array.isArray(payload.recipes) &&
        payload.recipes.length > 0
      ) {
        this.recipes = payload.recipes;
      }
    } catch (error) {
      console.warn("Recipe API unavailable. Using local fallback data.", error);
    }
  },

  cacheDOM() {
    this.pages = document.querySelectorAll(".page-view");
    this.navLinks = document.querySelectorAll(
      ".nav-link, .login-btn[data-page]",
    );
    this.featuredGrid = document.getElementById("featured-grid");
    this.trendingGrid = document.getElementById("trending-grid");
    this.discoveryGrid = document.getElementById("discovery-grid");
    this.detailsContainer = document.getElementById("details-container");
    this.myRecipesGrid = document.getElementById("my-recipes-grid");

    // Form & Filters
    this.recipeForm = document.getElementById("recipe-form");
    this.searchInput = document.getElementById("recipe-search");
    this.filterBtns = document.querySelectorAll(".filter-btn");
    this.loginForm = document.getElementById("login-form");
  },

  bindEvents() {
    // Navigation
    this.navLinks.forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        const pageId = link.getAttribute("data-page");
        this.navigateTo(pageId);
      });
    });

    // Search Real-time
    if (this.searchInput) {
      this.searchInput.addEventListener("input", (e) => {
        this.filterRecipes(e.target.value, this.getActiveCategory());
      });
    }

    // Category Filters
    this.filterBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        this.filterBtns.forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");
        this.filterRecipes(
          this.searchInput.value,
          btn.getAttribute("data-category"),
        );
      });
    });

    // Recipe Submission
    if (this.recipeForm) {
      this.recipeForm.addEventListener("submit", (e) => {
        e.preventDefault();
        this.handleRecipeSubmit();
      });
    }

    // Login Logic
    if (this.loginForm) {
      this.loginForm.addEventListener("submit", (e) => {
        e.preventDefault();
        alert("Login successful! Welcome back, Jane.");
        this.navigateTo("home");
      });
    }
  },

  navigateTo(pageId) {
    this.currentPage = pageId;
    this.pages.forEach((page) => {
      page.classList.remove("active");
      if (page.id === pageId) {
        page.classList.add("active");
      }
    });

    // Update Nav Active State
    this.navLinks.forEach((link) => {
      link.classList.remove("active");
      if (link.getAttribute("data-page") === pageId) {
        link.classList.add("active");
      }
    });

    window.scrollTo(0, 0);
    this.renderPage(pageId);
  },

  renderPage(pageId) {
    if (pageId === "home") this.renderHome();
    if (pageId === "discovery") this.renderDiscovery();
    if (pageId === "profile") this.renderProfile();
  },

  renderAll() {
    this.renderHome();
    this.renderDiscovery();
  },

  createRecipeCard(recipe) {
    const card = document.createElement("div");
    card.className = "recipe-card";
    card.innerHTML = `
            <div class="card-img">
                <img src="${recipe.image}" alt="${recipe.title}">
                <span class="card-badge">${recipe.category}</span>
            </div>
            <div class="card-content">
                <h3>${recipe.title}</h3>
                <p>${recipe.description || "A delicious dish you must try."}</p>
                <div class="card-footer">
                    <div class="card-time">
                        <i class="far fa-clock"></i> ${recipe.time}
                    </div>
                    <button class="btn view-btn" onclick="app.showDetails(${recipe.id})">View Recipe</button>
                </div>
            </div>
        `;
    return card;
  },

  renderHome() {
    this.featuredGrid.innerHTML = "";
    this.trendingGrid.innerHTML = "";

    this.recipes
      .filter((r) => r.featured)
      .forEach((recipe) => {
        this.featuredGrid.appendChild(this.createRecipeCard(recipe));
      });

    this.recipes
      .filter((r) => r.trending)
      .forEach((recipe) => {
        this.trendingGrid.appendChild(this.createRecipeCard(recipe));
      });
  },

  renderDiscovery(data = this.recipes) {
    this.discoveryGrid.innerHTML = "";
    if (data.length === 0) {
      this.discoveryGrid.innerHTML =
        '<p style="grid-column: 1/-1; text-align: center; padding: 40px;">No recipes found matching your search.</p>';
      return;
    }
    data.forEach((recipe) => {
      this.discoveryGrid.appendChild(this.createRecipeCard(recipe));
    });
  },

  filterRecipes(query, category) {
    let filtered = this.recipes;

    if (category && category !== "all") {
      filtered = filtered.filter((r) =>
        r.category.toLowerCase().includes(category.toLowerCase()),
      );
    }

    if (query) {
      filtered = filtered.filter(
        (r) =>
          r.title.toLowerCase().includes(query.toLowerCase()) ||
          r.category.toLowerCase().includes(query.toLowerCase()),
      );
    }

    this.renderDiscovery(filtered);
  },

  getActiveCategory() {
    const activeBtn = document.querySelector(".filter-btn.active");
    return activeBtn ? activeBtn.getAttribute("data-category") : "all";
  },

  showDetails(id) {
    const recipe = this.recipes.find((r) => r.id === id);
    if (!recipe) return;

    this.detailsContainer.innerHTML = `
            <div class="details-hero">
                <img src="${recipe.image}" alt="${recipe.title}">
            </div>
            <div class="details-content">
                <h1 style="font-size: 3rem; margin-bottom: 20px;">${recipe.title}</h1>
                
                <div class="meta-box">
                    <div class="meta-item">
                        <span>PREP TIME</span>
                        <strong>${recipe.time}</strong>
                    </div>
                    <div class="meta-item">
                        <span>SERVINGS</span>
                        <strong>${recipe.servings} People</strong>
                    </div>
                    <div class="meta-item">
                        <span>DIFFICULTY</span>
                        <strong>${recipe.difficulty}</strong>
                    </div>
                </div>

                <div class="details-split">
                    <div class="ingredients-col">
                        <h3>Ingredients</h3>
                        <ul class="ingredients-list">
                            ${recipe.ingredients.map((ing) => `<li>${ing}</li>`).join("")}
                        </ul>
                    </div>
                    <div class="instructions-col">
                        <h3>Instructions</h3>
                        <ol class="instructions-list">
                            ${recipe.instructions
                              .map(
                                (step, i) => `
                                <li>
                                    <span class="step-num">${i + 1}</span>
                                    ${step}
                                </li>
                            `,
                              )
                              .join("")}
                        </ol>
                    </div>
                </div>
            </div>
        `;

    this.navigateTo("recipe-details");
  },

  async handleRecipeSubmit() {
    const title = document.getElementById("recipe-title").value;
    const category = document.getElementById("recipe-category").value;
    const ingredients = document
      .getElementById("recipe-ingredients")
      .value.split(",")
      .map((i) => i.trim());
    const instructions = document
      .getElementById("recipe-instructions")
      .value.split("\n")
      .filter((s) => s.trim());
    const email = document.getElementById("recipe-email").value;

    // Validation (simple check already done by 'required' in HTML, but added here for logic)
    if (!title || !category || ingredients.length === 0 || !email) {
      alert("Please fill in all fields correctly.");
      return;
    }

    try {
      const response = await fetch("api.php?action=add_recipe", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          title,
          category,
          ingredients,
          instructions,
          email,
        }),
      });

      const payload = await response.json();
      if (!response.ok || !payload.ok || !payload.recipe) {
        throw new Error(payload.error || "Failed to submit recipe.");
      }

      this.recipes.unshift(payload.recipe);
      alert("Success! Your recipe has been submitted to the Realm.");
      console.log("New Recipe Submitted:", payload.recipe);

      this.recipeForm.reset();
      this.navigateTo("discovery");
    } catch (error) {
      alert(
        `Recipe submit failed: ${error.message}. Please ensure WAMP MySQL is running.`,
      );
    }
  },

  renderProfile() {
    this.myRecipesGrid.innerHTML = "";
    const userRecipes = this.recipes.filter((r) => r.userCreated);

    if (userRecipes.length === 0) {
      this.myRecipesGrid.innerHTML =
        '<p style="grid-column: 1/-1;">You haven\'t created any recipes yet.</p>';
    } else {
      userRecipes.forEach((recipe) => {
        this.myRecipesGrid.appendChild(this.createRecipeCard(recipe));
      });
    }
  },

  initSlider() {
    const slider = document.getElementById("hero-slider");
    const dots = document.querySelectorAll(".dot");
    const slidesData = [
      {
        title: "Welcome to Recipe Realm",
        p: "Step in a World of Flavors and discover your next favorite meal.",
        img: "recipe_realm_hero_1772531686443.png",
      },
      {
        title: "Fresh Spring Pasta",
        p: "Light, zesty, and filled with seasonal greens.",
        img: "pasta_primavera_recipe_1772531900140.png",
      },
      {
        title: "Authentic Street Tacos",
        p: "Bring the taste of Mexico directly to your kitchen.",
        img: "spicy_tacos_1772531723048.png",
      },
    ];

    let currentSlide = 0;

    const renderSlide = (index) => {
      const data = slidesData[index];
      slider.innerHTML = `
                <div class="slide" style="background-image: url('${data.img}'); width: 100%;">
                    <div class="hero-content">
                        <h1 style="animation: fadeInUp 0.8s ease;">${data.title}</h1>
                        <p style="animation: fadeInUp 1s ease;">${data.p}</p>
                        <button class="btn login-btn" onclick="app.navigateTo('discovery')">Explore Recipes</button>
                    </div>
                </div>
            `;

      dots.forEach((dot, i) => {
        dot.classList.toggle("active", i === index);
      });
    };

    // Auto slide
    setInterval(() => {
      currentSlide = (currentSlide + 1) % slidesData.length;
      renderSlide(currentSlide);
    }, 5000);

    // Dot clicks
    dots.forEach((dot, i) => {
      dot.addEventListener("click", () => {
        currentSlide = i;
        renderSlide(currentSlide);
      });
    });

    renderSlide(0);
  },
};

// Initialize App
document.addEventListener("DOMContentLoaded", () => {
  app.init().catch((error) => {
    console.error("App initialization failed:", error);
  });

  // Mobile Nav Toggle
  const menuToggle = document.querySelector(".menu-toggle");
  const navLinks = document.querySelector(".nav-links");

  menuToggle.addEventListener("click", () => {
    navLinks.style.display =
      navLinks.style.display === "flex" ? "none" : "flex";
    navLinks.style.flexDirection = "column";
    navLinks.style.position = "absolute";
    navLinks.style.top = "80px";
    navLinks.style.left = "0";
    navLinks.style.width = "100%";
    navLinks.style.background = "white";
    navLinks.style.padding = "20px";
    navLinks.style.boxShadow = "0 10px 10px rgba(0,0,0,0.1)";
  });
});
