document.getElementById("anonymous").addEventListener("change", function () {
    const identitySection = document.getElementById("identitySection");
    identitySection.style.display = this.checked ? "none" : "block";
});

document.getElementById("complaintForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const complaintId = "CLG-" + Math.floor(1000 + Math.random() * 9000);

    alert(
        "Complaint submitted successfully.\n" +
        "Your Complaint ID is: " + complaintId
    );

    this.reset();
    document.getElementById("identitySection").style.display = "block";
});
