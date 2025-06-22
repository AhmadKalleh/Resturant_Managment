// Sample product data
const products = [
    {
        id: 1,
        nameEn: "Grilled Salmon",
        nameAr: "سلمون مشوي",
        category: "main-courses",
        price: 24.99,
        calories: 450,
        rating: 5,
        image: "images/products/salmon.jpg",
        descriptionEn: "Fresh Atlantic salmon grilled to perfection with lemon herb butter",
        descriptionAr: "سلمون أطلنطي طازج مشوي مع زبدة الليمون والأعشاب"
    },
    {
        id: 2,
        nameEn: "Caesar Salad",
        nameAr: "سلطة قيصر",
        category: "appetizers",
        price: 12.99,
        calories: 320,
        rating: 4,
        image: "images/products/caesar.jpg",
        descriptionEn: "Crisp romaine lettuce with Caesar dressing, croutons, and parmesan",
        descriptionAr: "خس رومين مقرمش مع صلصة قيصر وكرتونس وجبن بارميزان"
    },
    {
        id: 3,
        nameEn: "Chocolate Cake",
        nameAr: "كيك الشوكولاتة",
        category: "desserts",
        price: 8.99,
        calories: 550,
        rating: 5,
        image: "images/products/cake.jpg",
        descriptionEn: "Rich chocolate cake with ganache and fresh berries",
        descriptionAr: "كيك شوكولاتة غني مع غاناش وتوت طازج"
    }
];

// DOM Elements
const productsTableBody = document.getElementById('productsTableBody');
const searchInput = document.querySelector('.search-box input');
const categoryFilter = document.getElementById('categoryFilter');
const priceFilter = document.getElementById('priceFilter');
const caloriesFilter = document.getElementById('caloriesFilter');
const ratingFilter = document.getElementById('ratingFilter');
const filterToggle = document.getElementById('filterToggle');
const filtersPanel = document.getElementById('filtersPanel');
const applyFilters = document.getElementById('applyFilters');
const resetFilters = document.getElementById('resetFilters');
const addProductBtn = document.getElementById('addProductBtn');
const productModal = document.getElementById('productModal');
const closeModal = document.getElementById('closeModal');
const cancelForm = document.getElementById('cancelForm');
const productForm = document.getElementById('productForm');
const imageUpload = document.getElementById('imageUpload');
const imagePreview = document.getElementById('imagePreview');

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
    setupEventListeners();
});

// Load products into table
function loadProducts(filteredProducts = products) {
    productsTableBody.innerHTML = '';
    
    filteredProducts.forEach((product, index) => {
        const row = document.createElement('tr');
        row.style.animationDelay = `${index * 0.1}s`;
        
        row.innerHTML = `
            <td>
                <img src="${product.image}" alt="${product.nameEn}" class="product-image">
            </td>
            <td>
                <div class="product-name">
                    <span class="en">${product.nameEn}</span>
                    <span class="ar">${product.nameAr}</span>
                </div>
            </td>
            <td>
                <span class="product-category">${formatCategory(product.category)}</span>
            </td>
            <td>
                <span class="product-price">$${product.price.toFixed(2)}</span>
            </td>
            <td>
                <span class="product-calories">${product.calories} cal</span>
            </td>
            <td>
                <div class="product-rating">
                    ${generateStars(product.rating)}
                </div>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="edit-btn" onclick="editProduct(${product.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="delete-btn" onclick="deleteProduct(${product.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        
        productsTableBody.appendChild(row);
    });
}

// Event Listeners
function setupEventListeners() {
    // Search
    searchInput.addEventListener('input', debounce(() => {
        filterProducts();
    }, 300));

    // Filters
    filterToggle.addEventListener('click', () => {
        filtersPanel.classList.toggle('active');
    });

    applyFilters.addEventListener('click', () => {
        filterProducts();
        filtersPanel.classList.remove('active');
    });

    resetFilters.addEventListener('click', () => {
        categoryFilter.value = '';
        priceFilter.value = '';
        caloriesFilter.value = '';
        ratingFilter.value = '';
        filterProducts();
    });

    // Modal
    addProductBtn.addEventListener('click', () => {
        openModal();
    });

    closeModal.addEventListener('click', () => {
        closeModalForm();
    });

    cancelForm.addEventListener('click', () => {
        closeModalForm();
    });

    productForm.addEventListener('submit', (e) => {
        e.preventDefault();
        saveProduct();
    });

    // Image Upload
    imageUpload.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                `;
            };
            reader.readAsDataURL(file);
        }
    });
}

