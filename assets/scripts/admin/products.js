/**
 * Admin Products Management
 * This script handles product management functionality in the admin panel
 */

document.addEventListener("DOMContentLoaded", function() {
  // Initialize tabs if they exist
  const tabButtons = document.querySelectorAll(".tab-btn");
  if (tabButtons.length > 0) {
    initTabs();
  }

  // Main functionality
  initProductForm();
  initProductDelete();
  initCharacteristics();
  initSizes();
  initImageManagement();
});

/**
 * Initialize tab functionality
 */
function initTabs() {
  const tabButtons = document.querySelectorAll(".tab-btn");
  const tabPanes = document.querySelectorAll(".tab-pane");
  
  tabButtons.forEach(button => {
    button.addEventListener("click", function() {
      // Remove active class from all buttons and panes
      tabButtons.forEach(btn => btn.classList.remove("active"));
      tabPanes.forEach(pane => pane.classList.remove("active"));
      
      // Add active class to current button
      this.classList.add("active");
      
      // Show the corresponding tab pane
      const tabId = this.getAttribute("data-tab");
      document.getElementById(`${tabId}-tab`).classList.add("active");
    });
  });
}

/**
 * Initialize product form (create/edit)
 */
function initProductForm() {
  const productForm = document.getElementById("product-form");
  if (!productForm) return;
  
  // File input preview functionality
  const thumbInput = document.getElementById("thumb");
  if (thumbInput) {
    thumbInput.addEventListener("change", function() {
      const preview = this.closest(".custom-file-upload").querySelector(".file-preview");
      const previewImage = preview.querySelector(".file-preview-image");
      const placeholder = preview.querySelector(".file-preview-placeholder");
      
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          previewImage.src = e.target.result;
          previewImage.style.display = "block";
          if (placeholder) placeholder.style.display = "none";
          preview.classList.add("has-image");
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  }
  
  // Form submission
  productForm.addEventListener("submit", async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Сохранение...';
    
    const formData = new FormData(this);
    const productId = this.getAttribute("data-product-id");
    const isEdit = !!productId;
    const url = isEdit ? `/api/admin/products/${productId}` : "/api/admin/products";
    
    try {
      const response = await fetch(url, {
        method: isEdit ? "PUT" : "POST",
        body: formData,
        headers: {
          "X-CSRF-Token": getCSRFToken()
        }
      });
      
      const result = await response.json();
      
      if (result.status === "success") {
        showNotification(`Товар успешно ${isEdit ? "обновлен" : "создан"}`, "success");
        
        if (result.redirect) {
          window.location.href = result.redirect;
        }
      } else {
        if (result.errors) {
          displayFormErrors(productForm, result.errors);
        }
        
        showNotification(result.message || `Не удалось ${isEdit ? "обновить" : "создать"} товар`, "error");
      }
    } catch (error) {
      console.error("Error:", error);
      showNotification(`Произошла ошибка при ${isEdit ? "обновлении" : "создании"} товара`, "error");
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    }
  });
}

/**
 * Initialize product deletion
 */
function initProductDelete() {
  document.querySelectorAll("[data-product-delete]").forEach(button => {
    button.addEventListener("click", async function(e) {
      e.preventDefault();
      
      const productId = this.getAttribute("data-product-id");
      const confirmMessage = this.getAttribute("data-confirm") || "Вы уверены что хотите удалить этот товар?";
      
      if (!confirm(confirmMessage)) return;
      
      const originalHTML = this.innerHTML;
      this.disabled = true;
      this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
      
      try {
        const response = await fetch(`/api/admin/products/${productId}`, {
          method: "DELETE",
          headers: {
            "X-CSRF-Token": getCSRFToken()
          }
        });
        
        const result = await response.json();
        
        if (result.status === "success") {
          showNotification("Товар успешно удален", "success");
          
          if (result.redirect) {
            window.location.href = result.redirect;
          } else {
            const productElement = document.querySelector(`[data-product-id="${productId}"]`);
            if (productElement) {
              productElement.remove();
            }
          }
        } else {
          this.disabled = false;
          this.innerHTML = originalHTML;
          showNotification(result.message || "Не удалось удалить товар", "error");
        }
      } catch (error) {
        this.disabled = false;
        this.innerHTML = originalHTML;
        console.error("Error:", error);
        showNotification("Произошла ошибка при удалении товара", "error");
      }
    });
  });
}

/**
 * Initialize characteristics management
 */
function initCharacteristics() {
  const characteristicsList = document.getElementById("characteristics-list");
  const addCharacteristicBtn = document.getElementById("add-characteristic-btn");
  
  if (!characteristicsList || !addCharacteristicBtn) return;
  
  // Add new characteristic
  addCharacteristicBtn.addEventListener("click", async function() {
    const productId = this.getAttribute("data-product-id");
    const nameInput = document.getElementById("new-characteristic-name");
    const valueInput = document.getElementById("new-characteristic-value");
    
    if (!nameInput.value.trim() || !valueInput.value.trim()) {
      showNotification("Заполните название и значение характеристики", "error");
      return;
    }
    
    const originalText = this.innerHTML;
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    
    try {
      const response = await fetch(`/api/admin/products/${productId}/characteristics`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-Token": getCSRFToken()
        },
        body: JSON.stringify({
          name: nameInput.value.trim(),
          value: valueInput.value.trim()
        })
      });
      
      const result = await response.json();
      
      if (result.status === "success") {
        const newItem = createCharacteristicElement(
          result.data.characteristic.id,
          result.data.characteristic.name,
          result.data.characteristic.value,
          productId
        );
        
        characteristicsList.appendChild(newItem);
        nameInput.value = "";
        valueInput.value = "";
        showNotification("Характеристика успешно добавлена", "success");
      } else {
        showNotification(result.message || "Не удалось добавить характеристику", "error");
      }
    } catch (error) {
      console.error("Error:", error);
      showNotification("Произошла ошибка при добавлении характеристики", "error");
    } finally {
      this.disabled = false;
      this.innerHTML = originalText;
    }
  });
  
  // Event delegation for edit/delete
  if (characteristicsList) {
    characteristicsList.addEventListener("click", function(e) {
      const target = e.target;
      
      // Handle delete
      if (target.matches("[data-characteristic-delete]") || target.closest("[data-characteristic-delete]")) {
        const btn = target.matches("[data-characteristic-delete]") ? target : target.closest("[data-characteristic-delete]");
        handleCharacteristicDelete(btn);
      }
      
      // Handle edit
      if (target.matches("[data-characteristic-edit]") || target.closest("[data-characteristic-edit]")) {
        const btn = target.matches("[data-characteristic-edit]") ? target : target.closest("[data-characteristic-edit]");
        handleCharacteristicEdit(btn);
      }
    });
  }
}

