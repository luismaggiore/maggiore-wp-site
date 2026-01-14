document.addEventListener("DOMContentLoaded", () => {
  if (document.querySelector("#telefono")) {
    const input = document.querySelector("#telefono");

    const iti = window.intlTelInput(input, {
      initialCountry: "auto",
      separateDialCode: true,
      nationalMode: false,
      geoIpLookup: (callback) => {
        fetch("https://ipapi.co/json/")
          .then((res) => res.json())
          .then((data) => callback(data.country_code))
          .catch(() => callback("cl")); // fallback Chile
      },
    });

    // Opcional: al enviar, guarda el número completo internacional
    document.querySelector("form").addEventListener("submit", (e) => {
      const fullNumber = iti.getNumber(); // ej: +56912345678
      input.value = fullNumber;
    });
  }
});
