<x-guest-layout>
    <h1 class="form-title">REGISTER</h1>
    <p class="form-subtitle">Create your account to get started.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input id="name" 
                   class="form-input" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Enter your full name">
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" 
                   class="form-input" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="username"
                   placeholder="Enter your Email here">
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="password-container">
                <input id="password" 
                       class="form-input" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="new-password"
                       placeholder="Create a strong password">
                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                    <img class="eye-open" src="{{ asset('images/icons/view.png') }}" alt="Show password" width="20" height="20">
                    <img class="eye-closed" src="{{ asset('images/icons/hide.png') }}" alt="Hide password" width="20" height="20" style="display: none;">
                </button>
            </div>
            @error('password')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="password-container">
                <input id="password_confirmation" 
                       class="form-input" 
                       type="password" 
                       name="password_confirmation" 
                       required 
                       autocomplete="new-password"
                       placeholder="Confirm your password">
                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                    <img class="eye-open" src="{{ asset('images/icons/view.png') }}" alt="Show password" width="20" height="20">
                    <img class="eye-closed" src="{{ asset('images/icons/hide.png') }}" alt="Hide password" width="20" height="20" style="display: none;">
                </button>
            </div>
            @error('password_confirmation')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn-primary">
            Register
        </button>

        <div class="auth-links">
            Already have an account? 
            <a href="{{ route('login') }}" class="auth-link">Sign in here</a>
        </div>
    </form>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleButton = passwordField.nextElementSibling;
            const eyeOpen = toggleButton.querySelector('.eye-open');
            const eyeClosed = toggleButton.querySelector('.eye-closed');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'inline';
            } else {
                passwordField.type = 'password';
                eyeOpen.style.display = 'inline';
                eyeClosed.style.display = 'none';
            }
        }
    </script>
</x-guest-layout>