/**
 * Create a characteristic list item
 */
function createCharacteristicElement(id, name, value, productId) {
  const li = document.createElement("li");
  li.className = "list-group-item characteristic-item";
  
  li.innerHTML = `
    <div class="characteristic-name">${escapeHTML(name)}</div>
    <div class="characteristic-value">${escapeHTML(value)}</div>
    <div class="characteristic-actions">
      <button class="btn btn-sm btn-primary" data-characteristic-edit data-characteristic-id="${id}" data-product-id="${productId}">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
        </svg>
      </button>
      <button class="btn btn-sm btn-danger" data-characteristic-delete data-characteristic-id="${id}" data-product-id="${productId}">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="3 6 5 6 21 6"></polyline>
          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
          <line x1="10" y1="11" x2="10" y2="17"></line>
          <line x1="14" y1="11" x2="14" y2="17"></line>
        </svg>
      </button>
    </div>
  `;
  
  return li;
}

/**
 * Handle characteristic deletion
 */
async function handleCharacteristicDelete(btn) {
  const productId = btn.getAttribute("data-product-id");
  const characteristicId = btn.getAttribute("data-characteristic-id");
  const item = btn.closest(".characteristic-item");
  
  if (!confirm("Вы уверены, что хотите удалить эту характеристику?")) return;
  
  const originalHTML = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
  
  try {
    const response = await fetch(`/api/admin/products/${productId}/characteristics/${characteristicId}`, {
      method: "DELETE",
      headers: {
        "X-CSRF-Token": getCSRFToken()
      }
    });
    
    const result = await response.json();
    
    if (result.status === "success") {
      item.remove();
      showNotification("Характеристика успешно удалена", "success");
    } else {
      btn.disabled = false;
      btn.innerHTML = originalHTML;
      showNotification(result.message || "Не удалось удалить характеристику", "error");
    }
  } catch (error) {
    btn.disabled = false;
    btn.innerHTML = originalHTML;
    console.error("Error:", error);
    showNotification("Произошла ошибка при удалении характеристики", "error");
  }
}

/**
 * Handle characteristic editing
 */
