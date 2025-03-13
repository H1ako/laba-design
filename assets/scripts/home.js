const editHistoryModal = document.getElementById("edit-history-modal");
const editHistoryModalForm = document.querySelector("#edit-history-modal form");
const editHistoryModalBtns = document.querySelectorAll(
  "[open-edit-history-modal]"
);
const editHistoryModalCloseBtn = editHistoryModal?.querySelector("[close-btn]");

const buyServiceModal = document.getElementById("buy-service-modal");
const buyServiceModalBtns = document.querySelectorAll(
  "[open-buy-service-modal]"
);
const buyServiceModalForms = document.querySelectorAll(
  "#buy-service-modal form"
);
const buyServiceModalCloseBtn = document.querySelector(
  "#buy-service-modal [close-btn]"
);

const settingsForm = document.getElementById("settings-form");

const logoutBtns = document.querySelectorAll("[logout-action]");

function openBuyServiceModal(buy_btn) {
  const serviceId = buy_btn.dataset.serviceId;
  const serviceName = buy_btn.dataset.serviceName;
  const serviceImg = buy_btn.dataset.servicePreviewImage;
  const servicePrice = buy_btn.dataset.servicePrice;
  const serviceWorkers = buy_btn.dataset.serviceWorkers;
  const serviceTime = buy_btn.dataset.serviceTime;

  const serviceIdEl = document.querySelector("#buy-service-modal [service-id]");
  const serviceImgEl = document.querySelector(
    "#buy-service-modal [service-img]"
  );
  const serviceNameEl = document.querySelector(
    "#buy-service-modal [service-name]"
  );
  const serviceTimeEl = document.querySelector(
    "#buy-service-modal [service-time]"
  );
  const serviceWorkersEl = document.querySelector(
    "#buy-service-modal [service-workers]"
  );
  const servicePriceEl = document.querySelector(
    "#buy-service-modal [service-price]"
  );

  serviceIdEl.value = serviceId;
  serviceImgEl.src = serviceImg;
  serviceNameEl.innerText = serviceName;
  serviceTimeEl.innerText = serviceTime;
  serviceWorkersEl.innerText = serviceWorkers;
  servicePriceEl.innerText = servicePrice;

  openModal(buyServiceModal);
}

function openEditHistoryModal(edit_btn) {
  const orderId = edit_btn.dataset.orderId;
  const serviceName = edit_btn.dataset.orderServiceName;
  const serviceImg = edit_btn.dataset.orderServicePreviewImage;
  const servicePrice = edit_btn.dataset.orderServicePrice;
  const serviceWorkers = edit_btn.dataset.orderServiceWorkers;
  const serviceTime = edit_btn.dataset.orderServiceTime;
  const contactPhone = edit_btn.dataset.orderContactPhone;
  const contactDatetime = edit_btn.dataset.orderContactDatetime;
  const address = edit_btn.dataset.orderAddress;

  const orderIdEl = document.querySelector("#edit-history-modal [history-id]");
  const serviceImgEl = document.querySelector(
    "#edit-history-modal [service-img]"
  );
  const serviceNameEl = document.querySelector(
    "#edit-history-modal [service-name]"
  );
  const serviceTimeEl = document.querySelector(
    "#edit-history-modal [service-time]"
  );
  const serviceWorkersEl = document.querySelector(
    "#edit-history-modal [service-workers]"
  );
  const servicePriceEl = document.querySelector(
    "#edit-history-modal [service-price]"
  );
  const contactPhoneEl = document.querySelector(
    "#edit-history-modal [history-phone]"
  );
  const contactDateEl = document.querySelector(
    "#edit-history-modal [history-date]"
  );
  const contactTimeEl = document.querySelector(
    "#edit-history-modal [history-time]"
  );
  const addressEl = document.querySelector(
    "#edit-history-modal [history-address]"
  );

  if (contactDatetime && contactDatetime.includes(" ")) {
    const time = contactDatetime.split(" ");
    const date = time[0];
    var timeStr = time[1];
    timeStr = timeStr.split(":").slice(0, 2).join(":");

    contactDateEl.value = date;
    contactTimeEl.value = timeStr;
  }

  contactPhoneEl.value = contactPhone;
  addressEl.value = address;
  orderIdEl.value = orderId;
  serviceImgEl.src = serviceImg;
  serviceNameEl.innerHTML = serviceName;
  serviceTimeEl.innerText = serviceTime;
  serviceWorkersEl.innerText = serviceWorkers;
  servicePriceEl.innerText = servicePrice;

  openModal(editHistoryModal);
}

function openModal(modal) {
  document.body.style.overflow = "hidden";
  modal.showModal();
}

function closeModal(modal) {
  modal.close();
}

