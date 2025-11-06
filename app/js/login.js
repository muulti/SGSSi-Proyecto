// Handle navigation links
        document.querySelectorAll('.links a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                window.location.href = href.replace('.php', '');
            });
        });
        
        const loginForm = document.getElementById('login_form');
        const loginMessages = document.getElementById('login_messages');
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            loginMessages.innerHTML = '';
            const data = new FormData(loginForm);
            try {
                    // Usar URL absoluta para evitar problemas si la página está en /login (sin .php)
                    const actionUrl = new URL(loginForm.action, window.location.href).href;
                    const resp = await fetch(actionUrl, {
                        method: 'POST',
                        body: data
                    });
                const json = await resp.json();
                if (json.status === 'ok') {
                    // Eliminar la extensión .php de la redirección
                    window.location.href = json.redirect.replace('.php', '');
                } else {
                    loginMessages.innerHTML = '<div class="alert">' + json.message + '</div>';
                }
            } catch (err) {
                loginMessages.innerHTML = '<div class="alert">Error de conexión</div>';
            }
        });