// Filter Products
function filterProducts() {
    const searchTerm = searchInput.value.toLowerCase();
    const category = categoryFilter.value;
    const price = priceFilter.value;
    const calories = caloriesFilter.value;
    const rating = ratingFilter.value;

    const filtered = products.filter(product => {
        const matchesSearch = product.nameEn.toLowerCase().includes(searchTerm) ||
                            product.nameAr.includes(searchTerm);
        const matchesCategory = !category || product.category === category;
        const matchesPrice = !price || checkPriceRange(product.price, price);
        const matchesCalories = !calories || checkCalorieRange(product.calories, calories);
        const matchesRating = !rating || product.rating >= parseInt(rating);

        return matchesSearch && matchesCategory && matchesPrice && matchesCalories && matchesRating;
    });

    loadProducts(filtered);
}

// Modal Functions
function openModal(product = null) {
    productModal.classList.add('active');
    document.getElementById('modalTitle').textContent = product ? 'Edit Product' : 'Add New Product';
    
    if (product) {
        // Fill form with product data
        productForm.elements.category.value = product.category;
        productForm.elements.nameEn.value = product.nameEn;
        productForm.elements.nameAr.value = product.nameAr;
        productForm.elements.price.value = product.price;
        productForm.elements.calories.value = product.calories;
        productForm.elements.rating.value = product.rating;
        productForm.elements.descriptionEn.value = product.descriptionEn;
        productForm.elements.descriptionAr.value = product.descriptionAr;
        
        if (product.image) {
            imagePreview.innerHTML = `
                <img src="${product.image}" alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
            `;
        }
    } else {
        productForm.reset();
        imagePreview.innerHTML = `
            <i class="fas fa-cloud-upload-alt"></i>
            <span>Click to upload image</span>
        `;
    }
}

function closeModalForm() {
    productModal.classList.remove('active');
    productForm.reset();
    imagePreview.innerHTML = `
        <i class="fas fa-cloud-upload-alt"></i>
        <span>Click to upload image</span>
    `;
}

function saveProduct() {
    // Get form data
    const formData = new FormData(productForm);
    const productData = {
        id: products.length + 1, // In real app, this would come from the backend
        nameEn: formData.get('nameEn'),
        nameAr: formData.get('nameAr'),
        category: formData.get('category'),
        price: parseFloat(formData.get('price')),
        calories: parseInt(formData.get('calories')),
        rating: parseInt(formData.get('rating')),
        descriptionEn: formData.get('descriptionEn'),
        descriptionAr: formData.get('descriptionAr'),
        image: imagePreview.querySelector('img')?.src || 'images/products/default.jpg'
    };

    // In a real app, this would be an API call
    products.push(productData);
    loadProducts();
    closeModalForm();
}

// Helper Functions
function formatCategory(category) {
    return category.split('-').map(word => 
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
}

function generateStars(rating) {
    return Array(5).fill('').map((_, index) => 
        `<i class="fas fa-star star ${index < rating ? 'filled' : ''}"></i>`
    ).join('');
}

function checkPriceRange(price, range) {
    switch(range) {
        case 'low': return price <= 10;
        case 'medium': return price > 10 && price <= 25;
        case 'high': return price > 25;
        default: return true;
    }
}

function checkCalorieRange(calories, range) {
    switch(range) {
        case 'low': return calories <= 300;
        case 'medium': return calories > 300 && calories <= 600;
        case 'high': return calories > 600;
        default: return true;
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Product Actions
function editProduct(id) {
    const product = products.find(p => p.id === id);
    if (product) {
        openModal(product);
    }
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        const index = products.findIndex(p => p.id === id);
        if (index !== -1) {
            products.splice(index, 1);
            loadProducts();
        }
    }
} 