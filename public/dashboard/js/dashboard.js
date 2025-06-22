document.addEventListener('DOMContentLoaded', function() {
    // Initialize Dashboard
    initializeDashboard();

    // Navigation
    const navLinks = document.querySelectorAll('.nav-menu a[data-section]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.getAttribute('data-section');
            
            // Add bounce animation to the clicked link
            this.classList.add('bounce-in');
            setTimeout(() => this.classList.remove('bounce-in'), 600);
            
            loadSection(section);
            
            // Update active state
            navLinks.forEach(l => {
                l.parentElement.classList.remove('active');
                l.classList.remove('bounce-in');
            });
            this.parentElement.classList.add('active');
        });
    });

    // Search functionality
    const searchInput = document.querySelector('.search-bar input');
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        // Implement search functionality
        console.log('Searching for:', searchTerm);
    });

    // Notifications
    const notifications = document.querySelector('.notifications');
    notifications.addEventListener('click', function() {
        // Implement notifications functionality
        console.log('Notifications clicked');
    });

    // User Profile
    const userProfile = document.querySelector('.user-profile');
    userProfile.addEventListener('click', function() {
        // Implement user profile functionality
        console.log('User profile clicked');
    });
});

// Initialize Dashboard
function initializeDashboard() {
    // Add initial animations to cards
    document.querySelectorAll('.stat-card').forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in');
        }, index * 100);
    });

    // Load Dashboard Data
    loadDashboardData();

    // Initialize Charts
    setTimeout(() => {
        initializeCharts();
    }, 500);
}

// Load Section Content
function loadSection(section) {
    const content = document.querySelector('.main-content');
    content.innerHTML = `
        <div class="text-center" style="height: 100vh; display: flex; align-items: center; justify-content: center;">
            <div class="loading-spinner"></div>
        </div>
    `;

    // Simulate API call
    setTimeout(() => {
        switch(section) {
            case 'dashboard':
                loadDashboardData();
                break;
            case 'products':
                loadProductsSection();
                break;
            case 'categories':
                loadCategoriesSection();
                break;
            case 'orders':
                loadOrdersSection();
                break;
            case 'tables':
                loadTablesSection();
                break;
            case 'chefs':
                loadChefsSection();
                break;
            case 'users':
                loadUsersSection();
                break;
            case 'settings':
                loadSettingsSection();
                break;
        }
    }, 800);
}

// Load Dashboard Data
function loadDashboardData() {
    // Fetch recent orders
    fetchRecentOrders();
    
    // Fetch popular products
    fetchPopularProducts();
    
    // Update statistics
    updateStatistics();
}

