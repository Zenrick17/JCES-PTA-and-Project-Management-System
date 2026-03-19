<x-guest-layout>
    <h1 class="form-title">LOGIN</h1>
    <p class="form-subtitle">Welcome back! Please sign in to your account.</p>

    @if (session('status'))
        <div class="status-message">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" 
                   class="form-input" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus 
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
                       autocomplete="current-password"
                       placeholder="Enter your Password">
                <button type="button" class="password-toggle" onclick="togglePassword()">
                    <img id="eye-open" src="{{ asset('images/icons/view.png') }}" alt="Show password" width="20" height="20">
                    <img id="eye-closed" src="{{ asset('images/icons/hide.png') }}" alt="Hide password" width="20" height="20" style="display: none;">
                </button>
            </div>
            @error('password')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="checkbox-group">
            <input id="remember_me" 
                   type="checkbox" 
                   class="checkbox" 
                   name="remember">
            <label for="remember_me" class="checkbox-label">Remember me</label>
        </div>

        <button type="submit" class="btn-primary">
            Login
        </button>

        @if (Route::has('password.request'))
            <div class="forgot-password">
                <a href="{{ route('password.request') }}">Forgot your password?</a>
            </div>
        @endif

    </form>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            
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
