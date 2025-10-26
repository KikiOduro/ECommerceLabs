$(function () {
  const $grid = $("#product-grid");
  const $pager = $("#pager");
  const $q = $("#q");
  const $cat = $("#filter_cat");
  const $brand = $("#filter_brand");

  let page = 1,
    limit = 10,
    mode = "list",
    param = {};

  function fetch() {
    const args = new URLSearchParams({ fn: mode, page, limit, ...param });
    $.getJSON("actions/product_actions.php?" + args.toString(), function (res) {
      if (res.status !== "success") return renderError(res.message || "Failed");
      render(res.data || [], res.total || 0);
    });
  }

  function renderError(msg) {
    $grid.html(`<p style="color:#b00">${msg}</p>`);
  }

  function render(rows, total) {
    $grid.empty();
    if (!rows.length) {
      $grid.html("<p>No products found.</p>");
      $pager.empty();
      return;
    }

    rows.forEach((p) => {
      const img =
        p.product_image && p.product_image.trim()
          ? p.product_image
          : "https://via.placeholder.com/300x300?text=No+Image";

      $grid.append(`
          <a class="card" href="single_product.php?id=${p.product_id}">
            <img src="${img}" alt="">
            <div class="meta">
              <div class="title">${escapeHtml(p.product_title)}</div>
              <div class="price">$${Number(p.product_price).toFixed(2)}</div>
              <div class="sub">${escapeHtml(p.cat_name)} • ${escapeHtml(
        p.brand_name
      )}</div>
              <button class="btn small" type="button">Add to Cart</button>
            </div>
          </a>
        `);
    });

    const pages = Math.max(1, Math.ceil(total / limit));
    let html = "";
    for (let i = 1; i <= pages; i++) {
      html += `<button class="pg ${
        i === page ? "active" : ""
      }" data-p="${i}">${i}</button>`;
    }
    $pager.html(html);
  }

  $pager.on("click", ".pg", function () {
    page = parseInt($(this).data("p"), 10);
    fetch();
  });

  // Search
  $("#searchForm").on("submit", function (e) {
    e.preventDefault();
    const term = $q.val().trim();
    page = 1;
    mode = term ? "search" : "list";
    param = term ? { q: term } : {};
    fetch();
  });

  // Filters
  $cat.on("change", function () {
    page = 1;
    const v = parseInt($cat.val() || "0", 10);
    if (v) {
      mode = "filter_cat";
      param = { cat_id: v };
    } else {
      mode = "list";
      param = {};
    }
    fetch();
  });

  $brand.on("change", function () {
    page = 1;
    const v = parseInt($brand.val() || "0", 10);
    if (v) {
      mode = "filter_brand";
      param = { brand_id: v };
    } else {
      mode = "list";
      param = {};
    }
    fetch();
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

  // Initial load
  fetch();
});
