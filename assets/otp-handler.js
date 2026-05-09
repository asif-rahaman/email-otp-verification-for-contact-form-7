document.addEventListener('DOMContentLoaded', function() {
    const sendBtn = document.getElementById('send-otp-btn');
    if (!sendBtn) return;

    // Create a message container and append it after the button
    const msgContainer = document.createElement('div');
    msgContainer.id = 'cf7-otp-response';
    msgContainer.style.marginTop = '10px';
    sendBtn.parentNode.insertBefore(msgContainer, sendBtn.nextSibling);

    sendBtn.addEventListener('click', function(e) {
        e.preventDefault();
        msgContainer.innerHTML = ''; // Clear previous messages
        
        const form = sendBtn.closest('form');
        const emailField = form.querySelector('input[type="email"]');
        const email = emailField ? emailField.value : '';

        if (!email || !email.includes('@')) {
            showOtpMsg(eov_cf7_obj.msg_invalid_email, 'error');
            return;
        }

        const originalText = sendBtn.innerText;
        sendBtn.innerText = eov_cf7_obj.msg_sending;
        sendBtn.disabled = true;

        const formData = new URLSearchParams();
        formData.append('action', 'send_cf7_otp');
        formData.append('email', email);
        formData.append('security', eov_cf7_obj.nonce);

        fetch(eov_cf7_obj.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showOtpMsg(data.data, 'success');
                startTimer(60);
            } else {
                showOtpMsg(data.data, 'error');
                sendBtn.innerText = originalText;
                sendBtn.disabled = false;
            }
        })
        .catch(() => {
            sendBtn.disabled = false;
            sendBtn.innerText = originalText;
        });
    });

    function showOtpMsg(text, type) {
        const color = type === 'success' ? '#008a20' : '#dc3232';
        msgContainer.innerHTML = `<span style="color: ${color}; font-size: 0.9em; font-weight: bold;">${text}</span>`;
    }

    function startTimer(seconds) {
        let timeLeft = seconds;
        const timer = setInterval(() => {
            timeLeft--;
            sendBtn.innerText = `${eov_cf7_obj.msg_wait} ${timeLeft}s`;
            if (timeLeft <= 0) {
                clearInterval(timer);
                sendBtn.innerText = eov_cf7_obj.msg_resend;
                sendBtn.disabled = false;
            }
        }, 1000);
    }
});