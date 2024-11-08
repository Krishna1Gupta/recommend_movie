// Define the async function separately
async function handleLogin(event) {
    event.preventDefault(); // Prevent default form submission
  
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
  
    try {
      // Send the login request to the PHP API
      const response = await fetch('login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          email: email,
          password: password
        })
      });
  
      const data = await response.json();
  
      if (data.status === 'success') {
        // Redirect to dashboard if login is successful
        window.location.href = 'dashboard.html';
      }
      else if (data.redirect) {
        // If the response contains a redirect (from the server), handle it here
        window.location.href = data.redirect;}
      else {
        // Show error message if login fails
        document.getElementById('error-message').style.display = 'block';
      }
    } catch (error) {
      console.error('Error:', error);
    }
  }
  
  // Add event listener for the submit event
  document.getElementById('login-form').addEventListener('submit', handleLogin);
  
  // You can add more event listeners to the same element if needed
  // document.getElementById('another-element').addEventListener('click', anotherFunction);
  
  
// document.getElementById('login-form'):
// This part of the code selects an HTML element with the ID login-form.
// document is the global object representing the web page, and getElementById is a method that retrieves an element based on its unique ID.

// .addEventListener('submit', ...):
// The addEventListener method is used to attach an event handler to the selected element (in this case, the login form).
// The first argument, 'submit', specifies the type of event to listen for. In this case, it listens for the submit event, which occurs when the form is submitted (usually when the user clicks a submit button).