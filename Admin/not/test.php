<!-- HTML login form -->
<div class="container p-5 my-5 bg-dark text-white">
        <h3>Login Page</h3>
        <form action="login.php" method="post">
            <label for="username" class="l1">Username:</label>
            <input type="text" name="username" required>

            <label for="password" class="l1">Password:</label>
            <input type="password" name="password" required>

            <label for="role" class="l1">Role:</label>
            <select name="role" required>
                <option value="manager">Manager</option>
                <option value="admin">Admin</option>
                <option value="mechanic">Mechanic</option>
            </select>

            <button type="submit" name="login_btn" class="btn btn-primary">Login</button>
        </form>
    </div>