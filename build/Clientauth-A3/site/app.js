/*
  CTF lesson: client-side checks are not security.
  The "admin" status is stored entirely in localStorage and enforced only in JS.
*/

const FLAG = "ctf{client_side_auth_is_not_security}";

const statusEl = document.getElementById("status");
const memberArea = document.getElementById("memberArea");
const adminArea = document.getElementById("adminArea");
const flagBox = document.getElementById("flagBox");

const loginBtn = document.getElementById("loginBtn");
const logoutBtn = document.getElementById("logoutBtn");

// The vulnerability: role is trusted from localStorage (user-controlled)
function getRole() {
  return localStorage.getItem("role") || "guest";
}

function setRole(role) {
  localStorage.setItem("role", role);
}

function render() {
  const role = getRole();

  memberArea.classList.add("hidden");
  adminArea.classList.add("hidden");
  flagBox.textContent = "";

  if (role === "guest") {
    statusEl.textContent = "Status: guest (no access)";
  } else if (role === "member") {
    statusEl.textContent = "Status: member (limited access)";
    memberArea.classList.remove("hidden");
  } else if (role === "admin") {
    statusEl.textContent = "Status: admin (full access)";
    adminArea.classList.remove("hidden");
    flagBox.textContent = FLAG;
  } else {
    statusEl.textContent = `Status: unknown role (${role})`;
  }
}

loginBtn.addEventListener("click", () => {
  // "Login" just sets a role. No server involved.
  setRole("member");
  render();
});

logoutBtn.addEventListener("click", () => {
  localStorage.removeItem("role");
  render();
});

// Initial load
render();
