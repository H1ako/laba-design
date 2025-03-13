const signInForm = document.getElementById("signin-form");
const signUpForm = document.getElementById("signup-form");

initControlTabs();
initPhoneFieldFormatter();

signInForm &&
  signInForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    await handleFormFetch(signInForm, "/api/auth/signin");
  });

signUpForm &&
  signUpForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    await handleFormFetch(signUpForm, "/api/auth/signup");
  });
