console.log("[jumpscare] loaded");

(function () {
  // Check if jumpscare is enabled (default true)
  let jumpscareEnabled = localStorage.getItem('jumpscareEnabled') !== 'false';

  const originalFetch = window.fetch;

  window.fetch = async function (...args) {
    const response = await originalFetch.apply(this, args);

    try {
      const url = args[0];

      if (
        typeof url === "string" &&
        url.includes("/api/v1/challenges/attempt")
      ) {
        const cloned = response.clone();
        const data = await cloned.json();

        if (data?.data?.status === "incorrect" && jumpscareEnabled && Math.random() < 0.2) {
          triggerJumpScare();
        }
      }
    } catch (e) {

    }

    return response;
  };

  window.triggerJumpScare = function () {
    const overlay = document.getElementById("jumpscare-overlay");
    const audio = document.getElementById("jumpscare-audio");

    if (!overlay || !audio) {
      console.error("Jumpscare elements missing");
      return;
    }

    overlay.classList.add("active");
    audio.currentTime = 0;
    audio.volume = 0.05;
    audio.play();

    setTimeout(() => {
      overlay.classList.remove("active");
    }, 3000);
  };

  // Toggle function
  window.toggleJumpScare = function () {
    jumpscareEnabled = !jumpscareEnabled;
    localStorage.setItem('jumpscareEnabled', jumpscareEnabled);
    updateToggleButton();
  };

  // Update button text
  function updateToggleButton() {
    const button = document.getElementById('jumpscare-toggle');
    if (button) {
      button.textContent = jumpscareEnabled ? 'Disable Jumpscare' : 'Enable Jumpscare';
    }
  }

  // Initialize button on load
  document.addEventListener('DOMContentLoaded', function() {
    updateToggleButton();
    const button = document.getElementById('jumpscare-toggle');
    if (button) {
      button.addEventListener('click', toggleJumpScare);
    }
  });
})();
