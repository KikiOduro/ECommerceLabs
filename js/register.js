// register.js
// Requires jQuery + SweetAlert2

$(document).ready(function () {
  $('#register-form').submit(function (e) {
    e.preventDefault();

    var name = $('#name').val();
    var email = $('#email').val();
    var password = $('#password').val();
    var country = $('#country').val();
    var city = $('#city').val();
    var phone_number = $('#phone_number').val();
    var role = $('#role').val(); 

    if (
      !name || !email || !password ||
      !country || !city || !phone_number
    ) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Please fill in all fields!',
      });
      return;
    }

    if (
      password.length < 6 ||
      !/[a-z]/.test(password) ||
      !/[A-Z]/.test(password) ||
      !/[0-9]/.test(password)
    ) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Password must be at least 6 characters long and contain at least one lowercase letter, one uppercase letter, and one number!',
      });
      return;
    }


    var formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('country', country);
    formData.append('city', city);
    formData.append('phone_number', phone_number);
    formData.append('role', role);


    var imgInput = $('#image')[0] ?? null;
    if (imgInput && imgInput.files && imgInput.files[0]) {
      formData.append('image', imgInput.files[0]);
    }

    $.ajax({
      url: '../actions/register_user.php',
      type: 'POST',
      data: formData,
      processData: false,    
      contentType: false,      
      dataType: 'json',        
      success: function (response) {
        if (response.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: response.message,
          }).then(function (result) {
            if (result.isConfirmed) {
              window.location.href = 'login.php';
            }
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: response.message || 'Failed to register.',
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'An error occurred! Please try again later.',
        });
      }
    });
  });
});
