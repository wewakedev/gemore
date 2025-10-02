// Admin Panel JavaScript
class AdminPanel {
  constructor() {
    this.apiBase = "/api";
    this.authToken = localStorage.getItem("adminToken");
    this.currentSection = "dashboard";
    this.currentPage = 1;
    this.limit = 20;

    this.init();
  }

  async init() {
    // Check authentication
    if (!this.authToken) {
      this.showLogin();
      return;
    }

    try {
      await this.verifyAuth();
      this.setupEventListeners();
      this.loadDashboard();
    } catch (error) {
      console.error("Auth verification failed:", error);
      this.showLogin();
    }
  }

  async verifyAuth() {
    const response = await fetch(`${this.apiBase}/auth/me`, {
      headers: {
        Authorization: `Bearer ${this.authToken}`,
      },
    });

    if (!response.ok) {
      throw new Error("Authentication failed");
    }

    const data = await response.json();
    if (data.user.role !== "admin") {
      throw new Error("Admin access required");
    }

    document.getElementById("adminName").textContent = data.user.name;
  }

  showLogin() {
    // Redirect to a login page or show login modal
    window.location.href = "/admin/login.html";
  }

  setupEventListeners() {
    // Navigation
    document
      .querySelectorAll(".sidebar .nav-link[data-section]")
      .forEach((link) => {
        link.addEventListener("click", (e) => {
          e.preventDefault();
          this.switchSection(e.target.dataset.section);
        });
      });

    // Order status change handler
    document.getElementById("orderStatus").addEventListener("change", (e) => {
      const trackingFields = document.getElementById("trackingFields");
      if (e.target.value === "shipped") {
        trackingFields.style.display = "block";
      } else {
        trackingFields.style.display = "none";
      }
    });

    // Category name auto-slug generation
    document.getElementById("categoryName").addEventListener("input", (e) => {
      const slug = e.target.value
        .toLowerCase()
        .replace(/[^a-z0-9]/g, "-")
        .replace(/-+/g, "-")
        .replace(/^-|-$/g, "");
      document.getElementById("categorySlug").value = slug;
    });
  }

  switchSection(section) {
    // Update navigation
    document.querySelectorAll(".sidebar .nav-link").forEach((link) => {
      link.classList.remove("active");
    });
    document
      .querySelector(`[data-section="${section}"]`)
      .classList.add("active");

    // Update content
    document.querySelectorAll(".content-section").forEach((sec) => {
      sec.classList.remove("active");
    });
    document.getElementById(section).classList.add("active");

    this.currentSection = section;
    this.currentPage = 1;

    // Load section data
    switch (section) {
      case "dashboard":
        this.loadDashboard();
        break;
      case "products":
        this.loadProducts();
        this.loadCategories("filter");
        break;
      case "categories":
        this.loadCategories();
        break;
      case "orders":
        this.loadOrders();
        break;
      case "users":
        this.loadUsers();
        break;
      case "coupons":
        this.loadCoupons();
        break;
    }
  }

