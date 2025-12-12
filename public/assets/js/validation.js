// Form validation functions
class FormValidator {
    // Email validation with regex
    static validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    static validatePassword(password) {
    // Regex: at least 1 uppercase, 1 lowercase, 1 number, 1 special character, min 6 chars
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
    return regex.test(password);
    }

    

    // Name validation (letters and spaces only)
    static validateName(name) {
        const regex = /^[A-Za-z\s]+$/;
        return regex.test(name) && name.length >= 2;
    }

    // Phone validation (basic international format)
    static validatePhone(phone) {
        const regex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        return regex.test(phone) || phone === '';
    }

    // Price validation (positive number)
    static validatePrice(price) {
        return parseFloat(price) > 0;
    }

    // Show error message
    static showError(input, message) {
        const formGroup = input.closest('.form-group');
        let errorElement = formGroup.querySelector('.error-message');
        
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            formGroup.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
        input.style.borderColor = '#dc3545';
    }

    // Clear error message
    static clearError(input) {
        const formGroup = input.closest('.form-group');
        const errorElement = formGroup.querySelector('.error-message');
        
        if (errorElement) {
            errorElement.remove();
        }
        
        input.style.borderColor = '#e9ecef';
    }

    // Validate registration form
    static validateRegistrationForm() {
        const email = document.querySelector('input[name="email"]');
        const password = document.querySelector('input[name="password"]');
        const firstName = document.querySelector('input[name="first_name"]');
        const lastName = document.querySelector('input[name="last_name"]');
        const phone = document.querySelector('input[name="phone"]');

        let isValid = true;

        // Clear previous errors
        [email, password, firstName, lastName, phone].forEach(input => {
            this.clearError(input);
        });

        // Validate email
        if (!this.validateEmail(email.value)) {
            this.showError(email, 'Please enter a valid email address');
            isValid = false;
        }

        // Validate password
        if (!this.validatePassword(password.value)) {
            this.showError(password, 'Password must include: uppercase, lowercase, number, and special character (@$!%*?&)');
            isValid = false;
        }

        

        // Validate first name
        if (!this.validateName(firstName.value)) {
            this.showError(firstName, 'Please enter a valid first name (letters only)');
            isValid = false;
        }

        // Validate last name
        if (!this.validateName(lastName.value)) {
            this.showError(lastName, 'Please enter a valid last name (letters only)');
            isValid = false;
        }

        // Validate phone (optional)
        if (phone.value && !this.validatePhone(phone.value)) {
            this.showError(phone, 'Please enter a valid phone number');
            isValid = false;
        }

        return isValid;
    }

    

    // Validate product form (admin)
    static validateProductForm() {
        const name = document.querySelector('input[name="name"]');
        const price = document.querySelector('input[name="base_price"]');
        const description = document.querySelector('textarea[name="description"]');

        let isValid = true;

        [name, price, description].forEach(input => {
            this.clearError(input);
        });

        // Validate product name
        if (name.value.trim().length < 2) {
            this.showError(name, 'Product name must be at least 2 characters long');
            isValid = false;
        }

        // Validate price
        if (!this.validatePrice(price.value)) {
            this.showError(price, 'Please enter a valid price (greater than 0)');
            isValid = false;
        }

        // Validate description
        if (description.value.trim().length < 10) {
            this.showError(description, 'Description must be at least 10 characters long');
            isValid = false;
        }

        return isValid;
    }
}

// Initialize validation when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Registration form validation
    const registerForm = document.querySelector('form[action*="register"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            if (!FormValidator.validateRegistrationForm()) {
                e.preventDefault();
            }
        });
    }

    // Product form validation (admin)
    const productForm = document.querySelector('form[action*="admin-add-product"]');
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            if (!FormValidator.validateProductForm()) {
                e.preventDefault();
            }
        });
    }

    // Real-time validation for better UX
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            // Add real-time validation based on input type
            if (this.name === 'email' && this.value) {
                if (!FormValidator.validateEmail(this.value)) {
                    FormValidator.showError(this, 'Please enter a valid email');
                } else {
                    FormValidator.clearError(this);
                }
            }
        });

        input.addEventListener('input', function() {
            FormValidator.clearError(this);
        });
    });
});