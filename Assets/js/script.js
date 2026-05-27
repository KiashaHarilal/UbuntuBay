// UbuntuBay Main JavaScript

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    
    // Mobile menu toggle
    const menuToggle = document.querySelector('.navbar-toggler');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('show');
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#ff8a92';
                    isValid = false;
                } else {
                    field.style.borderColor = 'rgba(255,255,255,0.15)';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showAlert('Please fill in all required fields', 'danger');
            }
        });
    });
    
    // Password confirmation validation
    const passwordForm = document.querySelector('form[action*="change_password"]');
    if (passwordForm) {
        const newPassword = passwordForm.querySelector('input[name="new_password"]');
        const confirmPassword = passwordForm.querySelector('input[name="confirm_password"]');
        
        if (newPassword && confirmPassword) {
            passwordForm.addEventListener('submit', function(e) {
                if (newPassword.value !== confirmPassword.value) {
                    e.preventDefault();
                    showAlert('New passwords do not match!', 'danger');
                } else if (newPassword.value.length < 6) {
                    e.preventDefault();
                    showAlert('Password must be at least 6 characters', 'danger');
                }
            });
        }
    }
    
    // Product image preview (for sell page)
    const imageInput = document.getElementById('product-images');
    const imagePreview = document.getElementById('image-preview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function(e) {
            imagePreview.innerHTML = '';
            const files = Array.from(this.files);
            
            if (files.length > 5) {
                showAlert('Maximum 5 images allowed', 'danger');
                this.value = '';
                return;
            }
            
            files.forEach(file => {
                if (!file.type.match('image.*')) {
                    showAlert('Only image files are allowed', 'danger');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.style.width = '80px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '10px';
                    img.style.margin = '5px';
                    imagePreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    }
    
    // Price formatting
    const priceInputs = document.querySelectorAll('input[type="number"][step="0.01"]');
    priceInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
    
    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('.delete-btn, [onclick*="confirm"]');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) alert.remove();
            }, 300);
        }, 5000);
    });
});

// Helper function to show alerts
function showAlert(message, type) {
    // Check if alert already exists
    const existingAlert = document.querySelector('.custom-alert');
    if (existingAlert) existingAlert.remove();
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `custom-alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.background = type === 'danger' ? 'rgba(220,53,69,0.95)' : 'rgba(40,167,69,0.95)';
    alertDiv.style.color = 'white';
    alertDiv.style.padding = '12px 24px';
    alertDiv.style.borderRadius = '12px';
    alertDiv.style.backdropFilter = 'blur(10px)';
    alertDiv.style.fontWeight = '500';
    alertDiv.style.boxShadow = '0 4px 15px rgba(0,0,0,0.2)';
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => {
            if (alertDiv.parentNode) alertDiv.remove();
        }, 300);
    }, 5000);
}

// Smooth scroll to top
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Add scroll to top button
window.addEventListener('scroll', function() {
    const scrollBtn = document.getElementById('scrollToTop');
    if (scrollBtn) {
        if (window.scrollY > 300) {
            scrollBtn.style.display = 'flex';
        } else {
            scrollBtn.style.display = 'none';
        }
    }
});

// Delivery method toggle (show/hide address fields)
function toggleDeliveryMethod() {
    const courierRadio = document.querySelector('input[name="delivery_method"][value="courier"]');
    const addressSection = document.getElementById('address-section');
    
    if (courierRadio && addressSection) {
        if (courierRadio.checked) {
            addressSection.style.display = 'block';
        } else {
            addressSection.style.display = 'none';
        }
    }
}

// Filter products by price range
function filterByPrice() {
    const minPrice = document.getElementById('min-price').value;
    const maxPrice = document.getElementById('max-price').value;
    const url = new URL(window.location.href);
    
    if (minPrice) url.searchParams.set('min_price', minPrice);
    if (maxPrice) url.searchParams.set('max_price', maxPrice);
    
    window.location.href = url.toString();
}