function handleCharacteristicEdit(btn) {
  const item = btn.closest(".characteristic-item");
  const nameElement = item.querySelector(".characteristic-name");
  const valueElement = item.querySelector(".characteristic-value");
  const actionsElement = item.querySelector(".characteristic-actions");
  
  const originalName = nameElement.textContent;
  const originalValue = valueElement.textContent;
  const originalActions = actionsElement.innerHTML;
  
  // Switch to edit mode
  nameElement.innerHTML = `<input type="text" class="form-control form-control-sm" value="${escapeHTML(originalName)}">`;
  valueElement.innerHTML = `<input type="text" class="form-control form-control-sm" value="${escapeHTML(originalValue)}">`;
  
  // Change actions to save/cancel
  actionsElement.innerHTML = `
    <button class="btn btn-sm btn-success save-characteristic">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
        <polyline points="17 21 17 13 7 13 7 21"></polyline>
        <polyline points="7 3 7 8 15 8"></polyline>
      </svg>
    </button>
    <button class="btn btn-sm btn-secondary cancel-edit">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
  `;
  
  // Setup cancel button
  actionsElement.querySelector(".cancel-edit").addEventListener("click", function() {
    nameElement.textContent = originalName;
    valueElement.textContent = originalValue;
    actionsElement.innerHTML = originalActions;
  });
  
  // Setup save button
  actionsElement.querySelector(".save-characteristic").addEventListener("click", async function() {
    const productId = btn.getAttribute("data-product-id");
    const characteristicId = btn.getAttribute("data-characteristic-id");
    const newName = nameElement.querySelector("input").value.trim();
    const newValue = valueElement.querySelector("input").value.trim();
    
    if (!newName || !newValue) {
      showNotification("Название и значение не могут быть пустыми", "error");
      return;
    }
    
    const saveBtn = this;
    const originalSaveBtnHTML = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    
    try {
      const response = await fetch(`/api/admin/products/${productId}/characteristics/${characteristicId}`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-Token": getCSRFToken()
        },
        body: JSON.stringify({
          name: newName,
          value: newValue
        })
      });
      
      const result = await response.json();
      
      if (result.status === "success") {
        nameElement.textContent = newName;
        valueElement.textContent = newValue;
        actionsElement.innerHTML = originalActions;
        showNotification("Характеристика успешно обновлена", "success");
      } else {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalSaveBtnHTML;
        showNotification(result.message || "Не удалось обновить характеристику", "error");
      }
    } catch (error) {
      saveBtn.disabled = false;
      saveBtn.innerHTML = originalSaveBtnHTML;
      console.error("Error:", error);
      showNotification("Произошла ошибка при обновлении характеристики", "error");
    }
  });
}

/**
 * Initialize sizes management
 */
function initSizes() {
  const sizesList = document.getElementById("sizes-list");
  const addSizeBtn = document.getElementById("add-size-btn");
  
  if (!sizesList || !addSizeBtn) return;
  
  // Add new size
  addSizeBtn.addEventListener("click", async function() {
    const productId = this.getAttribute("data-product-id");
    const sizeInput = document.getElementById("new-size-name");
    const inStockCheck = document.getElementById("new-size-in-stock");
    
    if (!sizeInput.value.trim()) {
      showNotification("Введите размер", "error");
      return;
    }
    
    const originalText = this.innerHTML;
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    
    try {
      const response = await fetch(`/api/admin/products/${productId}/sizes`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-Token": getCSRFToken()
        },
        body: JSON.stringify({
          size: sizeInput.value.trim(),
          in_stock: inStockCheck.checked
        })
      });
      
      const result = await response.json();
      
      if (result.status === "success") {
        const newItem = createSizeElement(
          result.size.id,
          result.size.size,
          result.size.in_stock,
          productId
        );
        
        sizesList.appendChild(newItem);
        sizeInput.value = "";
        showNotification("Размер успешно добавлен", "success");
      } else {
        showNotification(result.message || "Не удалось добавить размер", "error");
      }
    } catch (error) {
      console.error("Error:", error);
      showNotification("Произошла ошибка при добавлении размера", "error");
    } finally {
      this.disabled = false;
      this.innerHTML = originalText;
    }
  });
  
  // Event delegation for size actions
  if (sizesList) {
    // Delete size
    sizesList.addEventListener("click", function(e) {
      if (e.target.matches("[data-size-delete]") || e.target.closest("[data-size-delete]")) {
        const btn = e.target.matches("[data-size-delete]") ? e.target : e.target.closest("[data-size-delete]");
        handleSizeDelete(btn);
      }
    });
    
    // Toggle in-stock status
    sizesList.addEventListener("change", function(e) {
      if (e.target.matches("[data-size-in-stock]")) {
        const checkbox = e.target;
        handleSizeStockUpdate(checkbox);
      }
    });
  }
}

