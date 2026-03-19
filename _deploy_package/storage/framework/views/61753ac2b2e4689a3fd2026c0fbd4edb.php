<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <h1 class="form-title">LOGIN</h1>
    <p class="form-subtitle">Welcome back! Please sign in to your account.</p>

    <?php if(session('status')): ?>
        <div class="status-message">
            <?php echo e(session('status')); ?>

        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('login')); ?>">
        <?php echo csrf_field(); ?>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" 
                   class="form-input" 
                   type="email" 
                   name="email" 
                   value="<?php echo e(old('email')); ?>" 
                   required 
                   autofocus 
                   autocomplete="username" 
                   placeholder="Enter your Email here">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="form-error"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                    <img id="eye-open" src="<?php echo e(asset('images/icons/view.png')); ?>" alt="Show password" width="20" height="20">
                    <img id="eye-closed" src="<?php echo e(asset('images/icons/hide.png')); ?>" alt="Hide password" width="20" height="20" style="display: none;">
                </button>
            </div>
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="form-error"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

        <?php if(Route::has('password.request')): ?>
            <div class="forgot-password">
                <a href="<?php echo e(route('password.request')); ?>">Forgot your password?</a>
            </div>
        <?php endif; ?>

        <div class="auth-links">
            Don't have an account? 
            <a href="<?php echo e(route('register')); ?>" class="auth-link">Create one here</a>
        </div>
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\Web Developement\JCES-PTA and Project Management System\resources\views/auth/login.blade.php ENDPATH**/ ?>