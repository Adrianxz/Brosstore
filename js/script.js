document.getElementById('get-started').addEventListener('click', function(event) {
    event.preventDefault();

    document.getElementById('form-title').innerText = 'Create Account';
    document.getElementById('name-fields').style.display = 'block';
    document.getElementById('confirm-password-field').style.display = 'block';
    document.getElementById('remember-me-container').style.display = 'none';
    document.getElementById('submit-btn').innerText = 'Register';
    document.getElementById('forgot-password-link').style.display = 'none';
    document.getElementById('back-to-login').style.display = 'block';
});

document.getElementById('back-to-login').addEventListener('click', function(event) {
    event.preventDefault();

    document.getElementById('form-title').innerText = 'Account Login';
    document.getElementById('name-fields').style.display = 'none';
    document.getElementById('confirm-password-field').style.display = 'none';
    document.getElementById('remember-me-container').style.display = 'block';
    document.getElementById('submit-btn').innerText = 'Log in';
    document.getElementById('forgot-password-link').style.display = 'block';
    document.getElementById('back-to-login').style.display = 'none';
});
