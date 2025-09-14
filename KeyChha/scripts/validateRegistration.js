function validateForm() {
  const pwd = document.getElementById("password").value;
  const confirm = document.getElementById("confirm_password").value;

  if (pwd !== confirm) {
    alert("❗ Passwords do not match!");
    return false;
  }

  if (pwd.length < 6) {
    alert("❗ Password must be at least 6 characters.");
    return false;
  }

  return true;
}
