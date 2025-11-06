// Handle navigation links
        document.querySelectorAll('.links a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                window.location.href = href.replace('.php', '');
            });
        });

        // Attach submit handler to use client-side validation and AJAX posting
const regForm = document.getElementById('register_form');
        const messagesDiv = document.getElementById('messages');
        regForm.addEventListener('submit', async function(e){
            e.preventDefault();
            messagesDiv.innerHTML = '';
            
            try {
                if (!validarRegistro()) return;
                
                const data = new FormData(regForm);
                const actionUrl = new URL(regForm.action, window.location.href).href;
                const resp = await fetch(actionUrl, { method: 'POST', body: data });
                const json = await resp.json();
                
                if (json.status === 'ok') {
                    messagesDiv.innerHTML = '<div class="alert success">' + json.message + '</div>';
                    if (json.redirect) {
                        window.location.href = json.redirect.replace('.php', '');
                    } else {
                        regForm.reset();
                    }
                } else {
                    const errorMessage = json.errors ? json.errors.join('<br>') : json.message;
                    messagesDiv.innerHTML = '<div class="alert">' + errorMessage + '</div>';
                }
            } catch (error) {
                console.error('Error:', error);
                messagesDiv.innerHTML = '<div class="alert">Error al procesar la solicitud. Por favor, inténtalo de nuevo.</div>';
            }
        });
