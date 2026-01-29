document.addEventListener('DOMContentLoaded', function() {
    // Handle login form submission
    const loginForm = document.querySelector('form[action="user_auth.php"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const username = loginForm.username.value;
            const password = loginForm.password.value;
            const formData = new FormData();
            formData.append('username', username);
            formData.append('password', password);
            formData.append('login', true);

            fetch('user_auth.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                if (result.includes('Login successful')) {
                    window.location.href = 'dashboard.php';
                } else {
                    alert('Invalid credentials');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // Handle register form submission
    const registerForm = document.querySelector('form[action="user_auth.php"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const username = registerForm.username.value;
            const password = registerForm.password.value;
            const formData = new FormData();
            formData.append('username', username);
            formData.append('password', password);
            formData.append('register', true);

            fetch('user_auth.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                alert(result);
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // Handle adding equipment form
    const addEquipmentForm = document.querySelector('#add-equipment-form');
    if (addEquipmentForm) {
        addEquipmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = addEquipmentForm.name.value;
            const category = addEquipmentForm.category.value;
            const status = addEquipmentForm.status.value;
            const formData = new FormData();
            formData.append('name', name);
            formData.append('category', category);
            formData.append('status', status);
            formData.append('add_equipment', true);

            fetch('equipment_management.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                alert(result);
                location.reload();
            })
            .catch(error => console.error('Error:', error));
        });
    }
});
