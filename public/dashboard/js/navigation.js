// Navigation Handler
document.addEventListener('DOMContentLoaded', () => {
    // Get all navigation links
    const navLinks = document.querySelectorAll('.nav-menu a[data-section]');
    
    // Add click event listener to each link
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get the target section
            const targetSection = this.getAttribute('data-section');
            
            // Update active state
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Handle navigation based on section
            handleNavigation(targetSection);
        });
    });

    // Set active section on page load
    setActiveSection();
});

// Navigation Handler Function
function handleNavigation(section) {
    switch(section) {
        case 'dashboard':
            window.location.href = 'index.html';
            break;
            
        case 'products':
            window.location.href = 'products.html';
            break;
            
        case 'categories':
            window.location.href = 'categories.html';
            break;
            
        case 'orders':
            window.location.href = 'orders.html';
            break;
            
        case 'tables':
            window.location.href = 'tables.html';
            break;
            
        case 'chefs':
            window.location.href = 'chefs.html';
            break;
            
        case 'users':
            window.location.href = 'users.html';
            break;
            
        case 'settings':
            window.location.href = 'settings.html';
            break;
    }
}

// Set Active Section on Page Load
function setActiveSection() {
    // Get current page filename
    const currentPage = window.location.pathname.split('/').pop();
    
    // Map page to section
    const pageToSection = {
        'index.html': 'dashboard',
        'products.html': 'products',
        'categories.html': 'categories',
        'orders.html': 'orders',
        'tables.html': 'tables',
        'chefs.html': 'chefs',
        'users.html': 'users',
        'settings.html': 'settings'
    };
    
    // Get current section
    const currentSection = pageToSection[currentPage];
    
    if (currentSection) {
        // Find and activate the corresponding nav link
        const activeLink = document.querySelector(`.nav-menu a[data-section="${currentSection}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }
}

// Mobile Sidebar Toggle
document.addEventListener('DOMContentLoaded', () => {
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebar = document.querySelector('.sidebar');
    
    if (toggleSidebar && sidebar) {
        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !toggleSidebar.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    }
}); 