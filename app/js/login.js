// Handle navigation links
        document.querySelectorAll('.links a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                window.location.href = href.replace('.php', '');
            });
        });
        
        let countdownInterval = null;
        
        function startCountdown(seconds) {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            const loginMessages = document.getElementById('login_messages');
            let remainingSeconds = seconds;
            
            function updateDisplay() {
                if (remainingSeconds <= 0) {
                    clearInterval(countdownInterval);
                    loginMessages.innerHTML = '<div class="alert" style="background: #4CAF50;">Ya puedes intentar de nuevo</div>';
                    setTimeout(() => {
                        loginMessages.innerHTML = '';
                    }, 2000);
                    return;
                }
                
                const minutes = Math.floor(remainingSeconds / 60);
                const secs = remainingSeconds % 60;
                const timeStr = minutes > 0 ? `${minutes}:${secs.toString().padStart(2, '0')}` : `${secs}s`;
                
                loginMessages.innerHTML = `<div class="alert" style="background: #f44336;">
                    <strong>Cuenta bloqueada</strong><br>
                    Tiempo restante: <strong>${timeStr}</strong>
                </div>`;
                
                remainingSeconds--;
            }
            updateDisplay();
            countdownInterval = setInterval(updateDisplay, 1000);
        }
        
        const loginForm = document.getElementById('login_form');
        const loginMessages = document.getElementById('login_messages');
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            loginMessages.innerHTML = '';
            
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            const data = new FormData(loginForm);
            try {
                    const actionUrl = new URL(loginForm.action, window.location.href).href;
                    const resp = await fetch(actionUrl, {
                        method: 'POST',
                        body: data
                    });
                const json = await resp.json();
                if (json.status === 'ok') {
                    window.location.href = json.redirect.replace('.php', '');
                } else {
                    if (json.bloqueado && json.tiempo_restante) {
                        startCountdown(json.tiempo_restante);
                    } else {
                        loginMessages.innerHTML = '<div class="alert">' + json.message + '</div>';
                    }
                }
            } catch (err) {
                loginMessages.innerHTML = '<div class="alert">Error de conexión</div>';
            }
        });