async function handleBuyServiceModalForm(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const data = Object.fromEntries(formData);
  if (data.date && data.time) {
    data.datetime = data.date + " " + data.time;
  }

  const response = await fetch(`${SITE_URL}/api/service-history/create`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  if (response.ok) {
    localStorage.setItem("tabState_tabs", "history");

    const json = await response.json();
    if (json?.data?.service_history?.id) {
      localStorage.setItem("history_newOrder", json?.data?.service_history?.id);
    }

    return location.reload();
  }

  setStatusCodeToForm(form, response.status);
}

function setStatusCodeToForm(form, status) {
  form.dataset.status = status;

  if (status === 200) {
    setTimeout(() => {
      form.dataset.status = "";
    }, 2000);
  }
}

async function handleEditHistoryModalForm(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const data = Object.fromEntries(formData);
  if (data.date && data.time) {
    data.datetime = data.date + " " + data.time;
  }

  const response = await fetch(
    `${SITE_URL}/api/service-history/${data.history_id}/edit`,
    {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    }
  );

  if (response.ok) {
    localStorage.setItem("tabState_tabs", "history");
    localStorage.setItem("history_editedOrder", data.history_id);

    return location.reload();
  }

  setStatusCodeToForm(form, response.status);
}

async function handleSettingsForm(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const data = Object.fromEntries(formData);

  const response = await fetch(`${SITE_URL}/api/user/settings/edit`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  setStatusCodeToForm(settingsForm, response.status);
}

function onTabSwitch(target) {
  const newTab = target.value;
  const controlId = target.dataset.tabsControl;
  if (controlId !== "tabs") return;

  const tabsContentEl = document.querySelector(`[data-tabs-id="${controlId}"]`);
  const newTabContentEl = tabsContentEl?.querySelector(
    `.tab-content_${newTab}`
  );
  if (!newTabContentEl) return;

  const newTabContentHeight = newTabContentEl.offsetHeight;
  tabsContentEl.style.setProperty("--content-height", `${newTabContentHeight}px`);
}

function scrollToNewOrder() {
  const orderId = localStorage.getItem("history_newOrder");
  if (!orderId) return;

  highlightHistoryOrderById(orderId);

  localStorage.removeItem("history_newOrder");
}

function highlightHistoryOrderById(orderId) {
  const newOrderEl = document.querySelector(`[data-order-id="${orderId}"]`);
  if (!newOrderEl) return;

  const parent = newOrderEl.closest(".history-list__el");
  const newOrderRect = parent.getBoundingClientRect();
  const scrollTop =
    document.documentElement.scrollTop || document.body.scrollTop;
  const scrollTo = newOrderRect.top + scrollTop - newOrderRect.height / 2;

  window.scrollTo({ top: scrollTo, behavior: "instant" });
  parent.classList.add("highlight-animation");
}

function scrollToEditedOrder() {
  const orderId = localStorage.getItem("history_editedOrder");
  if (!orderId) return;

  highlightHistoryOrderById(orderId);
  localStorage.removeItem("history_editedOrder");
}

initControlTabs(onTabSwitch);
scrollToNewOrder();
scrollToEditedOrder();
initPhoneFieldFormatter();

editHistoryModalCloseBtn?.addEventListener("click", () => {
  closeModal(editHistoryModal);
});

editHistoryModal?.addEventListener("close", () => {
  document.body.style.overflow = "auto";
  editHistoryModal
    .querySelector("[data-status")
    ?.removeAttribute("data-status");
});

buyServiceModalCloseBtn?.addEventListener("click", () => {
  closeModal(buyServiceModal);
});

buyServiceModal?.addEventListener("close", () => {
  document.body.style.overflow = "auto";
  buyServiceModal.querySelector("[data-status")?.removeAttribute("data-status");
});

logoutBtns.forEach((logoutBtn) => {
  logoutBtn.addEventListener("click", async () => {
    const response = await fetch(`${SITE_URL}/api/auth/logout`);
    if (response.ok) {
      const json = await response.json();

      return redirectTo(json?.data?.redirect);
    }
  });
});

buyServiceModalBtns.forEach((buyServiceModalBtn) => {
  buyServiceModalBtn.addEventListener("click", () => {
    openBuyServiceModal(buyServiceModalBtn);
  });
});

editHistoryModalBtns.forEach((editHistoryModalBtn) => {
  editHistoryModalBtn.addEventListener("click", () => {
    openEditHistoryModal(editHistoryModalBtn);
  });
});

buyServiceModalForms.forEach((form) => {
  form.addEventListener("submit", handleBuyServiceModalForm);
});

editHistoryModalForm?.addEventListener("submit", handleEditHistoryModalForm);

settingsForm?.addEventListener("submit", handleSettingsForm);
