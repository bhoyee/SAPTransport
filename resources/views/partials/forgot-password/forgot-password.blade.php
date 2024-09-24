
        <section>
            <h2 class="text-center mb-3">Forgot Password</h2>
            <form>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Enter your email" required>
                </div>
                <button type="submit" class="btn disabled-btn w-100 mb-3" id="resetBtn" disabled>Reset Password</button>
                <p class="text-center">Remembered your password? <a href="{{ url('login') }}">Login</a></p>

            </form>
        </section>

        <script src="{{ asset('js/forgot-password.js') }}"></script>
