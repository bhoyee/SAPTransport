           
            <!-- ========== Form Section ========== -->
            <section>
                <h2 class="text-center mb-3">Login</h2>
                <form>
                    <!-- Email Field -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter your email" required>
                    </div>
                    
                    <!-- Password Field -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" placeholder="Enter your password" required>
                    </div>
                    
                    <!-- Login Button -->
                    <button type="submit" class="btn disabled-btn w-100 mb-3" id="loginBtn" disabled>Login</button>
                    
                    <!-- Social Login Buttons -->
                    <div class="text-center mb-3">
                        <p>Or login with:</p>
                        <button type="button" class="btn btn-danger w-100 mb-2">Login with Google</button>
                        <button type="button" class="btn btn-primary w-100">Login with Facebook</button>
                    </div>
                    
                    <!-- Forgot Password and Sign Up Links -->
                    <p class="text-center"><a href="{{ url('forgot-password') }}">Forgot Password?</a></p>
                    <p class="text-center">Don't have an account? <a href="{{ url('register') }}">Sign Up</a></p>
                </form>
            </section>