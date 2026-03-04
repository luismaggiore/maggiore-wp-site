document.addEventListener("DOMContentLoaded", () => {
  if (document.querySelector("#telefono")) {
    const e = document.querySelector("#telefono"),
      o = window.intlTelInput(e, {
        initialCountry: "auto",
        separateDialCode: !0,
        nationalMode: !1,
        geoIpLookup: (e) => {
          fetch("https://ipapi.co/json/")
            .then((e) => e.json())
            .then((o) => e(o.country_code))
            .catch(() => e("cl"));
        },
      });
    window.maggioreIti = o;
  }
});
