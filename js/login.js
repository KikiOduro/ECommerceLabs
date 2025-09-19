
$(function () {
    $('#login-form').on('submit', function (e) {
      e.preventDefault();
  
      let email = $('#email').val().trim();
      let password = $('#password').val();
  
      if (!email || !password) {
        Swal.fire('Oops...', 'Email and password are required.', 'error');
        return;
      }
  
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        Swal.fire('Oops...', 'Please enter a valid email address.', 'error');
        return;
      }
  
      let $btn = $('#loginBtn').prop('disabled', true).text('Logging in...');
  
      $.post('../actions/login_customer.php', { email, password }, function (res) {
        if (res.status === 'success') {
          Swal.fire('Success', res.message, 'success').then(() => {
            window.location.href = '../index.php';
          });
        } else {
          Swal.fire('Oops...', res.message || 'Invalid login.', 'error');
        }
      }, 'json')
      .fail(() => {
        Swal.fire('Error', 'Server error. Please try again later.', 'error');
      })
      .always(() => {
        $btn.prop('disabled', false).text('Login');
      });
    });
  });
  