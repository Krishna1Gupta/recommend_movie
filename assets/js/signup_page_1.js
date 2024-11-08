async function submitBasicInfo(event){
    event.preventDefault();

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    // Radio buttons are grouped by the name attribute, so we need to loop through the NodeList returned by getElementsByName() and check which one is selected (checked).
    const ageElements = document.getElementsByName('age');
    let adult;
    for(let i = 0;i < ageElements.length;i++){
        if(ageElements[i].checked){
            adult = ageElements[i].value;
            break;
        }
    }
    const genderElements  = document.getElementsByName('gender');
    let gender;
    for(let i = 0;i < genderElements.length;i++){
        if(genderElements[i].checked){
            gender = genderElements[i].value;
            break;
        }
    }


    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert("Please enter a valid email address");
        return;
    }
    

    const data ={name,email,password,adult,gender};
    console.log(data);
    console.log(JSON.stringify(data));


    try{
        const response = await fetch('http://localhost/recommend_movie/routes/signup_basic_info.php',{
            method : "POST",
            headers:{
                "Content-Type":"application/json"
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(response.statusText);
        }

        const responseText = await response.text();

        try {
            // Try parsing the text as JSON
            const result = JSON.parse(responseText);

            if (result.status === "success") {
                localStorage.setItem("user_id", result.user_id);
                window.location.href = "http://localhost/recommend_movie/views/signup_page_2.html";
            } 
            else {
                alert(result.message);
            }
        } 
        catch (jsonError) {
            // If parsing JSON fails, log or alert the raw response
            console.error("Response is not valid JSON:", responseText);
            alert('Received an invalid JSON response: ' + responseText);
        }

    }
    catch (error) {
        alert('An error occurred: ' + error.message);
    }

} 