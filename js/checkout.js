/* /js/checkout.js */
$(function () {
    const PROCESS_URL = window.CHECKOUT_ENDPOINT || 'actions/process_checkout_action.php';
  
    // Expected button on checkout.php:
    //   <button id="simulate-pay" class="btn">Simulate Payment</button>
    $('#simulate-pay').on('click', function () {
      Swal.fire({
        icon: 'question',
        title: 'Simulate Payment',
        text: 'Confirm that you have paid the total shown on this page.',
        showCancelButton: true,
        confirmButtonText: "Yes, I've paid",
        cancelButtonText: 'Cancel'
      }).then(r => {
        if (!r.isConfirmed) return;
        doProcess();
      });
    });
  
    function doProcess() {
      Swal.showLoading();
      $.post(PROCESS_URL, {}, function (res) {
        Swal.close();
        if (res && res.status === 'success') {
          const ref = (res.reference || '').toString();
          const orderId = (res.order_id || '').toString();
          Swal.fire({
            icon: 'success',
            title: 'Payment Confirmed',
            html: `
              <p>Thank you! Your order has been placed.</p>
              <p><strong>Order ID:</strong> ${orderId}</p>
              <p><strong>Reference:</strong> ${ref}</p>
            `,
            confirmButtonText: 'View Orders'
          }).then(() => {
            // Redirect to a simple orders page if you have one; otherwise back to all products
            const dest = window.AFTER_CHECKOUT_URL || 'all_product.php';
            window.location.href = dest;
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Payment Failed',
            text: (res && res.message) || 'Could not complete checkout'
          });
        }
      }, 'json').fail(() => {
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed' });
      });
    }
  });
  