  async apiCall(endpoint, options = {}) {
    const defaultOptions = {
      headers: {
        Authorization: `Bearer ${this.authToken}`,
        "Content-Type": "application/json",
      },
    };

    const finalOptions = { ...defaultOptions, ...options };

    // Don't set Content-Type for FormData
    if (options.body instanceof FormData) {
      delete finalOptions.headers["Content-Type"];
    }

    const response = await fetch(`${this.apiBase}${endpoint}`, finalOptions);
    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || "API call failed");
    }

    return data;
  }

  async loadDashboard() {
    try {
      const data = await this.apiCall("/admin/dashboard");

      // Update stats
      document.getElementById("totalProducts").textContent =
        data.data.stats.totalProducts;
      document.getElementById("totalOrders").textContent =
        data.data.stats.totalOrders;
      document.getElementById("totalUsers").textContent =
        data.data.stats.totalUsers;
      document.getElementById(
        "totalRevenue"
      ).textContent = `₹${data.data.stats.totalRevenue.toLocaleString(
        "en-IN"
      )}`;

      // Update recent orders
      this.updateRecentOrdersTable(data.data.recentOrders);

      // Update top products
      this.updateTopProductsList(data.data.topProducts);
    } catch (error) {
      console.error("Load dashboard error:", error);
      this.showError("Failed to load dashboard data");
    }
  }

  updateRecentOrdersTable(orders) {
    const tbody = document.getElementById("recentOrdersTable");

    if (orders.length === 0) {
      tbody.innerHTML =
        '<tr><td colspan="5" class="text-center">No recent orders</td></tr>';
      return;
    }

    tbody.innerHTML = orders
      .map(
        (order) => `
            <tr>
                <td><strong>#${order.orderNumber}</strong></td>
                <td>${order.user.name}</td>
                <td>₹${order.pricing.total.toLocaleString("en-IN")}</td>
                <td><span class="badge status-badge order-status-${
                  order.status
                }">${order.status}</span></td>
                <td>${new Date(order.createdAt).toLocaleDateString()}</td>
            </tr>
        `
      )
      .join("");
  }

  updateTopProductsList(products) {
    const container = document.getElementById("topProductsList");

    if (products.length === 0) {
      container.innerHTML =
        '<p class="text-center text-muted">No products found</p>';
      return;
    }

    container.innerHTML = products
      .map(
        (product) => `
            <div class="d-flex align-items-center mb-3">
                <img src="${
                  product.images[0] || "/images/placeholder.jpg"
                }" alt="${product.name}" 
                     class="product-image-preview me-3">
                <div>
                    <h6 class="mb-1">${product.name}</h6>
                    <small class="text-muted">${
                      product.ratings.count
                    } reviews</small>
                </div>
            </div>
        `
      )
      .join("");
  }

  async loadProducts() {
    try {
      const params = new URLSearchParams({
        page: this.currentPage,
        limit: this.limit,
      });

      const search = document.getElementById("productSearch")?.value;
      const category = document.getElementById("productCategoryFilter")?.value;
      const status = document.getElementById("productStatusFilter")?.value;

      if (search) params.append("search", search);
      if (category) params.append("category", category);
      if (status) params.append("status", status);

      const data = await this.apiCall(`/admin/products?${params}`);
      this.updateProductsTable(data.data.products);
      this.updatePagination("productsPagination", data.data.pagination, () =>
        this.loadProducts()
      );
    } catch (error) {
      console.error("Load products error:", error);
      this.showError("Failed to load products");
    }
  }

  updateProductsTable(products) {
    const tbody = document.getElementById("productsTable");

    if (products.length === 0) {
      tbody.innerHTML =
        '<tr><td colspan="8" class="text-center">No products found</td></tr>';
      return;
    }

    tbody.innerHTML = products
      .map((product) => {
        const minPrice = Math.min(...product.variants.map((v) => v.price));
        const totalStock = product.variants.reduce(
          (sum, v) => sum + v.stock,
          0
        );

        return `
                <tr>
                    <td>
                        <img src="${
                          product.images[0] || "/images/placeholder.jpg"
                        }" alt="${product.name}" 
                             class="product-image-preview">
                    </td>
                    <td>
                        <strong>${product.name}</strong><br>
                        <small class="text-muted">${
                          product.category.name
                        }</small>
                    </td>
                    <td>${product.sku}</td>
                    <td>${product.category.name}</td>
                    <td>₹${minPrice.toLocaleString("en-IN")}+</td>
                    <td>${totalStock}</td>
                    <td>
                        <span class="badge ${
                          product.isActive ? "bg-success" : "bg-danger"
                        }">
                            ${product.isActive ? "Active" : "Inactive"}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="admin.editProduct('${
                          product._id
                        }')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="admin.deleteProduct('${
                          product._id
                        }')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
      })
      .join("");
  }

  async loadCategories(target = "table") {
    try {
      const data = await this.apiCall("/admin/categories");

      if (target === "table") {
        this.updateCategoriesTable(data.data);
      } else if (target === "filter") {
        this.updateCategoryFilter(data.data);
      } else if (target === "modal") {
        this.updateCategorySelect(data.data);
      }
    } catch (error) {
      console.error("Load categories error:", error);
      this.showError("Failed to load categories");
    }
  }

  updateCategoriesTable(categories) {
    const tbody = document.getElementById("categoriesTable");

    if (categories.length === 0) {
      tbody.innerHTML =
        '<tr><td colspan="6" class="text-center">No categories found</td></tr>';
      return;
    }

    tbody.innerHTML = categories
      .map(
        (category) => `
            <tr>
                <td>
                    <img src="${
                      category.image || "/images/placeholder.jpg"
                    }" alt="${category.name}" 
                         class="product-image-preview">
                </td>
                <td>${category.name}</td>
                <td>${category.slug}</td>
                <td>-</td>
                <td>
                    <span class="badge ${
                      category.isActive ? "bg-success" : "bg-danger"
                    }">
                        ${category.isActive ? "Active" : "Inactive"}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="admin.editCategory('${
                      category._id
                    }')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="admin.deleteCategory('${
                      category._id
                    }')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `
      )
      .join("");
  }

  updateCategoryFilter(categories) {
    const select = document.getElementById("productCategoryFilter");
    select.innerHTML =
      '<option value="">All Categories</option>' +
      categories
        .map((cat) => `<option value="${cat._id}">${cat.name}</option>`)
        .join("");
  }

  updateCategorySelect(categories) {
    const select = document.getElementById("productCategory");
    select.innerHTML =
      '<option value="">Select Category</option>' +
      categories
        .map((cat) => `<option value="${cat._id}">${cat.name}</option>`)
        .join("");
  }

  async loadOrders() {
    try {
      const params = new URLSearchParams({
        page: this.currentPage,
        limit: this.limit,
      });

      const status = document.getElementById("orderStatusFilter")?.value;
      if (status) params.append("status", status);

      const data = await this.apiCall(`/admin/orders?${params}`);
      this.updateOrdersTable(data.data.orders);
      this.updatePagination("ordersPagination", data.data.pagination, () =>
        this.loadOrders()
      );
    } catch (error) {
      console.error("Load orders error:", error);
      this.showError("Failed to load orders");
    }
  }

  updateOrdersTable(orders) {
    const tbody = document.getElementById("ordersTable");

    if (orders.length === 0) {
      tbody.innerHTML =
        '<tr><td colspan="8" class="text-center">No orders found</td></tr>';
      return;
    }

    tbody.innerHTML = orders
      .map(
        (order) => `
            <tr>
                <td><strong>#${order.orderNumber}</strong></td>
                <td>
                    ${order.user.name}<br>
                    <small class="text-muted">${order.user.email}</small>
                </td>
                <td>${order.items.length} item(s)</td>
                <td>₹${order.pricing.total.toLocaleString("en-IN")}</td>
                <td>
                    <span class="badge ${
                      order.payment.method === "cod" ? "bg-warning" : "bg-info"
                    }">
                        ${order.payment.method.toUpperCase()}
                    </span>
                </td>
                <td>
                    <span class="badge status-badge order-status-${
                      order.status
                    }">${order.status}</span>
                </td>
                <td>${new Date(order.createdAt).toLocaleDateString()}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="admin.viewOrder('${
                      order._id
                    }')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="admin.updateOrderStatus('${
                      order._id
                    }')">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
        `
      )
      .join("");
  }

  async loadUsers() {
    try {
      const data = await this.apiCall("/admin/users");
      this.updateUsersTable(data.data.users);
    } catch (error) {
      console.error("Load users error:", error);
      this.showError("Failed to load users");
    }
  }

  updateUsersTable(users) {
    const tbody = document.getElementById("usersTable");

    if (users.length === 0) {
      tbody.innerHTML =
        '<tr><td colspan="7" class="text-center">No users found</td></tr>';
      return;
    }

    tbody.innerHTML = users
      .map(
        (user) => `
            <tr>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td>${user.phone || "-"}</td>
                <td>
                    <span class="badge ${
                      user.role === "admin" ? "bg-danger" : "bg-primary"
                    }">
                        ${user.role}
                    </span>
                </td>
                <td>${new Date(user.createdAt).toLocaleDateString()}</td>
                <td>-</td>
                <td>
                    <button class="btn btn-sm btn-outline-info" onclick="admin.viewUser('${
                      user._id
                    }')">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `
      )
      .join("");
  }

  async loadCoupons() {
    try {
      const data = await this.apiCall("/admin/coupons");
      this.updateCouponsTable(data.data);
    } catch (error) {
      console.error("Load coupons error:", error);
      this.showError("Failed to load coupons");
    }
  }

  updateCouponsTable(coupons) {
    const tbody = document.getElementById("couponsTable");

    if (coupons.length === 0) {
      tbody.innerHTML =
        '<tr><td colspan="9" class="text-center">No coupons found</td></tr>';
      return;
    }

    tbody.innerHTML = coupons
      .map(
        (coupon) => `
            <tr>
                <td><strong>${coupon.code}</strong></td>
                <td>${coupon.name}</td>
                <td>
                    <span class="badge ${
                      coupon.type === "percentage" ? "bg-info" : "bg-warning"
                    }">
                        ${coupon.type}
                    </span>
                </td>
                <td>
                    ${
                      coupon.type === "percentage"
                        ? coupon.value + "%"
                        : "₹" + coupon.value
                    }
                </td>
                <td>₹${coupon.minimumOrderAmount}</td>
                <td>${coupon.usageCount}${
          coupon.usageLimit.total ? "/" + coupon.usageLimit.total : ""
        }</td>
                <td>${new Date(coupon.validUntil).toLocaleDateString()}</td>
                <td>
                    <span class="badge ${
                      coupon.isActive ? "bg-success" : "bg-danger"
                    }">
                        ${coupon.isActive ? "Active" : "Inactive"}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="admin.editCoupon('${
                      coupon._id
                    }')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="admin.deleteCoupon('${
                      coupon._id
                    }')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `
      )
      .join("");
  }

  updatePagination(containerId, pagination, loadFunction) {
    const container = document.getElementById(containerId);

    if (pagination.totalPages <= 1) {
      container.innerHTML = "";
      return;
    }

    let paginationHTML = '<ul class="pagination justify-content-center">';

    // Previous button
    paginationHTML += `
            <li class="page-item ${!pagination.hasPrevPage ? "disabled" : ""}">
                <a class="page-link" href="#" onclick="admin.changePage(${
                  pagination.currentPage - 1
                }, ${loadFunction})">Previous</a>
            </li>
        `;

    // Page numbers
    for (let i = 1; i <= pagination.totalPages; i++) {
      if (i === pagination.currentPage) {
        paginationHTML += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
      } else {
        paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="admin.changePage(${i}, ${loadFunction})">${i}</a></li>`;
      }
    }

    // Next button
    paginationHTML += `
            <li class="page-item ${!pagination.hasNextPage ? "disabled" : ""}">
                <a class="page-link" href="#" onclick="admin.changePage(${
                  pagination.currentPage + 1
                }, ${loadFunction})">Next</a>
            </li>
        `;

    paginationHTML += "</ul>";
    container.innerHTML = paginationHTML;
  }

  changePage(page, loadFunction) {
    this.currentPage = page;
    loadFunction.call(this);
  }

  // Product Management
  openProductModal(productId = null) {
    const modal = new bootstrap.Modal(document.getElementById("productModal"));
    this.loadCategories("modal");

    if (productId) {
      this.loadProductForEdit(productId);
    } else {
      this.resetProductForm();
    }

    modal.show();
  }

  async loadProductForEdit(productId) {
    try {
      const data = await this.apiCall(`/admin/products/${productId}`);
      const product = data.data;

      document.getElementById("productId").value = product._id;
      document.getElementById("productName").value = product.name;
      document.getElementById("productSku").value = product.sku;
      document.getElementById("productDescription").value = product.description;
      document.getElementById("productCategory").value = product.category._id;
      document.getElementById("productTags").value = product.tags.join(", ");
      document.getElementById("productActive").checked = product.isActive;
      document.getElementById("productFeatured").checked = product.isFeatured;

      // Load variants
      this.loadProductVariants(product.variants);

      // Show existing images
      this.showExistingImages(product.images);
    } catch (error) {
      console.error("Load product error:", error);
      this.showError("Failed to load product details");
    }
  }

  resetProductForm() {
    document.getElementById("productForm").reset();
    document.getElementById("productId").value = "";
    document.getElementById("variantsContainer").innerHTML =
      this.getVariantHTML();
    document.getElementById("existingImages").innerHTML = "";
  }

  loadProductVariants(variants) {
    const container = document.getElementById("variantsContainer");
    container.innerHTML = variants
      .map((variant) => this.getVariantHTML(variant))
      .join("");
  }

  getVariantHTML(variant = {}) {
    return `
            <div class="variant-item border p-3 mb-3 rounded">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Variant Name</label>
                        <input type="text" class="form-control variant-name" placeholder="e.g., Chocolate Flavor" value="${
                          variant.name || ""
                        }">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Size</label>
                        <input type="text" class="form-control variant-size" placeholder="e.g., 1kg" value="${
                          variant.size || ""
                        }">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Price *</label>
                        <input type="number" class="form-control variant-price" value="${
                          variant.price || ""
                        }" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Original Price</label>
                        <input type="number" class="form-control variant-original-price" value="${
                          variant.originalPrice || ""
                        }">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control variant-stock" value="${
                          variant.stock || 0
                        }">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeVariant(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
  }

  showExistingImages(images) {
    const container = document.getElementById("existingImages");
    container.innerHTML = images
      .map(
        (img, index) => `
            <div class="d-inline-block position-relative me-2 mb-2">
                <img src="${img}" class="product-image-preview">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                        onclick="admin.removeImage(${index})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `
      )
      .join("");
  }

  async saveProduct() {
    try {
      const formData = new FormData();
      const productId = document.getElementById("productId").value;

      // Basic fields
      formData.append("name", document.getElementById("productName").value);
      formData.append("sku", document.getElementById("productSku").value);
      formData.append(
        "description",
        document.getElementById("productDescription").value
      );
      formData.append(
        "category",
        document.getElementById("productCategory").value
      );
      formData.append("tags", document.getElementById("productTags").value);
      formData.append(
        "isActive",
        document.getElementById("productActive").checked
      );
      formData.append(
        "isFeatured",
        document.getElementById("productFeatured").checked
      );

      // Variants
      const variants = this.collectVariants();
      formData.append("variants", JSON.stringify(variants));

      // Images
      const imageFiles = document.getElementById("productImages").files;
      for (let i = 0; i < imageFiles.length; i++) {
        formData.append("images", imageFiles[i]);
      }

      let endpoint = "/admin/products";
      let method = "POST";

      if (productId) {
        endpoint += `/${productId}`;
        method = "PUT";
      }

      await this.apiCall(endpoint, {
        method: method,
        body: formData,
      });

      this.showSuccess("Product saved successfully");
      bootstrap.Modal.getInstance(
        document.getElementById("productModal")
      ).hide();
      this.loadProducts();
    } catch (error) {
      console.error("Save product error:", error);
      this.showError("Failed to save product: " + error.message);
    }
  }

  collectVariants() {
    const variants = [];
    document.querySelectorAll(".variant-item").forEach((item) => {
      const variant = {
        name: item.querySelector(".variant-name").value,
        size: item.querySelector(".variant-size").value,
        price: parseFloat(item.querySelector(".variant-price").value),
        originalPrice:
          parseFloat(item.querySelector(".variant-original-price").value) ||
          null,
        stock: parseInt(item.querySelector(".variant-stock").value) || 0,
        isActive: true,
      };

      if (variant.name && variant.price) {
        variants.push(variant);
      }
    });
    return variants;
  }

  // Category Management
  openCategoryModal(categoryId = null) {
    const modal = new bootstrap.Modal(document.getElementById("categoryModal"));

    if (categoryId) {
      this.loadCategoryForEdit(categoryId);
    } else {
      this.resetCategoryForm();
    }

    modal.show();
  }

  resetCategoryForm() {
    document.getElementById("categoryForm").reset();
    document.getElementById("categoryId").value = "";
  }

  async saveCategory() {
    try {
      const formData = new FormData();
      const categoryId = document.getElementById("categoryId").value;

      formData.append("name", document.getElementById("categoryName").value);
      formData.append("slug", document.getElementById("categorySlug").value);
      formData.append(
        "description",
        document.getElementById("categoryDescription").value
      );
      formData.append("icon", document.getElementById("categoryIcon").value);
      formData.append(
        "isActive",
        document.getElementById("categoryActive").checked
      );

      const imageFile = document.getElementById("categoryImage").files[0];
      if (imageFile) {
        formData.append("image", imageFile);
      }

      let endpoint = "/admin/categories";
      let method = "POST";

      if (categoryId) {
        endpoint += `/${categoryId}`;
        method = "PUT";
      }

      await this.apiCall(endpoint, {
        method: method,
        body: formData,
      });

      this.showSuccess("Category saved successfully");
      bootstrap.Modal.getInstance(
        document.getElementById("categoryModal")
      ).hide();
      this.loadCategories();
    } catch (error) {
      console.error("Save category error:", error);
      this.showError("Failed to save category: " + error.message);
    }
  }

  // Order Management
  updateOrderStatus(orderId) {
    const modal = new bootstrap.Modal(
      document.getElementById("orderStatusModal")
    );
    document.getElementById("orderStatusId").value = orderId;
    modal.show();
  }

  async saveOrderStatus() {
    try {
      const orderId = document.getElementById("orderStatusId").value;
      const status = document.getElementById("orderStatus").value;
      const note = document.getElementById("orderStatusNote").value;
      const trackingProvider =
        document.getElementById("trackingProvider").value;
      const trackingNumber = document.getElementById("trackingNumber").value;

      const data = {
        status,
        note,
        trackingProvider,
        trackingNumber,
      };

      await this.apiCall(`/admin/orders/${orderId}/status`, {
        method: "PUT",
        body: JSON.stringify(data),
      });

      this.showSuccess("Order status updated successfully");
      bootstrap.Modal.getInstance(
        document.getElementById("orderStatusModal")
      ).hide();
      this.loadOrders();
    } catch (error) {
      console.error("Update order status error:", error);
      this.showError("Failed to update order status: " + error.message);
    }
  }

  // Coupon Management
  openCouponModal(couponId = null) {
    const modal = new bootstrap.Modal(document.getElementById("couponModal"));

    if (couponId) {
      this.loadCouponForEdit(couponId);
    } else {
      this.resetCouponForm();
    }

    modal.show();
  }

  resetCouponForm() {
    document.getElementById("couponForm").reset();
    document.getElementById("couponId").value = "";

    // Set default dates
    const now = new Date();
    const tomorrow = new Date(now.getTime() + 24 * 60 * 60 * 1000);
    const nextWeek = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);

    document.getElementById("couponValidFrom").value = now
      .toISOString()
      .slice(0, 16);
    document.getElementById("couponValidUntil").value = nextWeek
      .toISOString()
      .slice(0, 16);
  }

  async saveCoupon() {
    try {
      const couponId = document.getElementById("couponId").value;

      const data = {
        code: document.getElementById("couponCode").value.toUpperCase(),
        name: document.getElementById("couponName").value,
        description: document.getElementById("couponDescription").value,
        type: document.getElementById("couponType").value,
        value: parseFloat(document.getElementById("couponValue").value),
        minimumOrderAmount:
          parseFloat(document.getElementById("couponMinOrder").value) || 0,
        maximumDiscount:
          parseFloat(document.getElementById("couponMaxDiscount").value) ||
          null,
        validFrom: document.getElementById("couponValidFrom").value,
        validUntil: document.getElementById("couponValidUntil").value,
        "usageLimit.total":
          parseInt(document.getElementById("couponTotalLimit").value) || null,
        "usageLimit.perUser":
          parseInt(document.getElementById("couponUserLimit").value) || 1,
        isActive: document.getElementById("couponActive").checked,
      };

      let endpoint = "/admin/coupons";
      let method = "POST";

      if (couponId) {
        endpoint += `/${couponId}`;
        method = "PUT";
      }

      await this.apiCall(endpoint, {
        method: method,
        body: JSON.stringify(data),
      });

      this.showSuccess("Coupon saved successfully");
      bootstrap.Modal.getInstance(
        document.getElementById("couponModal")
      ).hide();
      this.loadCoupons();
    } catch (error) {
      console.error("Save coupon error:", error);
      this.showError("Failed to save coupon: " + error.message);
    }
  }

  // Delete functions
  async deleteProduct(productId) {
    if (!confirm("Are you sure you want to delete this product?")) return;

    try {
      await this.apiCall(`/admin/products/${productId}`, { method: "DELETE" });
      this.showSuccess("Product deleted successfully");
      this.loadProducts();
    } catch (error) {
      console.error("Delete product error:", error);
      this.showError("Failed to delete product");
    }
  }

  async deleteCategory(categoryId) {
    if (!confirm("Are you sure you want to delete this category?")) return;

    try {
      await this.apiCall(`/admin/categories/${categoryId}`, {
        method: "DELETE",
      });
      this.showSuccess("Category deleted successfully");
      this.loadCategories();
    } catch (error) {
      console.error("Delete category error:", error);
      this.showError("Failed to delete category");
    }
  }

  async deleteCoupon(couponId) {
    if (!confirm("Are you sure you want to delete this coupon?")) return;

    try {
      await this.apiCall(`/admin/coupons/${couponId}`, { method: "DELETE" });
      this.showSuccess("Coupon deleted successfully");
      this.loadCoupons();
    } catch (error) {
      console.error("Delete coupon error:", error);
      this.showError("Failed to delete coupon");
    }
  }

  // Utility functions
  showSuccess(message) {
    this.showAlert(message, "success");
  }

  showError(message) {
    this.showAlert(message, "danger");
  }

  showAlert(message, type) {
    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText =
      "top: 20px; right: 20px; z-index: 9999; min-width: 300px;";
    alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
      if (alertDiv.parentNode) {
        alertDiv.parentNode.removeChild(alertDiv);
      }
    }, 5000);
  }

  logout() {
    localStorage.removeItem("adminToken");
    window.location.href = "/admin/login.html";
  }
}

// Helper functions for HTML onclick events
function toggleSidebar() {
  document.querySelector(".sidebar").classList.toggle("show");
}

function addVariant() {
  const container = document.getElementById("variantsContainer");
  container.insertAdjacentHTML("beforeend", admin.getVariantHTML());
}

function removeVariant(button) {
  button.closest(".variant-item").remove();
}

function filterProducts() {
  admin.currentPage = 1;
  admin.loadProducts();
}

function filterOrders() {
  admin.currentPage = 1;
  admin.loadOrders();
}

function openProductModal() {
  admin.openProductModal();
}

function openCategoryModal() {
  admin.openCategoryModal();
}

function openCouponModal() {
  admin.openCouponModal();
}

function saveProduct() {
  admin.saveProduct();
}

function saveCategory() {
  admin.saveCategory();
}

function saveCoupon() {
  admin.saveCoupon();
}

function updateOrderStatus() {
  admin.saveOrderStatus();
}

function logout() {
  admin.logout();
}

// Initialize admin panel
const admin = new AdminPanel();
