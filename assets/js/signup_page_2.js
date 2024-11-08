// Function to handle form submission for additional information
async function submitAdditionalInfo(event) {
    event.preventDefault();
    
    const address = document.getElementById("address").value;
    const phone = document.getElementById("phone").value;
    
    // Get user_id from localStorage
    const user_id = localStorage.getItem("user_id");
    
    // Prepare data for API
    const data = { user_id, address, phone };

    // Call the API using fetch
    const response = await fetch("http://localhost/recommend_movie/routes/signup_additional_info_2.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    });

    const result = await response.json();
    
    if (result.status === "success") {
        alert("Registration complete!");
        window.location.href = "http://localhost/recommend_movie/routes/views/signup_success.html"; // Redirect to a success page
    } else {
        alert(result.message);
    }
}