/**
 * Create a size list item
 */
function createSizeElement(id, size, inStock, productId) {
  const li = document.createElement("li");
  li.className = "list-group-item size-item";
  
  li.innerHTML = `
    <div class="size-name">${escapeHTML(size)}</div>
    <div class="size-controls">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="size-in-stock-${id}" 
               data-size-in-stock data-size-id="${id}" data-product-id="${productId}" 
               ${inStock ? "checked" : ""}>
        <label class="form-check-label" for="size-in-stock-${id}">В наличии</label>
      </div>
      <button class="btn btn-sm btn-danger ms-2" data-size-delete data-size-id="${id}" data-product-id="${productId}">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="3 6 5 6 21 6"></polyline>
          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
          <line x1="10" y1="11" x2="10" y2="17"></line>
          <line x1="14" y1="11" x2="14" y2="17"></line>
        </svg>
      </button>
    </div>
  `;
  
  return li;
}

/**
 * Handle size deletion
 */
async function handleSizeDelete(btn) {
  const productId = btn.getAttribute("data-product-id");
  const sizeId = btn.getAttribute("data-size-id");
  const item = btn.closest(".size-item");
  
  if (!confirm("Вы уверены, что хотите удалить этот размер?")) return;
  
  const originalHTML = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
  
  try {
    const response = await fetch(`/api/admin/products/${productId}/sizes/${sizeId}`, {
      method: "DELETE",
      headers: {
        "X-CSRF-Token": getCSRFToken()
      }
    });
    
    const result = await response.json();
    
    if (result.status === "success") {
      item.remove();
      showNotification("Размер успешно удален", "success");
    } else {
      btn.disabled = false;
      btn.innerHTML = originalHTML;
      showNotification(result.message || "Не удалось удалить размер", "error");
    }
  } catch (error) {
    btn.disabled = false;
    btn.innerHTML = originalHTML;
    console.error("Error:", error);
    showNotification("Произошла ошибка при удалении размера", "error");
  }
}

/**
 * Handle size in-stock update
 */
