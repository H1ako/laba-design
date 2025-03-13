function initPhoneFieldFormatter() {
  const phoneInputs = document.querySelectorAll("input[type='tel']");

  phoneInputs.forEach((input) => {
    input.addEventListener("input", function (e) {
      let input = e.target.value.replace(/\D/g, ""); // Remove all non-numeric characters
      if (input.startsWith("8")) input = input.replace(/^8/, "7"); // Replace starting 8 with 7
      if (!input.startsWith("7")) input = "7" + input; // Ensure it starts with 7

      let formatted = "+7";
      if (input.length > 1) formatted += " (" + input.substring(1, 4);
      if (input.length >= 5) formatted += ") " + input.substring(4, 7);
      if (input.length >= 8) formatted += "-" + input.substring(7, 9);
      if (input.length >= 10) formatted += "-" + input.substring(9, 11);

      e.target.value = formatted;
    });
  });
}
