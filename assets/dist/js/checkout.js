$("#checkout_error").css("display", "none");
let name;
let email;
let cc_no;
let cc_month;
let cc_year;
let cvv;

$(document).ready(function () {
  $("#paymentPayBtn2").click(function () {
    name = $("#paymentCardName").val();
    email = $("#paymentCardEmail").val();
    cc_no = $("#cc_no").val();
    cc_month = $("#cc_month").val();
    cc_year = $("#cc_year").val();
    cvv = $("#cvv").val();

    var error;
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (name && email && cc_no && cc_month && cc_year && cvv) {
      if (!validateEmail(email)) {
        error = "Email is not Valid";
        $("#checkout_error").html(error);
        $("#checkout_error").css("display", "block");
      } else if (cc_no.length < 16) {
        error = "Card Number Must be 16 digit long";
        $("#checkout_error").html(error);
        $("#checkout_error").css("display", "block");
      } else {
        $("#checkout_error").css("display", "none");
        sessionStorage.setItem("name", name);
        // window.location.href = "cart_checkoutFailed.php";
        window.location.href = "cart_checkoutSuccess.php";
      }
    }
  });
  function validateEmail(email) {
    console.log("Inside Email Validation");
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  }
});
