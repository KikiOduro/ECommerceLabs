$(function () {
    const $rows = $('#category-rows');
  
    function load() {
      $.getJSON('../actions/fetch_category_action.php', function (res) {
        if (res.status === 'success') {
          render(res.data);
        } else {
          Swal.fire('Error', res.message || 'Could not load categories', 'error');
        }
      });
    }
  
    function render(list) {
      $rows.empty();
      if (!list || !list.length) {
        $rows.append('<tr><td colspan="4" style="text-align:center;">No categories yet.</td></tr>');
        return;
      }
      list.forEach(c => {
        $rows.append(`
          <tr data-id="${c.cat_id}">
            <td>${c.cat_id}</td>
            <td><input type="text" class="cat-name" value="${c.cat_name}"></td>
            <td>${c.created_at || ''}</td>
            <td>
              <button class="saveBtn">Update</button>
              <button class="delBtn">Delete</button>
            </td>
          </tr>
        `);
      });
    }
  
   
    $('#add-category-form').submit(function (e) {
      e.preventDefault();
      const name = $('#name').val().trim();
      if (!name) return Swal.fire('Oops', 'Enter a category name', 'error');
  
      $.post('../actions/add_category_action.php', { name }, function (res) {
        if (res.status === 'success') {
          $('#name').val('');
          Swal.fire('Success', res.message, 'success');
          load();
        } else {
          Swal.fire('Error', res.message, 'error');
        }
      }, 'json');
    });
 
    $rows.on('click', '.saveBtn', function () {
      const $tr = $(this).closest('tr');
      const id = $tr.data('id');
      const name = $tr.find('.cat-name').val().trim();
  
      $.post('../actions/update_category_action.php', { category_id: id, name }, function (res) {
        if (res.status === 'success') {
          Swal.fire('Updated', res.message, 'success');
          load();
        } else {
          Swal.fire('Error', res.message, 'error');
        }
      }, 'json');
    });
  
   
    $rows.on('click', '.delBtn', function () {
      const id = $(this).closest('tr').data('id');
      Swal.fire({ title: 'Delete?', showCancelButton: true }).then(r => {
        if (!r.isConfirmed) return;
        $.post('../actions/delete_category_action.php', { category_id: id }, function (res) {
          if (res.status === 'success') {
            Swal.fire('Deleted', res.message, 'success');
            load();
          } else {
            Swal.fire('Error', res.message, 'error');
          }
        }, 'json');
      });
    });
  
    load();
  });
  