<?php include __DIR__ . '/../header.php'; ?>

<div class="auth-container">
    <h2>🔐 Login to RafiaShop</h2>
    
    <!-- Messages will be shown here by JavaScript -->
    <div id="login-messages"></div>
    
    <form id="login-form" class="auth-form">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required placeholder="Enter your email">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required placeholder="Enter your password">
        </div>
        
        <button type="submit" class="btn-primary" id="login-btn">Login</button>
    </form>
    
    <div class="auth-links">
        <a href="?page=forgot-password">🔓 Forgot Password?</a>
        <a href="?page=register">📝 Don't have an account? Register</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const loginBtn = document.getElementById('login-btn');
    const messagesDiv = document.getElementById('login-messages');
    
    // ✅ ADD VALIDATION FUNCTIONS HERE
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function validatePassword(password) {
        return password.length >= 6;
    }

    function showMessage(message, type) {
        messagesDiv.innerHTML = `
            <div class="${type}">
                ${message}
            </div>
        `;
        
        // Scroll to message
        messagesDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    // ✅ ADD REAL-TIME VALIDATION
    document.getElementById('email').addEventListener('blur', function() {
        if (this.value && !validateEmail(this.value)) {
            this.classList.add('input-error');
            showMessage('Please enter a valid email address', 'error');
        } else {
            this.classList.remove('input-error');
            this.classList.add('input-success');
            messagesDiv.innerHTML = ''; // Clear error message
        }
    });

    document.getElementById('password').addEventListener('blur', function() {
        if (this.value && !validatePassword(this.value)) {
            this.classList.add('input-error');
            showMessage('Password must be at least 6 characters', 'error');
        } else {
            this.classList.remove('input-error');
            this.classList.add('input-success');
            messagesDiv.innerHTML = ''; // Clear error message
        }
    });
    
    // Login form submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(loginForm);
        const email = formData.get('email');
        const password = formData.get('password');
        
        // Validate inputs
        if (!email || !password) {
            showMessage('Please fill in all fields', 'error');
            return;
        }
        
        if (!validateEmail(email)) {
            showMessage('Please enter a valid email address', 'error');
            document.getElementById('email').classList.add('input-error');
            return;
        }
        
        if (!validatePassword(password)) {
            showMessage('Password must be at least 6 characters', 'error');
            document.getElementById('password').classList.add('input-error');
            return;
        }
        
        // Disable button and show loading
        loginBtn.disabled = true;
        loginBtn.innerHTML = 'Logging in... ⌛';
        
        // Clear previous messages
        messagesDiv.innerHTML = '';
        
        // Send AJAX request WITH DEBUG
        fetch('?page=ajax-login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                email: email,
                password: password
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
        
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.log('Non-JSON response:', text);
                    throw new Error('Server returned non-JSON response: ' + text.substring(0, 100));
                });
            }
        
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
        
            if (data.success) {
                showMessage('Login successful! Redirecting...', 'success');
            
                setTimeout(() => {
                    window.location.href = data.redirect || '?page=user-dashboard';
                }, 1500);
            } else {
                showMessage(data.error || 'Login failed', 'error');
                loginBtn.disabled = false;
                loginBtn.innerHTML = 'Login';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showMessage('Error: ' + error.message, 'error');
            loginBtn.disabled = false;
            loginBtn.innerHTML = 'Login';
        });
    });
    
    // Enter key support
    document.getElementById('password').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            loginForm.dispatchEvent(new Event('submit'));
        }
    });
});
</script>

<?php include __DIR__ . '/../footer.php'; ?>