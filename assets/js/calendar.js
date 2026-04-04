document.addEventListener("click", function (e) {
  if (
    e.target.classList.contains("kaem-prev") ||
    e.target.classList.contains("kaem-next")
  ) {
    let month = parseInt(e.target.dataset.month);
    let year = parseInt(e.target.dataset.year);

    month += e.target.classList.contains("kaem-next") ? 1 : -1;

    if (month > 12) {
      month = 1;
      year++;
    }

    if (month < 1) {
      month = 12;
      year--;
    }

    let calendar = document.querySelector(".kaem-calendar");

    if (calendar) {
      calendar.innerHTML = "<div class='kaem-loader'></div>";
    }

    fetch(kaem_ajax.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=kaem_load_calendar&month=${month}&year=${year}`,
    })
      .then((res) => res.text())
      .then((html) => {
        if (calendar) calendar.outerHTML = html;
      })
      .catch(() => {
        if (calendar) {
          calendar.innerHTML =
            "<p style='color:red;text-align:center;'>Failed to load calendar</p>";
        }
      });
  } else if (e.target.classList.contains("kaem-event")) {
    let eventId = e.target.dataset.id;
    if (!eventId) return;

    let modalBody = document.getElementById("kaem-modal-body");

    if (modalBody) {
      modalBody.innerHTML = "<div class='kaem-loader'></div>";
    }

    fetch(kaem_ajax.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=kaem_get_event&event_id=${eventId}`,
    })
      .then((res) => res.text())
      .then((html) => {
        document.getElementById("kaem-modal-body").innerHTML = html;

        let modal = document.getElementById("kaem-modal");
        modal.style.display = "block";
        document.body.style.overflow = "hidden";
      })
      .catch(() => {
        if (modalBody) {
          modalBody.innerHTML =
            "<p style='color:red;'>Failed to load event</p>";
        }
      });
  } else if (
    e.target.classList.contains("kaem-close") ||
    e.target.classList.contains("kaem-modal-overlay")
  ) {
    let modal = document.getElementById("kaem-modal");
    if (modal) {
      modal.style.display = "none";
      document.body.style.overflow = "auto";
    }
  } else if (e.target.classList.contains("kaem-book-btn")) {
    let form = document.getElementById("kaem-booking-form");
    if (form) form.style.display = "block";
  } else if (e.target.id === "kaem-submit-booking") {
    let eventId = e.target.dataset.id;
    let name = document.getElementById("kaem_name").value;
    let email = document.getElementById("kaem_email").value;

    let msg = document.getElementById("kaem-booking-message");

    if (!name || !email) {
      msg.innerHTML = "<span style='color:red;'>Fill all fields</span>";
      return;
    }

    msg.innerHTML = "Processing...";

    fetch(kaem_ajax.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=kaem_save_booking&nonce=${kaem_ajax.nonce}&event_id=${eventId}&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}`,
    })
      .then((res) => {
        const contentType = res.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
          return res.json();
        }
        return res.text();
      })
      .then((response) => {
        if (typeof response === "object") {
          if (!response.success) {
            msg.innerHTML = `<span style='color:red;'>${response.data}</span>`;
            return;
          }
          msg.innerHTML = response.data;
        } else {
          msg.innerHTML = response;
        }

        let form = document.getElementById("kaem-booking-form");
        if (form) form.style.display = "none";
      })
      .catch(() => {
        msg.innerHTML = "<span style='color:red;'>Error occurred</span>";
      });
  }
});
