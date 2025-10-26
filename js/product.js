$(function () {
  const $cat = $("#category_id");
  const $brand = $("#brand_id");
  $brand.prop("disabled", true);
  const $list = $("#product-list");
  let brandsByCat = {};

  // Load brands grouped by category
  function loadBrands() {
    $.getJSON("../actions/fetch_brand_action.php", (res) => {
      if (res.status !== "success") {
        Swal.fire("Error", res.message || "Could not load brands", "error");
        return;
      }
      brandsByCat = {};
      (res.data || []).forEach((g) => {
        brandsByCat[g.cat_id] = g.brands.map((b) => ({
          id: b.brand_id,
          name: b.brand_name,
        }));
      });
      refreshBrandOptions();
    });
  }

  function refreshBrandOptions() {
    const id = parseInt($cat.val() || "0", 10);
    $brand.empty().append('<option value="">-- Brand --</option>');
    $brand.prop("disabled", !id); // disable brand if no category is selected
    (brandsByCat[id] || []).forEach((b) =>
      $brand.append(`<option value="${b.id}">${escapeHtml(b.name)}</option>`)
    );
  }

  $cat.on("change", refreshBrandOptions);

  // Load products
  function loadProducts() {
    $.getJSON("../actions/fetch_product_action.php", (res) => {
      if (res.status !== "success") {
        Swal.fire("Error", res.message || "Load failed", "error");
        return;
      }
      render(res.data || []);
    });
  }

  function render(rows) {
    $list.empty();
    if (!rows.length) {
      $list.append("<p>No products yet. Add one above.</p>");
      return;
    }
    rows.forEach((p) => {
      const img =
        p.product_image && p.product_image.trim()
          ? "../" + p.product_image
          : "https://via.placeholder.com/64";
      $list.append(`
          <div class="prod"
               data-id="${p.product_id}"
               data-cat="${p.cat_id}"
               data-brand="${p.brand_id}">
            <img class="thumb" src="${img}" alt="">
            <strong>${escapeHtml(p.cat_name)} › ${escapeHtml(
        p.brand_name
      )}</strong>
            <span>${escapeHtml(p.product_title)} — $${Number(
        p.product_price
      ).toFixed(2)}</span>
            <button class="btn edit">Edit</button>
          </div>
        `);
    });
  }

  // Populate form for edit
  $list.on("click", ".edit", function () {
    const $row = $(this).closest(".prod");
    $("#product_id").val($row.data("id"));
    $cat.val(String($row.data("cat"))).trigger("change");
    setTimeout(() => $brand.val(String($row.data("brand"))), 0);

    // Fetch details to fill inputs
    $.getJSON("../actions/fetch_product_action.php", (res) => {
      if (res.status !== "success") return;
      const p = (res.data || []).find((x) => x.product_id == $row.data("id"));
      if (!p) return;
      $("#title").val(p.product_title || "");
      $("#price").val(p.product_price || "");
      $("#description").val(p.product_desc || "");
      $("#keyword").val(p.product_keywords || "");
      $("html,body").animate({ scrollTop: 0 }, 200);
    });
  });

  // Save (add or update), then upload image if provided
  $("#product-form").on("submit", function (e) {
    e.preventDefault();
    const payload = {
      product_id: parseInt($("#product_id").val() || "0", 10),
      category_id: parseInt($cat.val() || "0", 10),
      brand_id: parseInt($brand.val() || "0", 10),
      title: $("#title").val().trim(),
      price: parseFloat($("#price").val() || "0"),
      description: $("#description").val().trim(),
      keyword: $("#keyword").val().trim(),
    };

    if (!payload.title) return Swal.fire("Oops", "Title is required", "error");
    if (!payload.category_id)
      return Swal.fire("Oops", "Choose a category", "error");
    if (!payload.brand_id) return Swal.fire("Oops", "Choose a brand", "error");

    const url = payload.product_id
      ? "../actions/update_product_action.php"
      : "../actions/add_product_action.php";

    $.post(
      url,
      payload,
      function (res) {
        if (res.status === "success") {
          const pid = payload.product_id || res.id;
          const file = $("#image")[0].files[0];
          if (file) {
            uploadImage(pid, file, () => afterSave());
          } else {
            afterSave();
          }
        } else {
          Swal.fire("Error", res.message, "error");
        }
      },
      "json"
    );

    function afterSave() {
      Swal.fire("Success", "Saved", "success");
      $("#product-form")[0].reset();
      $("#product_id").val("");
      refreshBrandOptions();
      loadProducts();
    }
  });

  // Separate image upload
  function uploadImage(product_id, file, done) {
    const fd = new FormData();
    fd.append("product_id", product_id);
    fd.append("image", file);
    $.ajax({
      url: "../actions/upload_product_image_action.php",
      method: "POST",
      data: fd,
      processData: false,
      contentType: false,
      dataType: "json",
      success: (res) => {
        if (res.status !== "success") {
          Swal.fire("Image Error", res.message || "Upload failed", "error");
        }
        done && done();
      },
      error: () => {
        Swal.fire("Image Error", "Upload failed", "error");
        done && done();
      },
    });
  }

  $("#resetBtn").on("click", function () {
    $("#product-form")[0].reset();
    $("#product_id").val("");
    refreshBrandOptions();
  });

  function escapeHtml(s) {
    return (s || "").replace(
      /[&<>"']/g,
      (m) =>
        ({
          "&": "&amp;",
          "<": "&lt;",
          ">": "&gt;",
          '"': "&quot;",
          "'": "&#39;",
        }[m])
    );
  }

  loadBrands();
  loadProducts();

  // After initial AJAX finishes, auto-pick the first category so Brand shows immediately
  $(document).one("ajaxStop", function () {
    const firstCat = $cat.find('option[value!=""]').first().val();
    if (firstCat) {
      $cat.val(firstCat);
      refreshBrandOptions();
    }
  });
});