// Fetch Recent Orders
function fetchRecentOrders() {
    const recentOrders = [
        { id: 'ORD001', customer: 'John Doe', amount: '$45.00', status: 'Completed' },
        { id: 'ORD002', customer: 'Jane Smith', amount: '$32.50', status: 'Processing' },
        { id: 'ORD003', customer: 'Mike Johnson', amount: '$78.25', status: 'Pending' }
    ];

    const ordersTable = document.getElementById('recentOrdersTable');
    if (ordersTable) {
        ordersTable.innerHTML = recentOrders.map((order, index) => `
            <tr class="fade-in" style="animation-delay: ${index * 100}ms">
                <td>${order.id}</td>
                <td>${order.customer}</td>
                <td>${order.amount}</td>
                <td><span class="status ${order.status.toLowerCase()}">${order.status}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-icon" onclick="viewOrder('${order.id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-icon" onclick="editOrder('${order.id}')">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
}

// Fetch Popular Products
function fetchPopularProducts() {
    const popularProducts = [
        { 
            name: 'Margherita Pizza', 
            orders: 45, 
            rating: 4.5, 
            image: 'https://images.pexels.com/photos/825661/pexels-photo-825661.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
            price: 12.99
        },
        { 
            name: 'Caesar Salad', 
            orders: 38, 
            rating: 4.2, 
            image: 'https://images.pexels.com/photos/1211887/pexels-photo-1211887.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
            price: 8.99
        },
        { 
            name: 'Pasta Carbonara', 
            orders: 32, 
            rating: 4.7, 
            image: 'https://images.pexels.com/photos/1437267/pexels-photo-1437267.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
            price: 14.99
        },
        { 
            name: 'Chicken Wings', 
            orders: 29, 
            rating: 4.3, 
            image: 'https://images.pexels.com/photos/2338407/pexels-photo-2338407.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
            price: 10.99
        }
    ];

    const productsList = document.getElementById('popularProductsList');
    if (productsList) {
        productsList.innerHTML = popularProducts.map((product, index) => `
            <div class="menu-item fade-in" style="animation-delay: ${index * 100}ms">
                <img src="${product.image}" alt="${product.name}">
                <div class="menu-item-info">
                    <h3>${product.name}</h3>
                    <div class="price">$${product.price.toFixed(2)}</div>
                    <div class="description">
                        <i class="fas fa-star text-warning"></i> ${product.rating}
                        <span class="ms-2">(${product.orders} orders)</span>
                    </div>
                    <div class="menu-item-actions">
                        <button class="btn-primary">
                            <i class="fas fa-edit"></i>
                            Edit
                        </button>
                        <button class="btn-icon">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }
}

// Update Statistics
function updateStatistics() {
    const stats = {
        totalOrders: {
            total: 150,
            pending: 45,
            completed: 105
        },
        revenue: 12500,
        reservations: {
            total: 25,
            pending: 8
        },
        tables: {
            total: 12,
            reserved: 8,
            available: 4
        }
    };

    // Update the statistics cards with animations
    document.querySelectorAll('.stat-card').forEach((card, index) => {
        card.classList.add('fade-in');
        card.style.animationDelay = `${index * 100}ms`;
    });
}

// Initialize Charts
function initializeCharts() {
    // Common chart options
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    color: '#fff',
                    font: {
                        size: 12
                    }
                }
            }
        },
        scales: {
            x: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#fff'
                }
            },
            y: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#fff'
                }
            }
        }
    };

    // Orders Chart
    const ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx) {
        new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Orders',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    backgroundColor: '#FFD700',
                    borderRadius: 5,
                    borderWidth: 0
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Tables Chart
    const tablesCtx = document.getElementById('tablesChart');
    if (tablesCtx) {
        new Chart(tablesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Reserved', 'Available'],
                datasets: [{
                    data: [8, 4],
                    backgroundColor: ['#FFD700', '#2d2d2d']
                }]
            },
            options: {
                ...commonOptions,
                cutout: '70%',
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue',
                    data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                    borderColor: '#FFD700',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(255, 215, 0, 0.1)'
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Products Chart
    const productsCtx = document.getElementById('productsChart');
    if (productsCtx) {
        new Chart(productsCtx, {
            type: 'bar',
            data: {
                labels: ['Pizza', 'Salad', 'Pasta', 'Wings', 'Burger'],
                datasets: [{
                    label: 'Sales',
                    data: [45, 38, 32, 29, 25],
                    backgroundColor: '#FFD700',
                    borderRadius: 5,
                    borderWidth: 0
                }]
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}

// Helper Functions
function viewOrder(orderId) {
    const button = event.currentTarget;
    button.classList.add('bounce-in');
    setTimeout(() => button.classList.remove('bounce-in'), 600);
    console.log(`Viewing order: ${orderId}`);
}

function editOrder(orderId) {
    const button = event.currentTarget;
    button.classList.add('bounce-in');
    setTimeout(() => button.classList.remove('bounce-in'), 600);
    console.log(`Editing order: ${orderId}`);
}

// Section Loaders
function loadProductsSection() {
    // Implement products section
    console.log('Loading products section');
}

function loadCategoriesSection() {
    // Implement categories section
    console.log('Loading categories section');
}

function loadOrdersSection() {
    // Implement orders section
    console.log('Loading orders section');
}

function loadTablesSection() {
    // Implement tables section
    console.log('Loading tables section');
}

function loadChefsSection() {
    // Implement chefs section
    console.log('Loading chefs section');
}

function loadUsersSection() {
    // Implement users section
    console.log('Loading users section');
}

function loadSettingsSection() {
    // Implement settings section
    console.log('Loading settings section');
} 