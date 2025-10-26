$(function () {
    const $list = $('#brand-list');
  
    function load() {
      $.getJSON('../actions/fetch_brand_action.php', function (res) {
        if (res.status !== 'success') {
          Swal.fire('Error', res.message || 'Could not load brands', 'error');
          return;
        }
        render(res.data || []);
      });
    }
  
    function render(grouped) {
      $list.empty();
  
      if (!grouped.length) {
        $list.append('<p>No brands yet. Add your first brand above.</p>');
        return;
      }
  
      grouped.forEach(group => {
        const card = $(`
          <div class="cat-card" data-cat="${group.cat_id}">
            <h4>${escapeHtml(group.cat_name)}</h4>
            <div class="brands"></div>
          </div>
        `);
        const $brands = card.find('.brands');
  
        group.brands.forEach(b => {
          $brands.append(`
            <div class="row" data-id="${b.brand_id}">
              <input class="brand-name" type="text" value="${escapeHtml(b.brand_name)}">
              <div class="actions">
                <button class="saveBtn">Update</button>
                <button class="delBtn">Delete</button>
              </div>
            </div>
          `);
        });
  
        $list.append(card);
      });
    }
  
    // Add brand
    $('#add-brand-form').on('submit', function (e) {
      e.preventDefault();
      const name = $('#brand_name').val().trim();
      const category_id = parseInt($('#category_id').val() || '0', 10);
  
      if (!name) return Swal.fire('Oops', 'Enter a brand name', 'error');
      if (!category_id) return Swal.fire('Oops', 'Choose a category', 'error');
  
      $.post('../actions/add_brand_action.php', { name, category_id }, function (res) {
        if (res.status === 'success') {
          $('#brand_name').val('');
          $('#category_id').val('');
          Swal.fire('Success', res.message, 'success');
          load();
        } else {
          Swal.fire('Error', res.message, 'error');
        }
      }, 'json');
    });
  
    // Update brand
    $list.on('click', '.saveBtn', function () {
      const $row = $(this).closest('.row');
      const brand_id = $row.data('id');
      const name = $row.find('.brand-name').val().trim();
  
      if (!name) return Swal.fire('Oops', 'Brand name cannot be empty', 'error');
  
      $.post('../actions/update_brand_action.php', { brand_id, name }, function (res) {
        if (res.status === 'success') {
          Swal.fire('Updated', res.message, 'success');
          load();
        } else {
          Swal.fire('Error', res.message, 'error');
        }
      }, 'json');
    });
  
    // Delete brand
    $list.on('click', '.delBtn', function () {
      const brand_id = $(this).closest('.row').data('id');
      Swal.fire({ title: 'Delete this brand?', showCancelButton: true }).then(r => {
        if (!r.isConfirmed) return;
        $.post('../actions/delete_brand_action.php', { brand_id }, function (res) {
          if (res.status === 'success') {
            Swal.fire('Deleted', res.message, 'success');
            load();
          } else {
            Swal.fire('Error', res.message, 'error');
          }
        }, 'json');
      });
    });
  
    function escapeHtml(s){
      return (s || '').replace(/[&<>"']/g, m =>
        ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])
      );
    }
  
    load();
  });
  