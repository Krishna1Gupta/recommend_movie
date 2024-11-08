
// async function takes a single parameter, event, which is automatically passed to it when the form is submitted
async function submitBasicInfo(event) {
    event.preventDefault(); // Prevent form refreshing the page

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    // Simple validation (optional)
    if (!email.includes("@")) {
        alert("Please enter a valid email address.");
        return;
    }

    const data = {name, email, password};

    // try {
    //     const response = await fetch("sign_up_basic_info.php", {
    //         method: 'POST',
    //         headers: {
    //             "Content-Type": "application/json"
    //         },
    //         body: JSON.stringify(data)
    //     });

    //     if (!response.ok) {
    //         throw new Error(response.statusText);
    //     }

    //     const result = await response.json();

    //     if (result.status === "success") {
    //         localStorage.setItem("user_id", result.user_id);
    //         window.location.href = "sign_up_page_2.html";
    //     } else {
    //         alert(result.message);
    //     }

    // } catch (error) {
    //     alert('An error occurred: ' + error.message);
    // }
    try {
        const response = await fetch("sign_up_basic_info.php", {
            method: 'POST',
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
            //JSON.stringify() is a method that converts a JavaScript object into a JSON string
        });

        if (!response.ok) {
            throw new Error(response.statusText);
        }

        // Get the response as text first to check what is actually being returned
        const responseText = await response.text();

        try {
            // Try parsing the text as JSON
            const result = JSON.parse(responseText);

            if (result.status === "success") {
                localStorage.setItem("user_id", result.user_id);
                window.location.href = "sign_up_page_2.html";
            } else {
                alert(result.message);
            }
        } catch (jsonError) {
            // If parsing JSON fails, log or alert the raw response
            console.error("Response is not valid JSON:", responseText);
            alert('Received an invalid JSON response: ' + responseText);
        }

    } catch (error) {
        alert('An error occurred: ' + error.message);
    }

}