async function handleSizeStockUpdate(checkbox) {
  const productId = checkbox.getAttribute("data-product-id");
  const sizeId = checkbox.getAttribute("data-size-id");
  const inStock = checkbox.checked;
  
  try {
    const response = await fetch(`/api/admin/products/${productId}/sizes/${sizeId}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": getCSRFToken()
      },
      body: JSON.stringify({
        in_stock: inStock
      })
    });
    
    const result = await response.json();
    
    if (result.status === "success") {
      showNotification("Наличие товара обновлено", "success");
    } else {
      checkbox.checked = !inStock; // Revert the checkbox
      showNotification(result.message || "Не удалось обновить наличие товара", "error");
    }
  } catch (error) {
    checkbox.checked = !inStock; // Revert the checkbox
    console.error("Error:", error);
    showNotification("Произошла ошибка при обновлении наличия товара", "error");
  }
}

/**
 * Initialize image management (upload, sort, delete)
 */
function initImageManagement() {
  initImageUpload();
  initImageSorting();
  initImageDelete();
}

/**
 * Initialize image upload
 */
function initImageUpload() {
  const imageUploadForm = document.getElementById("image-upload-form");
  if (!imageUploadForm) return;
  
  const imageInput = document.getElementById("product-image");
  const imagePreview = document.getElementById("image-preview");
  
  if (imageInput && imagePreview) {
    // Preview image on selection
    imageInput.addEventListener("change", function() {
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          imagePreview.style.backgroundImage = `url(${e.target.result})`;
          imagePreview.classList.add("has-image");
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  }
  
  // Handle form submission
  imageUploadForm.addEventListener("submit", async function(e) {
    e.preventDefault();
    
    const productId = this.getAttribute("data-product-id");
    
    if (!imageInput.files || !imageInput.files[0]) {
      showNotification("Выберите изображение", "error");
      return;
    }
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Загрузка...';
    
    const formData = new FormData();
    formData.append("image", imageInput.files[0]);
    
    try {
      const response = await fetch(`/api/admin/products/${productId}/images`, {
        method: "POST",
        body: formData,
        headers: {
          "X-CSRF-Token": getCSRFToken()
        }
      });
      
      const result = await response.json();
      
      if (result.status === "success") {
        showNotification("Изображение успешно загружено", "success");
        
        // Add new image to the gallery
        const gallery = document.getElementById("product-images-gallery");
        if (gallery && result.image) {
          const newImage = document.createElement("div");
          newImage.className = "product-image-item";
          newImage.setAttribute("data-image-id", result.image.id);
          newImage.setAttribute("data-sort-order", result.image.sort_order);
          
          newImage.innerHTML = `
            <div class="product-image" style="background-image: url('${result.image.image_url}')"></div>
            <div class="product-image-actions">
              <button type="button" class="btn btn-sm btn-danger" data-image-delete data-image-id="${result.image.id}" data-product-id="${productId}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="3 6 5 6 21 6"></polyline>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                  <line x1="10" y1="11" x2="10" y2="17"></line>
                  <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
              </button>
            </div>
          `;
          
          gallery.appendChild(newImage);
          
          // Reset form
          imageInput.value = "";
          imagePreview.style.backgroundImage = "";
          imagePreview.classList.remove("has-image");
        }
      } else {
        showNotification(result.message || "Не удалось загрузить изображение", "error");
      }
    } catch (error) {
      console.error("Error:", error);
      showNotification("Произошла ошибка при загрузке изображения", "error");
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    }
  });
}

/**
 * Initialize image sorting functionality
 */
function initImageSorting() {
  const gallery = document.getElementById("product-images-gallery");
  
  if (gallery && window.Sortable) {
    new Sortable(gallery, {
      animation: 150,
      ghostClass: "sortable-ghost",
      onEnd: function(evt) {
        const productId = gallery.getAttribute("data-product-id");
        const imageId = evt.item.getAttribute("data-image-id");
        const newOrder = evt.newIndex;
        
        // Update the sorting order in the database
        updateImageOrder(productId, imageId, newOrder);
      }
    });
  }
}

/**
 * Update image sorting order
 */
async function updateImageOrder(productId, imageId, newOrder) {
  try {
    const response = await fetch(`/api/admin/products/${productId}/images/${imageId}/sort`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": getCSRFToken()
      },
      body: JSON.stringify({
        sort_order: newOrder
      })
    });
    
    const result = await response.json();
    
    if (result.status === "success") {
      showNotification("Порядок изображений обновлен", "success");
    } else {
      showNotification(result.message || "Не удалось обновить порядок изображений", "error");
    }
  } catch (error) {
    console.error("Error:", error);
    showNotification("Произошла ошибка при обновлении порядка изображений", "error");
  }
}

/**
 * Initialize image deletion
 */
function initImageDelete() {
  const gallery = document.getElementById("product-images-gallery");
  
  if (!gallery) return;
  
  gallery.addEventListener("click", function(e) {
    if (e.target.matches("[data-image-delete]") || e.target.closest("[data-image-delete]")) {
      const btn = e.target.matches("[data-image-delete]") ? e.target : e.target.closest("[data-image-delete]");
      handleImageDelete(btn);
    }
  });
}

/**
 * Handle image deletion
 */
async function handleImageDelete(btn) {
  const productId = btn.getAttribute("data-product-id");
  const imageId = btn.getAttribute("data-image-id");
  const item = btn.closest(".product-image-item");
  
  if (!confirm("Вы уверены, что хотите удалить это изображение?")) return;
  
  const originalHTML = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
  
  try {
    const response = await fetch(`/api/admin/products/${productId}/images/${imageId}`, {
      method: "DELETE",
      headers: {
        "X-CSRF-Token": getCSRFToken()
      }
    });
    
    const result = await response.json();
    
    if (result.status === "success") {
      item.remove();
      showNotification("Изображение успешно удалено", "success");
    } else {
      btn.disabled = false;
      btn.innerHTML = originalHTML;
      showNotification(result.message || "Не удалось удалить изображение", "error");
    }
  } catch (error) {
    btn.disabled = false;
    btn.innerHTML = originalHTML;
    console.error("Error:", error);
    showNotification("Произошла ошибка при удалении изображения", "error");
  }
}

/**
 * Utility function to get CSRF token
 */
function getCSRFToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

/**
 * Display validation errors on form
 */
function displayFormErrors(form, errors) {
  // Clear previous errors
  form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
  form.querySelectorAll('.invalid-feedback').forEach(el => el.classList.remove('active'));
  
  // Display new errors
  for (const [field, message] of Object.entries(errors)) {
    const input = form.querySelector(`[name="${field}"]`);
    const feedback = form.querySelector(`[data-error-for="${field}"]`);
    
    if (input) input.classList.add('is-invalid');
    if (feedback) {
      feedback.textContent = message;
      feedback.classList.add('active');
    }
  }
}



/**
 * Safely escape HTML to prevent XSS
 */
function escapeHTML(str) {
  return str
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}