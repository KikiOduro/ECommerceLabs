/* /js/cart.js */
$(function () {
    // ---------- CONFIG ----------
    // If your cart.php sits at the site root, these paths are correct.
    // If your JS runs from a subdir, you can override on each page:
    //   window.CART_ENDPOINTS = {...}
    const EP = window.CART_ENDPOINTS || {
      add:    'actions/add_to_cart_action.php',
      remove: 'actions/remove_from_cart_action.php',
      update: 'actions/update_quantity_action.php',
      empty:  'actions/empty_cart_action.php',
      fetch:  'actions/get_cart_action.php'   // implement a tiny fetch endpoint that returns cart JSON
    };
  
    // If your public path uses a user path like /~akua.oduro, set it here once:
    const PUBLIC_PREFIX = window.PUBLIC_PREFIX || '/~akua.oduro/';
  
    // ---------- HELPERS ----------
    function toastOK(title, text) {
      return Swal.fire({ icon: 'success', title, text, timer: 1200, showConfirmButton: false });
    }
    function toastError(title, text) {
      return Swal.fire({ icon: 'error', title, text: text || 'Something went wrong' });
    }
    function escapeHtml(s) {
      return (s || '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    }
    function imgUrl(p) {
      if (!p) return 'https://via.placeholder.com/64';
      if (/^https?:\/\//i.test(p)) return p;
      return PUBLIC_PREFIX + p.replace(/^\/+/, ''); // prepend your live path
    }
  
    // ---------- ADD TO CART (global handler for product cards/buttons) ----------
    // Expected markup: <button class="add-to-cart" data-id="PRODUCT_ID" data-qty="1">Add</button>
    // Optional: if you place an <input type="number" class="qty-input"> next to the button, we’ll read that too.
    $(document).on('click', '.add-to-cart', function (e) {
      e.preventDefault();
      const $btn = $(this);
      const pid = parseInt($btn.data('id') || '0', 10);
      if (!pid) return toastError('Error', 'No product selected');
  
      // prefer a nearby qty input if present
      let qty = parseInt($btn.data('qty') || '1', 10);
      const $nearQty = $btn.closest('[data-product]').find('.qty-input');
      if ($nearQty.length) {
        qty = parseInt($nearQty.val() || '1', 10);
      }
      qty = Math.max(1, qty);
  
      $.post(EP.add, { product_id: pid, qty }, function (res) {
        if (res && res.status === 'success') {
          toastOK('Added', 'Item added to cart');
          refreshCartBadge(); // update small badge if present
        } else {
          toastError('Error', (res && res.message) || 'Could not add item');
        }
      }, 'json').fail(() => toastError('Error', 'Request failed'));
    });
  
    // ---------- CART PAGE RENDER ----------
    // Expected containers on cart.php:
    //   #cart-items         -> list/grid for rows
    //   #cart-subtotal      -> number
    //   #cart-total         -> number (same as subtotal unless you later add shipping/tax)
    // Buttons/inputs inside each row:
    //   .qty       (input[type=number]) with data-cart-id
    //   .btn-update (button) with data-cart-id and data-qty-source="#idOfQtyInput" (optional)
    //   .btn-remove (button) with data-cart-id
    // Page-level buttons:
    //   #empty-cart
    function loadCart() {
      const $wrap = $('#cart-items');
      if (!$wrap.length) return; // not on cart page
  
      $.getJSON(EP.fetch, function (res) {
        if (!res || res.status !== 'success') {
          $wrap.html('<p class="muted">Could not load cart.</p>');
          return;
        }
        renderCart(res.data || []);
      }).fail(() => {
        $('#cart-items').html('<p class="muted">Could not load cart.</p>');
      });
    }
  
    function renderCart(items) {
      const $wrap = $('#cart-items');
      $wrap.empty();
  
      if (!items || !items.length) {
        $wrap.html('<p>Your cart is empty.</p>');
        $('#cart-subtotal').text('0.00');
        $('#cart-total').text('0.00');
        refreshCartBadgeWith(0);
        return;
      }
  
      let subtotal = 0;
      let totalQty = 0;
  
      items.forEach(it => {
        const price = Number(it.product_price || 0);
        const qty   = Number(it.qty || 0);
        const line  = price * qty;
        subtotal   += line;
        totalQty   += qty;
  
        // Ensure we have cart_id & product_id from your fetch JSON (see note below)
        const cartId = it.cart_id; 
        const title  = escapeHtml(it.product_title);
        const img    = imgUrl(it.product_image);
  
        $wrap.append(`
          <div class="cart-row" data-cart-id="${cartId}">
            <img class="thumb" src="${img}" alt="">
            <div class="info">
              <div class="title">${title}</div>
              <div class="price">GHS ${price.toFixed(2)}</div>
            </div>
            <div class="qty-wrap">
              <input type="number" class="qty" min="1" value="${qty}" data-cart-id="${cartId}">
              <button class="btn btn-update" data-cart-id="${cartId}">Update</button>
            </div>
            <div class="line-total">GHS ${line.toFixed(2)}</div>
            <button class="btn btn-remove" data-cart-id="${cartId}">Remove</button>
          </div>
        `);
      });
  
      $('#cart-subtotal').text(subtotal.toFixed(2));
      $('#cart-total').text(subtotal.toFixed(2));
      refreshCartBadgeWith(totalQty);
    }
  
    // ---------- UPDATE QTY ----------
    $(document).on('click', '.btn-update', function () {
      const cartId = parseInt($(this).data('cart-id') || '0', 10);
      if (!cartId) return;
      const qty = Math.max(1, parseInt($(`.qty[data-cart-id="${cartId}"]`).val() || '1', 10));
  
      $.post(EP.update, { cart_id: cartId, qty }, function (res) {
        if (res && res.status === 'success') {
          toastOK('Updated', 'Quantity updated');
          loadCart();
        } else {
          toastError('Error', (res && res.message) || 'Update failed');
        }
      }, 'json').fail(() => toastError('Error', 'Request failed'));
    });
  
    // ---------- REMOVE ITEM ----------
    $(document).on('click', '.btn-remove', function () {
      const cartId = parseInt($(this).data('cart-id') || '0', 10);
      if (!cartId) return;
  
      Swal.fire({
        icon: 'warning',
        title: 'Remove item?',
        showCancelButton: true,
        confirmButtonText: 'Remove'
      }).then(r => {
        if (!r.isConfirmed) return;
  
        $.post(EP.remove, { cart_id: cartId }, function (res) {
          if (res && res.status === 'success') {
            toastOK('Removed', 'Item removed');
            loadCart();
          } else {
            toastError('Error', (res && res.message) || 'Remove failed');
          }
        }, 'json').fail(() => toastError('Error', 'Request failed'));
      });
    });
  
    // ---------- EMPTY CART ----------
    $('#empty-cart').on('click', function () {
      Swal.fire({
        icon: 'warning',
        title: 'Empty cart?',
        text: 'This will remove all items from your cart.',
        showCancelButton: true,
        confirmButtonText: 'Empty'
      }).then(r => {
        if (!r.isConfirmed) return;
  
        $.post(EP.empty, {}, function (res) {
          if (res && res.status === 'success') {
            toastOK('Cleared', 'Cart emptied');
            loadCart();
          } else {
            toastError('Error', (res && res.message) || 'Could not empty cart');
          }
        }, 'json').fail(() => toastError('Error', 'Request failed'));
      });
    });
  
    // ---------- NAV BADGE ----------
    function refreshCartBadge() {
      // Optional: if you have a count endpoint, call it. Otherwise, re-fetch cart and count.
      $.getJSON(EP.fetch, function (res) {
        const items = (res && res.data) || [];
        let count = 0;
        items.forEach(i => count += Number(i.qty || 0));
        refreshCartBadgeWith(count);
      });
    }
    function refreshCartBadgeWith(count) {
      const $b = $('#cart-count');
      if ($b.length) $b.text(count > 99 ? '99+' : String(count));
    }
  
    // ---------- INITIALIZE ----------
    loadCart();        // if on cart page, it renders; if not, it does nothing
    refreshCartBadge();// keeps header badge synced
  });
  