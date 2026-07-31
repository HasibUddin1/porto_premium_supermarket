document.addEventListener("DOMContentLoaded", function () {
  // ---------- Product page: quantity form (product.php) ----------
  var productForm = document.querySelector('form[action*="cart_add.php"]');
  if (productForm) {
    productForm.addEventListener("submit", function (e) {
      e.preventDefault();
      var formData = new FormData(productForm);
      addToCart(formData);
    });
  }

  // ---------- Listing pages: icon "Add to Cart" links (index.php, includes/products.php) ----------
  // Delegated on document so it also works for products rendered after page load.
  document.addEventListener("click", function (e) {
    var link = e.target.closest(".tultip-op[data-product-id] a");
    if (!link) return;

    e.preventDefault();
    var productId = link.closest(".tultip-op").getAttribute("data-product-id");
    if (!productId) return;

    var formData = new FormData();
    formData.append("product_id", productId);
    formData.append("quantity", 1);
    addToCart(formData);
  });

  // ---------- Header cart dropdown: remove item ----------
  // Delegated on document so it works for items rebuilt after an "add" too.
  document.addEventListener("click", function (e) {
    var icon = e.target.closest(".cart_list .cart_remove_icon");
    if (!icon) return;

    e.preventDefault();
    e.stopPropagation();

    var li = icon.closest("li[data-product-id]");
    if (!li) return;

    var productId = li.getAttribute("data-product-id");
    var formData = new FormData();
    formData.append("product_id", productId);

    fetch("core/cart_remove.php", {
      method: "POST",
      body: formData,
    })
      .then(function (res) {
        return res.json();
      })
      .then(function (data) {
        if (data.success) {
          li.style.transition = "opacity 0.3s ease";
          li.style.opacity = "0";
          setTimeout(function () {
            li.remove();
          }, 300);

          updateCartCount(data.cart_count);
          updateCartTotal(data.cart_total);

          showToast(data.message || "Item removed.", false);
        } else {
          showToast(data.message || "Could not remove item.", true);
        }
      })
      .catch(function () {
        showToast("Something went wrong. Please try again.", true);
      });
  });

  // ---------- Shared AJAX call for adding an item ----------
  function addToCart(formData) {
    fetch("core/cart_add.php", {
      method: "POST",
      body: formData,
    })
      .then(function (res) {
        return res.json();
      })
      .then(function (data) {
        if (data.success) {
          showToast(data.message || "Added to cart!", false);
          updateCartCount(data.cart_count);
          updateCartTotal(data.cart_total);
          if (data.items) {
            renderCartDropdown(data.items);
          }
        } else {
          showToast(data.message || "Could not add to cart.", true);
        }
      })
      .catch(function () {
        showToast("Something went wrong. Please try again.", true);
      });
  }

  // ---------- Rebuild the dropdown item list from the server's cart data ----------
  function renderCartDropdown(items) {
    var list = document.querySelector(".cart_list > ul");
    if (!list) return;

    list.innerHTML = "";

    if (!items.length) {
      var emptyLi = document.createElement("li");
      var emptyP = document.createElement("p");
      emptyP.style.padding = "15px 0";
      emptyP.textContent = "Your cart is empty.";
      emptyLi.appendChild(emptyP);
      list.appendChild(emptyLi);
      return;
    }

    items.forEach(function (item) {
      var li = document.createElement("li");
      li.setAttribute("data-product-id", item.id);

      var wrapper = document.createElement("div");
      wrapper.className = "cart_item_wrapper clear_fix";

      var imgHolder = document.createElement("div");
      imgHolder.className = "img_holder float_left";
      var img = document.createElement("img");
      img.src = item.image;
      img.width = 70;
      img.alt = "Cart Image";
      img.className = "img-responsive";
      imgHolder.appendChild(img);

      var details = document.createElement("div");
      details.className = "item_deatils float_left";
      var h6 = document.createElement("h6");
      h6.textContent = item.name;
      var span = document.createElement("span");
      span.className = "font_fix";
      span.textContent =
        "$ " + parseFloat(item.price).toFixed(2) + " \u00D7 " + item.quantity;
      details.appendChild(h6);
      details.appendChild(span);

      var removeIcon = document.createElement("i");
      removeIcon.className = "fa fa-times-circle cart_remove_icon";
      removeIcon.setAttribute("aria-hidden", "true");

      wrapper.appendChild(imgHolder);
      wrapper.appendChild(details);
      wrapper.appendChild(removeIcon);
      li.appendChild(wrapper);
      list.appendChild(li);
    });
  }

  function updateCartCount(count) {
    document.querySelectorAll(".cart_count").forEach(function (el) {
      el.textContent = count;
    });
  }

  function updateCartTotal(total) {
    document.querySelectorAll(".cart_total_amount").forEach(function (el) {
      el.textContent = "$" + total;
    });
    document.querySelectorAll(".cart_total_text").forEach(function (el) {
      el.textContent = "Total - $" + total;
    });
  }

  function showToast(message, isError) {
    var toast = document.createElement("div");
    toast.className = "cart_toast" + (isError ? " cart_toast_error" : "");
    toast.textContent = message;
    document.body.appendChild(toast);

    requestAnimationFrame(function () {
      toast.classList.add("cart_toast_visible");
    });

    setTimeout(function () {
      toast.classList.remove("cart_toast_visible");
      setTimeout(function () {
        toast.remove();
      }, 300);
    }, 2000);
  }
});
