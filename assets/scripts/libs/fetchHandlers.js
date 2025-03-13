async function handleFormFetch(form, url) {
  const formData = new FormData(form);
  const data = Object.fromEntries(formData);
  if (data?.pass1 && data?.pass2 && data.pass1 !== data.pass2) {
    form.dataset.status = 504;
    return
  }

  const response = await fetch(`${SITE_URL}${url}`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });
  if (response.ok) {
    const json = await response.json();

    if (json?.data?.redirect) {
      return redirectTo(json.data.redirect);
    }
  }

  form.dataset.status = response.status;
}

function redirectTo(url) {
  enableLoadingScreen();
  setTimeout(() => {
    window.location.href = `${SITE_URL}${url}`;
  }, 2000);
}
