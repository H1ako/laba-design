const usersTab = document.querySelector("section.users");
const userCreateForm = document.querySelector("section.users form");
const usersList = document.querySelectorAll("section.users .list li");

const servicesTab = document.querySelector("section.services");
const serviceCreateForm = document.querySelector("section.services form");
const servicesList = document.querySelectorAll("section.services .list li");

const ordersTab = document.querySelector("section.orders");
const orderCreateForm = document.querySelector("section.orders form");
const ordersList = document.querySelectorAll("section.orders .list li");

const imgUploadFields = document.querySelectorAll(
  'label:has(input[type="file"])'
);

initPhoneFieldFormatter();

function setStatusCodeToForm(form, status) {
  form.dataset.status = status;

  if (status === 200) {
    setTimeout(() => {
      form.dataset.status = "";
    }, 2000);
  }
}

imgUploadFields.forEach((field) => {
  const input = field.querySelector('input[type="file"]');
  const image = field.querySelector("img");

  input.addEventListener("change", () => {
    const file = input.files[0];
    const reader = new FileReader();

    reader.onload = () => {
      image.src = reader.result;
    };

    reader.readAsDataURL(file);
  });
});

usersList.forEach((user) => {
  const userId = user.dataset.id;
  const editForm = user.querySelector("form");
  const deleteBtn = user.querySelector("button[delete-action]");

  deleteBtn.addEventListener("click", async () => {
    const response = await fetch(
      `${SITE_URL}/api/admin/users/${userId}/delete`,
      {
        method: "DELETE",
      }
    );

    if (response.ok) {
      user.remove();
      Notiflix.Notify.success("Успешно");
    } else {
      Notiflix.Notify.failure("Ошибка, перезагрузите страницу и повторите");
    }

    setStatusCodeToForm(editForm, response.status);
  });

  editForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const response = await fetch(`${SITE_URL}/api/admin/users/${userId}/edit`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(Object.fromEntries(new FormData(editForm))),
    });

    if (response.ok) {
      Notiflix.Notify.success("Успешно");
    } else {
      Notiflix.Notify.failure("Ошибка, перезагрузите страницу и повторите");
    }

    setStatusCodeToForm(editForm, response.status);
  });
});

servicesList.forEach((service) => {
  const serviceId = service.dataset.id;
  const editForm = service.querySelector("form");
  const deleteBtn = service.querySelector("button[delete-action]");

  deleteBtn.addEventListener("click", async () => {
    const response = await fetch(
      `${SITE_URL}/api/admin/services/${serviceId}/delete`,
      {
        method: "DELETE",
      }
    );

    if (response.ok) {
      service.remove();
      Notiflix.Notify.success("Успешно");
    } else {
      Notiflix.Notify.failure("Ошибка, перезагрузите страницу и повторите");
    }

    setStatusCodeToForm(editForm, response.status);
  });

  editForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(editForm);

    const response = await fetch(
      `${SITE_URL}/api/admin/services/${serviceId}/edit`,
      {
        method: "POST",
        body: formData,
      }
    );

    if (response.ok) {
      Notiflix.Notify.success("Успешно");
    } else {
      Notiflix.Notify.failure("Ошибка, перезагрузите страницу и повторите");
    }

    setStatusCodeToForm(editForm, response.status);
  });
});

ordersList.forEach((order) => {
  const orderId = order.dataset.id;
  const editForm = order.querySelector("form");
  const deleteBtn = order.querySelector("button[delete-action]");

  deleteBtn.addEventListener("click", async () => {
    const response = await fetch(
      `${SITE_URL}/api/admin/service_history/${orderId}/delete`,
      {
        method: "DELETE",
      }
    );

    if (response.ok) {
      order.remove();
      Notiflix.Notify.success("Успешно");
    } else {
      Notiflix.Notify.failure("Ошибка, перезагрузите страницу и повторите");
    }

    setStatusCodeToForm(editForm, response.status);
  });

  editForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const response = await fetch(
      `${SITE_URL}/api/admin/service_history/${orderId}/edit`,
      {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(Object.fromEntries(new FormData(editForm))),
      }
    );

    if (response.ok) {
      Notiflix.Notify.success("Успешно");
    } else {
      Notiflix.Notify.failure("Ошибка, перезагрузите страницу и повторите");
    }

    setStatusCodeToForm(editForm, response.status);
  });
});

userCreateForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const response = await fetch(`${SITE_URL}/api/admin/users/create`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(Object.fromEntries(new FormData(userCreateForm))),
  });

  if (response.ok) {
    return location.reload();
  }

  setStatusCodeToForm(userCreateForm, response.status);
});

serviceCreateForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const formData = new FormData(serviceCreateForm);

  const response = await fetch(`${SITE_URL}/api/admin/services/create`, {
    method: "POST",
    body: formData,
  });

  if (response.ok) {
    return location.reload();
  }

  setStatusCodeToForm(serviceCreateForm, response.status);
});

orderCreateForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const response = await fetch(`${SITE_URL}/api/admin/service_history/create`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(Object.fromEntries(new FormData(orderCreateForm))),
  });

  if (response.ok) {
    return location.reload();
  }

  setStatusCodeToForm(orderCreateForm, response.status);
});
